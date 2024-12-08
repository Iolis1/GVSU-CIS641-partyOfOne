from locust import HttpUser, task

class SummarizationTestUser(HttpUser):
    @task
    def test_summarization_request(self):
        payload = {
            "patient_id": 8040,
            "data_type": "Test Result",
            "data_content": "Sample lab data",
            "data_id": "all_labs"
        }
        self.client.post("/summarize", json=payload)
