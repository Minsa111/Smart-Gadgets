<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tubes";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real-time Temperature Chart</title>
    <style type = "text/css">
        .chartBox {
            width: 700px;
        }
        body{
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <h1>Real-time Temperature Chart</h1>
    <?php
// API endpoint to get temperature data
        $result = $conn->query("SELECT * FROM temperature ORDER BY timestamp DESC LIMIT 10");
        $data = [];

        while ($row = $result->fetch_assoc()) {
            $resultArray[] = array(
                'x' => $row['timestamp'],
                'y' => $row['temperature']
            );
        }
        $conn->close();
    ?>
    <div class = "chartBox">
        <canvas id="temperatureChart"></canvas>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js/dist/Chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>

<script>
    const temperatureData = <?php echo json_encode($resultArray); ?>;
    temperatureData.reverse();
    const data = {
    labels: temperatureData.map(dataPoint => dataPoint.x),
    datasets: [{
        label: 'Temperature (°C)',
        borderColor: 'rgb(75, 192, 192)',
        data: temperatureData.map(dataPoint => ({
            x: new Date(dataPoint.x),  // Assuming timestamps are in milliseconds
            y: dataPoint.y
        })),
    }]
};

    const config = {
        type:'line',
        scales: {
            x: {
                type: 'time',
                time: {
                    unit: 'second' // Adjust the unit as needed (e.g., 'minute', 'hour', 'day')
                },
                title: {
                    display: true,
                    text: 'Timestamp'
                }
            },
            y: {
                min: 0,
                max: 100
            }
        },
};

    const temperatureChart = new Chart(
        document.getElementById('temperatureChart'),
        {
            type: 'line',
            data: data,
            options: config,
        }
    );
</script>


</body>
</html>




