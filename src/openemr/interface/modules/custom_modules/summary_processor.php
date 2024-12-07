<?php
require_once("../../globals.php");
require_once("$srcdir/sql.inc.php");

// Get `patient_id` from query parameters
$patient_id = isset($_GET['patient_id']) ? $_GET['patient_id'] : null;

if (!$patient_id) {
    echo "<h2>Error:</h2><p>Missing patient ID.</p>";
    exit;
}

// Initialize variables
$data_content = "";
$data_type = "Test Result"; // Default data type

// Fetch all lab results for the patient
$query = "
    SELECT pr.*, po.patient_id
    FROM procedure_result AS pr
    JOIN procedure_report AS prr ON pr.procedure_report_id = prr.procedure_report_id
    JOIN procedure_order AS po ON prr.procedure_order_id = po.procedure_order_id
    WHERE po.patient_id = ?
";
$lab_results = sqlStatement($query, array($patient_id));

// Concatenate all lab results into data content
while ($row = sqlFetchArray($lab_results)) {
    $data_content .= "Result: " . $row['result'] . " " . $row['units'] . " (" . $row['result_text'] . ")\n";
    $data_content .= "Range: " . $row['range'] . "\n";
    if (!empty($row['abnormal'])) {
        $data_content .= "Abnormal: " . $row['abnormal'] . "\n";
    }
    if (!empty($row['comments'])) {
        $data_content .= "Comments: " . $row['comments'] . "\n";
    }
    $data_content .= "\n"; // Separate each result
}

// If no data content is built, return an error
if (empty($data_content)) {
    echo "<h2>Error:</h2><p>No lab results found for this patient.</p>";
    exit;
}

// Debugging: Log the data content being sent to the middleware
error_log("Data Content: " . $data_content);

// Prepare payload for middleware
$api_url = "http://localhost:5000/summarize";
$payload = json_encode([
    "patient_id" => $patient_id,
    "data_type" => $data_type,
    "data_content" => $data_content,
    "data_id" => "all_labs" // Identifier for all labs
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo "<h2>Error:</h2><p>Failed to connect to middleware API: " . curl_error($ch) . "</p>";
    curl_close($ch);
    exit;
}
curl_close($ch);

// Decode the response from the middleware
$result = json_decode($response, true);

// Display the results
if (isset($result['bio_summary']) && isset($result['simplified_summary'])) {
    $bio_summary = $result['bio_summary'];
    $simplified_summary = $result['simplified_summary'];

    // Display the summary and form for editing/approval
    echo "<h2>Lab Test Summary:</h2>";
    echo "<form method='post' action=''>
        <textarea name='edited_summary' rows='10' cols='80'>" . htmlspecialchars($simplified_summary) . "</textarea><br>
        <input type='hidden' name='patient_id' value='" . htmlspecialchars($patient_id) . "'>
        <input type='hidden' name='bio_summary' value='" . htmlspecialchars($bio_summary) . "'>
        <button type='submit' name='approve' value='1'>Approve Summary</button>
        <button type='submit' name='edit' value='1'>Save Edited Summary</button>
    </form>";
} else {
    echo "<h2>Error:</h2><p>Failed to get a valid response from the middleware.</p>";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $edited_summary = $_POST['edited_summary'] ?? null;
    $patient_id = $_POST['patient_id'] ?? null;
    $bio_summary = $_POST['bio_summary'] ?? null;

    if (isset($_POST['approve']) && $edited_summary) {
        // Mark the summary as approved and save the edited summary
        $query = "
            INSERT INTO procedure_summaries (patient_id, data_type, data_id, bio_summary, simplified_summary, updated_summary, approved)
            VALUES (?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                updated_summary = VALUES(updated_summary), 
                approved = VALUES(approved), 
                simplified_summary = VALUES(simplified_summary),
                bio_summary = VALUES(bio_summary)
        ";
        sqlStatement(
            $query,
            array(
                $patient_id,
                $data_type,
                "all_labs",
                $bio_summary,
                $simplified_summary,
                $edited_summary,
                1 // Approved
            )
        );
        echo "<p>Summary approved and saved successfully!</p>";
    } elseif (isset($_POST['edit']) && $edited_summary) {
        // Save only the edited summary
        $query = "
            INSERT INTO procedure_summaries (patient_id, data_type, data_id, bio_summary, simplified_summary, updated_summary, approved)
            VALUES (?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                updated_summary = VALUES(updated_summary), 
                approved = 0
        ";
        sqlStatement(
            $query,
            array(
                $patient_id,
                $data_type,
                "all_labs",
                $bio_summary,
                $simplified_summary,
                $edited_summary,
                0 // Not approved yet
            )
        );
        echo "<p>Edited summary saved successfully!</p>";
    } else {
        echo "<p>Error: No summary data provided.</p>";
    }
}
?>