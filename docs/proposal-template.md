Team name: Party of One?

Team members: Lupe Hernandez Jr.

# Introduction

## Project Description:
The project focuses on developing a method to simplify and present medical test results in an easily understandable way for patients. The goal is to bridge the gap between complex medical data and patient comprehension, ensuring patients can make informed decisions about their health without needing extensive medical knowledge. The project aims to empower patients, reduce anxiety, and improve overall patient engagement and satisfaction by delivering simplified summaries of test results directly to patients through their health portals.

## What is Data Simplification?
Data simplification in this context refers to transforming complex, jargon-heavy medical test results into clear, concise, and easily digestible information. This involves:
1.	Translation of Medical Terminology: Converting technical medical terms and abbreviations into plain language that individuals without medical training can easily understand.
2.	Contextualization: Explaining the results' meaning regarding the patient's health, potential implications, and next steps provides context.
3.	Visualization: Using visual aids like charts, graphs, and infographics to help patients grasp numerical data and trends at a glance.
4.	Personalization: Tailoring the simplified information to the patient's health history, literacy level, and specific needs.

## Domain:
This project's domain lies at the intersection of healthcare, patient education, and health informatics. It involves using Electronic Health Records (EHR) systems, patient portals, and data visualization tools to improve patient experience and health literacy.

## Targeted Users:
* Primary User: Patients - The main focus is on patients, particularly those with limited medical knowledge who need more precise explanations of their test results to make informed decisions about their health.
* Secondary Users:
    * Healthcare Providers (Physicians, Nurses): While the tool is designed for patients, it will also benefit healthcare providers by reducing the time they spend explaining test results, allowing them to focus on patient care.
    * Healthcare Administrators: Individuals who manage patient engagement and satisfaction within healthcare institutions.
    * Health Educators: Professionals responsible for patient education who can use the simplified data as teaching tools.
    * Health Informatics Specialists: These specialists may be involved in integrating and maintaining the system within EHR platforms.

## Areas of Computing to Explore:
1.	Natural Language Processing (NLP): Explore NLP techniques to automatically translate medical jargon into plain language.
2.	Data Visualization: Develop tools that convert complex medical data into easy-to-understand visual formats, like graphs and infographics.
3.	Human-Computer Interaction (HCI): Focus on designing an intuitive user interface that enhances patient experience and accessibility.
4.	Machine Learning: Investigate the use of machine learning algorithms to tailor the presentation of data based on individual patient characteristics and preferences.
5.	Healthcare Data Integration: Explore how to effectively integrate this tool into existing EHR systems while ensuring data privacy and security.
6.	Usability Testing: Conduct usability testing with patients to ensure the simplified data is more understandable and helpful.

# Anticipated Technologies

* EMR System (openEMR)
* LLM (BioGPT or ClinicalBERT)
* VSCode
* I'm sure there's more that I'll add shen I think of them.

# Method/Approach

1. Since I'm working on this project solo, I'll start with learning the required languages needed to deliver a complete project.
    * PHP and javascript are the languages used with openEMR.  
2. I'll have to test out medical results on the LLMs to see if it comes back with relevant information, or gibberish.
3. Come up with a rough sketch of the health portal after the information is simplified.
4. Review the code to understand what the program is already doing, and modify it to fit my needs.

# Estimated Timeline

(Figure out what your major milestones for this project will be, including how long you anticipate it *may* take to reach that point)
1. Test the LLMs outside of the EMR system and use whichever one is trained to spacific problem.
2. Integrate the LLM into the OpenEMR system.
3. Add in a verification feature for doctors to approve or decline the simplified statement and allow them to edit.
4. 

# Anticipated Problems

* I'm not well versed in PHP or implementing LLM's into a system that is fully functioning, so it might not even work, or it might look terrible. 
* I'm not a design focused so the final layout of the EMR patient portal may be janky.  
* I might not be able to get access to medical records to accurately test the system.


Solutions to all these problems is to utilized my network to get some clarity when I run into a snag.