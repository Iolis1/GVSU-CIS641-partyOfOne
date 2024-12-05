from flask import Flask, request, jsonify
from transformers import AutoModelForCausalLM, AutoTokenizer
import openai
import pymysql
import json
import zlib  # For compression/decompression
import base64

# Load configuration from config.json
with open("config.json") as config_file:
    config = json.load(config_file)

app = Flask(__name__)

# BioGPT setup
bio_tokenizer = AutoTokenizer.from_pretrained("microsoft/BioGPT")
bio_model = AutoModelForCausalLM.from_pretrained("microsoft/BioGPT")

# Set OpenAI API key
openai.api_key = config["openai_api_key"]

# MySQL database connection
db_config = {
    "host": config["db_host"],
    "user": config["db_user"],
    "password": config["db_password"],
    "database": config["db_name"]
}

def save_summaries_to_db(patient_id, data_type, data_id, bio_summary, simplified_summary):
    """
    Save summaries to the database.
    """
    connection = pymysql.connect(**db_config)
    try:
        with connection.cursor() as cursor:
            query = """
                INSERT INTO procedure_summaries (patient_id, data_type, data_id, bio_summary, simplified_summary)
                VALUES (%s, %s, %s, %s, %s)
            """
            # Debug: Log the query and values
            print(f"Executing Query: {query}")
            print(f"Values: Patient ID: {patient_id}, Data Type: {data_type}, Data ID: {data_id}")
            print(f"Bio Summary (First 100 chars): {bio_summary[:100]}")
            print(f"Simplified Summary (First 100 chars): {simplified_summary[:100]}")

            cursor.execute(query, (patient_id, data_type, data_id, bio_summary, simplified_summary))
            connection.commit()
    except pymysql.MySQLError as e:
        print(f"Database Error: {e}")
        raise
    finally:
        connection.close()

@app.route('/summarize', methods=['POST'])
def summarize_data():
    """
    Process and summarize data from OpenEMR.
    """
    try:
        data = request.get_json()
        print(f"Received Data: {data}")

        # Extract and validate input fields
        patient_id = data["patient_id"]
        data_type = data["data_type"]
        data_id = data["data_id"]

         # Decode and decompress data_content
        try:
            compressed_data = base64.b64decode(data["data_content"])
            data_content = zlib.decompress(compressed_data).decode()
            print("Successfully decompressed data_content")
        except (zlib.error, base64.binascii.Error) as e:
            print(f"Decompression or Decoding Error: {e}")
            data_content = data["data_content"]  # Fall back to plain text
            print("Falling back to plain text data_content")

        print(f"Data Content Length: {len(data_content)}")
    except KeyError as e:
        print(f"Missing Key: {e}")
        return {"error": f"Missing key {str(e)}"}, 400
    except Exception as e:
        print(f"Unexpected Error: {e}")
        return {"error": f"Unexpected error occurred: {str(e)}"}, 500

    if not patient_id or not data_type or not data_content or not data_id:
        return jsonify({"error": "Missing required fields: patient_id, data_type, data_content, or data_id"}), 400

    # Generate BioGPT summary
    try:
        bio_inputs = bio_tokenizer(data_content, return_tensors="pt")
        bio_max_tokens = min(150, len(data_content) // 4)  # Dynamic token adjustment
        bio_outputs = bio_model.generate(**bio_inputs, max_new_tokens=bio_max_tokens)
        bio_summary = bio_tokenizer.decode(bio_outputs[0], skip_special_tokens=True)
        print(f"BioGPT Summary: {bio_summary[:100]}")  # Log first 100 characters
    except Exception as e:
        print(f"BioGPT Error: {e}")
        return jsonify({"error": "BioGPT summarization failed"}), 500

    # Simplify summary with ChatGPT
    try:
        chat_response = openai.ChatCompletion.create(
            model="gpt-4o-mini",
            messages=[
                {"role": "system", "content": "You are a helpful assistant."},
                {"role": "user", "content": f"Explain the following medical summary to a 3rd-grade audience:\n{bio_summary}"}
            ],
            max_tokens=150
        )
        simplified_summary = chat_response.choices[0].message.content.strip()
        print(f"Simplified Summary: {simplified_summary[:100]}")  # Log first 100 characters
    except Exception as e:
        print(f"ChatGPT Error: {e}")
        return jsonify({"error": "ChatGPT summarization failed"}), 500

    # Save to database
    try:
        save_summaries_to_db(patient_id, data_type, data_id, bio_summary, simplified_summary)
    except Exception as e:
        print(f"Database Save Error: {e}")
        return jsonify({"error": "Failed to save summaries to database"}), 500

    return jsonify({
        "bio_summary": bio_summary,
        "simplified_summary": simplified_summary
    })

if __name__ == '__main__':
    app.run(port=5000)