Team name: Party of One?

Team members: Lupe Hernandez Jr.

# Introduction

## Project Description:
This project aims to leverage a Large Language Model (LLM) to simplify and present medical test results directly within Electronic Medical Records (EMR) systems. By harnessing the capabilities of LLMs, we can translate complex medical data into clear, easily understandable information for patients. The primary goal is to enhance patient comprehension, reduce anxiety, and improve engagement by providing simplified summaries of test results through the patient’s health portal.

## What is Data Simplification with LLMs?
Data simplification in this context refers to using LLMs to transform technical medical language into patient-friendly explanations. This involves:
1. **Automated Translation of Medical Terminology**: Using LLMs to convert complex medical terms and abbreviations into plain language that non-experts can understand.
2. **Contextualization**: Explaining the significance of the results in relation to the patient’s health and next steps.
3. **Personalization**: Tailoring the simplified explanations based on patient history, literacy level, and medical background.

## Domain:
This project lies at the intersection of healthcare and artificial intelligence. By integrating LLMs into EMR systems, I aim to improve patient understanding of their medical information directly within their health portals, contributing to better patient care and education.

## Targeted Users:
* **Primary User: Patients** – Patients will benefit from easily digestible explanations of their test results, empowering them to make informed health decisions.
* **Secondary Users:**
  * **Healthcare Providers (Physicians, Nurses)** – Providers will spend less time explaining test results, allowing them to focus on personalized care.
  * **Health Informatics Specialists** – Specialists who work with integrating AI tools into EMR systems to improve patient experience.
  * **Healthcare Administrators** – Administrators managing patient engagement will see improved satisfaction metrics as patients better understand their health information.

## Areas of Computing to Explore:
1. **Natural Language Processing (NLP)**: Using LLMs to automatically translate medical data into patient-friendly language, ensuring clarity and accuracy.
2. **Healthcare Data Integration**: Focusing on the integration of LLMs into existing EMR platforms while maintaining security and data privacy standards.
3. **Human-Computer Interaction (HCI)**: Developing an intuitive interface within the patient portal for delivering LLM-generated explanations.
4. **Machine Learning**: Exploring how LLMs can learn from patient feedback to continuously improve the accuracy and relevance of the simplified explanations.
5. **Usability Testing**: Conducting usability tests to ensure the LLM-generated explanations are clear and helpful to patients with varying levels of health literacy.

# Anticipated Technologies

* EMR System (openEMR)
* LLM (BioGPT or ClinicalBERT)
* VSCode
* I'm sure there's more that I'll add when I think of them.

# Method/Approach

1. Since I'm working on this project solo, I'll start with learning the required languages needed to deliver a complete project.
    * PHP and javascript are the languages used with openEMR.  
2. I'll have to test out medical results on the LLMs to see if it comes back with relevant information, or gibberish.
3. Come up with a rough sketch of the health portal after the information is simplified.
4. Review the code to understand what the program is already doing, and modify it to fit my needs.

# Estimated Timeline

(Figure out what your major milestones for this project will be, including how long you anticipate it *may* take to reach that point)
1. Phase 1 - Test the LLMs outside of the EMR system and use whichever one is trained to specific problem.
    * Aproximately 2 weeks
2. Phase 2 - Integrate the LLM into the OpenEMR system.
    * Aproximately 3 weeks
3. Phase 3 - Add in a verification feature for doctors to approve or decline the simplified statement and allow them to edit.
    * Aproximately 1 week
4. Phase 4 - Testing
    * Entire length of project.

# Anticipated Problems

* I'm not well versed in PHP or implementing LLM's into a system that is fully functioning, so it might not even work, or it might look terrible. 
* I'm not a design focused so the final layout of the EMR patient portal may be janky.  
* I might not be able to get access to medical records to accurately test the system.

Solutions to all these problems is to utilized my network to get some clarity when I run into a snag.