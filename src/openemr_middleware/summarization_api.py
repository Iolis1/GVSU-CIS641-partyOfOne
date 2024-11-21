from flask import Flask, request, jsonify
from transformers import AutoModelForCausalLM, AutoTokenizer
import openai
import pymysql
import json

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

def save_summaries_to_db(patient_id, procedure_id, bio_summary, simplified_summary):
    connection = pymysql.connect(**db_config)
    try:
        with connection.cursor() as cursor:
            query = """
                INSERT INTO procedure_summaries (patient_id, procedure_id, bio_summary, simplified_summary)
                VALUES (%s, %s, %s, %s)
            """
            cursor.execute(query, (patient_id, procedure_id, bio_summary, simplified_summary))
            connection.commit()
    finally:
        connection.close()

@app.route('/summarize', methods=['POST'])
def summarize_procedure():
    data = request.json
    procedure_text = data.get("procedure_text")
    patient_id = data.get("patient_id")
    procedure_id = data.get("procedure_id")
    
    # Generate BioGPT summary
    bio_inputs = bio_tokenizer(procedure_text, return_tensors="pt")
    bio_outputs = bio_model.generate(**bio_inputs)
    bio_summary = bio_tokenizer.decode(bio_outputs[0], skip_special_tokens=True)
    
    # Simplify summary with ChatGPT
    chat_prompt = f"Explain the following medical summary to a 3rd-grade audience:\n{bio_summary}"
    chat_response = openai.Completion.create(
        engine="text-davinci-003",
        prompt=chat_prompt,
        max_tokens=150
    )
    simplified_summary = chat_response["choices"][0]["text"].strip()
    
    # Save to database
    save_summaries_to_db(patient_id, procedure_id, bio_summary, simplified_summary)
    
    return jsonify({
        "bio_summary": bio_summary,
        "simplified_summary": simplified_summary
    })

if __name__ == '__main__':
    app.run(port=5000)