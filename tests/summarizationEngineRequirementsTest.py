import requests
from unittest.mock import patch

@patch('requests.post')
def test_summarization_api(mock_post):
    # Mock the API response
    mock_post.return_value.json.return_value = {
        "bio_summary": "Mocked BioGPT Summary",
        "simplified_summary": "Mocked Simplified Summary"
    }
    payload = {
        "patient_id": 8040,
        "data_type": "Test Result",
        "data_content": "Sample lab data",
        "data_id": "all_labs"
    }
    response = requests.post("http://localhost:5000/summarize", json=payload)
    data = response.json()
    assert data["bio_summary"] == "Mocked BioGPT Summary"
    assert data["simplified_summary"] == "Mocked Simplified Summary"