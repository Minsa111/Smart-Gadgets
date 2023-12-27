<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tubes";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM temperature ORDER BY id DESC LIMIT 10");
while ($row = $result->fetch_assoc()) {
    $resultArray[] = array(
        'x' => $row['timestamp'],
        'y' => $row['temperature']
    );
}

echo json_encode($resultArray);
?>
