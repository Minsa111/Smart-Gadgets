<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real-time Temperature Chart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
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
    <h1>Real-time Temperature Chart</h1>
    <?php
// API endpoint to get temperature data
        $result = $conn->query("SELECT * FROM temperature ORDER BY id DESC LIMIT 10");
        while ($row = $result->fetch_assoc()) {
            $resultArray[] = array(
                'x' => $row['timestamp'],
                'y' => $row['temperature']
            );
        }
    ?>
    <div class="container">
        <div class = "row">
            <div class = "col">
                <div class = "chartBox">
                <canvas id="temperatureChart"></canvas>
                </div>
            </div>
            
            <div class = "col">
                <div class="container">
                    <div class="row">
                    <div class="col-6 col-sm-4 p-3 mb-2 bg-primary text-white text-center"> <p>Status</p> </div>
                        <div class="w-100 d-none d-md-block"></div>
                        <div class="col-6 col-sm-4 p-3 mb-2 bg-dark text-white text-center">
                            <script>
                                const updateStatus = () => {
                                    $.ajax({
                                        url: 'updatestatus.php',
                                        method: 'GET',
                                        dataType: 'json',
                                        success: function (newStatus) {
                                            $('#statusElement').text(newStatus);
                                        },
                                        error: function (error) {
                                            console.error('Error fetching status:', error);
                                        }
                                    });
                                };

                                // Call updateStatus initially and then set it to run every 3000 milliseconds
                                
                            </script>
                            <p><span id="statusElement">Loading...</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js/dist/Chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script><!-- Include jQuery from a CDN -->


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
                max:10,
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
    
    const updateData = () => {
    $.ajax({
        url: 'updateData.php',
        method: 'GET',
        dataType: 'json',
        success: function (newData) {
            const tempaddData = newData[0].x;

            // Check if tempaddData is different from the latest x value in temperatureData
            const tempTemperatureData = temperatureData.length > 0 ? temperatureData[temperatureData.length - 1].x : null;
            console.log(tempTemperatureData)
            console.log(newData[0])

            if (tempTemperatureData !== tempaddData) {
                // Convert the time string to a full timestamp (modify as needed)

                temperatureData.push({
                    x: tempaddData,
                    y: newData[0].y // Assuming y is a string, convert it to an integer
                });

                if (temperatureData.length > 10) {
                    temperatureData.shift(); // Remove the oldest data point
                }

                temperatureChart.data.labels = temperatureData.map(dataPoint => dataPoint.x);
                temperatureChart.data.datasets[0].data = temperatureData.map(dataPoint => ({
                    x: new Date(dataPoint.x),
                    y: dataPoint.y
                }));

                temperatureChart.update();
                console.log("Data updated");
            }

            console.log("Last Data:", newData[0].y);
            console.log("New Data:", temperatureData);
        },
        error: function (error) {
            console.error('Error fetching data:', error);
        }
    });
    };
setInterval(updateData, 3000);
updateStatus();
setInterval(updateStatus, 3000);

</script>
</body>
</html>