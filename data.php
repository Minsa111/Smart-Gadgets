<?php
    $host = 'localhost';
    $username  = 'root';
    $password = '';
    $db = 'latihan';

    $koneksi = mysqli_connect($host, $username, $password, $db);

    if (!$koneksi) {
        echo "Gagal melakukan koneksi ke MYSQL: " . mysqli_connect_error();
    }
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the keys are set in the POST request
    if (isset($_POST["temperature"])  && isset($_POST["state"])) {
        $temperature = $_POST["temperature"];
        $state = $_POST["state"];

        // Ensure values are not null before inserting into the database
        if ($temperature !== null && $state !== null) {
            $stmt = $koneksi->prepare("INSERT INTO mpu (temperature, state) VALUES (?,  ?)");
            $stmt->bind_param("ds", $temperature, $state);

            try {
                $stmt->execute();
                echo "Data inserted successfully";
            } catch (mysqli_sql_exception $e) {
                echo "Error: " . $e->getMessage();
            }

            $stmt->close();
        } else {
            echo "Invalid data received";
        }
    } else {
        echo "Invalid POST data";
    }
}

$koneksi->close();
?>
