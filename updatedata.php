<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tubes";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// API endpoint to get temperature data
$result = $conn->query("SELECT * FROM temperature ORDER BY timestamp DESC LIMIT 10");
$data = [];

while ($row = $result->fetch_assoc()) {
    // Format the timestamp as a string
    $row['timestamp'] = $row['timestamp'];
    
    $data[] = $row;
}

// Close the database connection
$conn->close();

// Set the content type to JSON
header('Content-Type: application/json');

// Encode the data as JSON and echo it
echo json_encode($data);
?>
