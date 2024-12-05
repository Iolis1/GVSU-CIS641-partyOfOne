<?php
require_once("../../globals.php");
require_once("$srcdir/sql.inc.php");
error_log("Full GET Request: " . json_encode($_GET));

// Get result_codes and procedure_order_id from query parameters
$result_codes = $_GET['procedure_ids']; // Expecting an array of result codes
$procedure_order_id = $_GET['procedure_order_id']; // Expecting a single procedure_order_id

if (!is_array($result_codes) || empty($result_codes) || empty($procedure_order_id)) {
    echo "<h2>Error:</h2><p>Missing required parameters: procedure IDs or procedure_order_id.</p>";
    exit;
}

// Initialize variables
$data_type = "Test Result"; // We're working with lab results
$data_content = "";
$patient_id = null;

// Fetch the patient ID from the procedure_order table
$procedure_data = sqlQuery("SELECT * FROM procedure_order WHERE procedure_order_id = ?", array($procedure_order_id));
if ($procedure_data) {
    $patient_id = $procedure_data['patient_id'];
} else {
    echo "<h2>Error:</h2><p>Invalid procedure_order_id: {$procedure_order_id}.</p>";
    exit;
}

// Loop through each result code and fetch its data for the specific procedure_order_id
foreach ($result_codes as $result_code) {
    // Query the procedure_result table using result_code and procedure_report_id
    $lab_results = sqlStatement("
        SELECT pr.* 
        FROM procedure_result pr
        JOIN procedure_report rpt ON pr.procedure_report_id = rpt.procedure_report_id
        WHERE pr.result_code = ? AND rpt.procedure_order_id = ?
    ", array($result_code, $procedure_order_id));

    $row_count = 0;

    while ($row = sqlFetchArray($lab_results)) {
        $row_count++;
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

    if ($row_count === 0) {
        error_log("No results found for result_code: " . $result_code . " and procedure_order_id: " . $procedure_order_id);
    }
}

// If no data content is built, return an error
if (empty($data_content)) {
    echo "<h2>Error:</h2><p>No valid data found for the selected result codes and procedure_order_id.</p>";
    exit;
}

// Log the determined data type
error_log("Data Type: " . $data_type);

// Call the Python API
$api_url = "http://localhost:5000/summarize";
$payload = json_encode([
    "patient_id" => $patient_id,
    "data_type" => $data_type,
    "data_content" => $data_content,
    "data_id" => implode(",", $result_codes)
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

if (isset($result['bio_summary']) && isset($result['simplified_summary'])) {
    echo "<h2>BioGPT Summary:</h2>";
    echo "<p>" . htmlspecialchars($result['bio_summary']) . "</p>";

    echo "<h2>Simplified Summary:</h2>";
    echo "<p>" . htmlspecialchars($result['simplified_summary']) . "</p>";
} else {
    echo "<h2>Error:</h2><p>Failed to get a valid response from the middleware.</p>";
}
?>
