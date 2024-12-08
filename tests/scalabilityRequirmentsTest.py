import threading
import requests

def send_request():
    payload = {
        "patient_id": 8040,
        "data_type": "Test Result",
        "data_content": "Sample lab data",
        "data_id": "all_labs"
    }
    response = requests.post("http://localhost:5000/summarize", json=payload)
    print(response.status_code)

# Simulate 50 concurrent requests
threads = []
for i in range(50):
    t = threading.Thread(target=send_request)
    threads.append(t)
    t.start()

for t in threads:
    t.join()