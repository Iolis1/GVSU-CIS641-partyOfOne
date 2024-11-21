<?php
require_once("../../globals.php");
require_once("$srcdir/sql.inc");

$procedure_id = $_GET['procedure_id'];
$procedure_data = sqlQuery("SELECT * FROM procedure_order WHERE procedure_order_id = ?", array($procedure_id));

// Fetch lab results from the `procedure_result` table
$lab_results = sqlStatement("SELECT * FROM procedure_result WHERE procedure_report_id = ?", array($procedure_id));

$lab_data = "";
while ($row = sqlFetchArray($lab_results)) {
    // Build a concatenated string of lab results
    $lab_data .= "Result: " . $row['result'] . " " . $row['units'] . " (" . $row['result_text'] . ")\n";
    $lab_data .= "Range: " . $row['range'] . "\n";
    if (!empty($row['abnormal'])) {
        $lab_data .= "Abnormal: " . $row['abnormal'] . "\n";
    }
    if (!empty($row['comments'])) {
        $lab_data .= "Comments: " . $row['comments'] . "\n";
    }
    $lab_data .= "\n"; // Separate each result
}

// Call the Python API
$api_url = "http://localhost:5000/summarize";
$payload = json_encode([
    "procedure_text" => $procedure_text,
    "patient_id" => $patient_id,
    "procedure_id" => $procedure_id
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

echo "<h2>BioGPT Summary:</h2>";
echo "<p>" . htmlspecialchars($result['bio_summary']) . "</p>";

echo "<h2>Simplified Summary:</h2>";
echo "<p>" . htmlspecialchars($result['simplified_summary']) . "</p>";
?>
