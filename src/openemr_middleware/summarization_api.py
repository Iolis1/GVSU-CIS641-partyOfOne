from flask import Flask, request, jsonify
import openai
import pymysql
import json

# Load configuration from config.json
with open("config.json") as config_file:
    config = json.load(config_file)

app = Flask(__name__)

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
    Process and summarize data using ChatGPT.
    """
    try:
        data = request.get_json()
        print(f"Received Data: {data}")

        # Extract and validate input fields
        patient_id = data["patient_id"]
        data_type = data["data_type"]
        data_id = data["data_id"]
        data_content = data["data_content"]
    except KeyError as e:
        print(f"Missing Key: {e}")
        return {"error": f"Missing key {str(e)}"}, 400
    except Exception as e:
        print(f"Unexpected Error: {e}")
        return {"error": f"Unexpected error occurred: {str(e)}"}, 500

    if not patient_id or not data_type or not data_content or not data_id:
        return jsonify({"error": "Missing required fields: patient_id, data_type, data_content, or data_id"}), 400

    # Generate bio_summary using ChatGPT
    try:
        chat_response = openai.chat.completions.create(
            model="gpt-4o-mini",
            messages=[
                {"role": "system", "content": "You are a highly knowledgeable medical professional."},
                {"role": "user", "content": f"Provide a professional medical summary of the following test results using appropriate medical terminology and clinical language:\n\n{data_content}"}
            ]
        )
        bio_summary = chat_response.choices[0].message.content.strip()
        print(f"Bio Summary: {bio_summary[:100]}")  # Log first 100 characters
    except Exception as e:
        print(f"ChatGPT Error (bio_summary): {e}")
        return jsonify({"error": "ChatGPT summarization for bio_summary failed"}), 500

    # Generate simplified summary using ChatGPT
    try:
        chat_response = openai.chat.completions.create(
            model="gpt-4o-mini",
            messages=[
                {"role": "system", "content": "You are a helpful medical professional."},
                {"role": "user", "content": f"Explain the following medical summary as if you were a doctor talking to a patient with a third-grade reading level following the flesch kincaid method. Use simple, easy-to-understand language, avoiding medical jargon or acronyms, and condense the information into 1 paragraph or less, ensure not to use phrases like everything looks good and the like, instead simply present the information and for further questions contact your medical service provider:\n\n{bio_summary}"}
            ]
        )
        simplified_summary = chat_response.choices[0].message.content.strip()
        print(f"Simplified Summary: {simplified_summary[:100]}")  # Log first 100 characters
    except Exception as e:
        print(f"ChatGPT Error (simplified_summary): {e}")
        return jsonify({"error": "ChatGPT summarization for simplified_summary failed"}), 500

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
