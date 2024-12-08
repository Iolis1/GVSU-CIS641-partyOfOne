from selenium import webdriver
from selenium.webdriver.common.by import By

# Initialize the browser
driver = webdriver.Chrome()

# Test: Ensure the "View Lab Summaries" button navigates correctly
def test_view_lab_summaries_button():
    driver.get("http://localhost/openemr")
    view_button = driver.find_element(By.LINK_TEXT, "View All Lab Summaries")
    view_button.click()
    assert "summary_processor.php" in driver.current_url

# Test: Ensure the "Approve Summary" button works
def test_approve_summary():
    driver.get("http://localhost/openemr/interface/modules/custom_modules/summary_processor.php?patient_id=8040")
    approve_button = driver.find_element(By.NAME, "approve")
    approve_button.click()
    # Check for a success message
    success_message = driver.find_element(By.ID, "successMessage")
    assert "Summary approved" in success_message.text

# Close the browser
driver.quit()