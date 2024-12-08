def test_integration_with_openemr():
    response = requests.get("http://localhost/openemr/api/summaries/8040")
    assert response.status_code == 200
    assert "simplified_summary" in response.json()