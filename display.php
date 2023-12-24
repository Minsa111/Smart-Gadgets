<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real-time Temperature Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h1>Real-time Temperature Chart</h1>
    <canvas id="temperatureChart" width="400" height="200"></canvas>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var ctx = document.getElementById('temperatureChart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Temperature (°C)',
                    borderColor: 'rgb(75, 192, 192)',
                    data: [],
                    fill: false,
                }]
            },
            options: {
                responsive: false,
                maintainAspectRatio: false,
                width: 400,
                height: 200,
                scales: {
                    x: {
                        type: 'linear',
                        position: 'bottom'
                    },
                    y: {
                        min: 0,
                        max: 100
                    }
                }
            }
        });

        function updateChart() {
            fetch('updatedata.php')
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        // Extract timestamps and temperatures
                        var timestamps = data.map(entry => new Date(entry.timestamp).toLocaleTimeString());
                        var temperatures = data.map(entry => entry.temperature);

                        // Update chart data
                        chart.data.labels = timestamps;
                        chart.data.datasets[0].data = temperatures;

                        // Update the chart
                        chart.update();
                    } else {
                        console.error('No data received from the server.');
                    }
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                });
        }

        // Initial chart update
        updateChart();

        // Set interval to update the chart every 3 seconds
        setInterval(updateChart, 3000);
    });
    </script>

</body>
</html>

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
    $data[] = [
        'timestamp' => $row['timestamp'],
        'temperature' => $row['temperature'],
    ];
}

echo json_encode($data);

$conn->close();
?>
