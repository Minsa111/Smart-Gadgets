<?php
include "koneksi.php";

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the latest data from the database
$result = $conn->query("SELECT * FROM temperature ORDER BY id DESC LIMIT 1");

$data = array();

while ($row = $result->fetch_assoc()) {
    $data[] = array(
        'x' => $row['timestamp'],
        'y' => $row['temperature']
    );
}

// Close the database connection
$conn->close();

// Return the data as JSON with the appropriate Content-Type header
header('Content-Type: application/json');
echo json_encode($data);
?>
