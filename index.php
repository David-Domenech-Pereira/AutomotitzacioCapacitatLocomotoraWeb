<?php
include 'config.php';



?>
 <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<table>
    <thead>
        <th>User</th>
        <th>Start</th>
        <th>End</th>
        <th>Type</th>
    </thead>
    <tbody>
        <?php
        $sql = "SELECT * FROM test";
        $result = $link->query($sql);
        if($result){
            while($row = $result->fetch_assoc()){
                echo "<tr>";
                echo "<td>".$row["user"]."</td>";
                echo "<td>".$row["start"]."</td>";
                echo "<td>".$row["end"]."</td>";
                //el type lo cogemos de la tabla typesoftest
                $sql2 = "SELECT * FROM typesoftest WHERE id=".$row["type"];
                $result2 = $link->query($sql2);
                $row2 = $result2->fetch_assoc();
                echo "<td>".$row2["name"]."</td>";
                echo "</tr>";
            }
        }
        ?>
</table>
<h1>Dades d'avui</h1>
<b>Móvil</b>
<?php
//cogemos los timestamp de hoy a las 00:00 hasta las 23:59
$today = strtotime('today midnight');
$todayEnd = strtotime('tomorrow midnight')-1;
    $sql = "SELECT * FROM data WHERE user=1 AND time BETWEEN $today AND $todayEnd AND type=2 ORDER BY time ASC";
    $result = $link->query($sql);
    $data = array();
    $times = array();
    $i = 0;
    if($result){
        while($row = $result->fetch_assoc()){
            //convertimos un UTC timestamp a YYYY-MM-DD HH:MM:SS

            $data[$row["index"]][]= $row["value"];
            if($row["index"]==2){
                $times[] = date("H:i:s",$row["time"]);
                 $i++; //pasamos a la siguiente fila
            }
        }
    }

?>
<hr>

<canvas id="myChart" width="1500" height="1050"></canvas>
<script>
    // Sample data for x, y, z values over time
const data = {
    <?php
    echo "labels: ".json_encode($times).",";
    ?>
    
    datasets: [
        {
            label: 'X Value',
            <?php
            echo "data: ".json_encode($data[0]).",";
            ?>
           
            borderColor: 'red',
            fill: false
        },
        {
            label: 'Y Value',
            <?php
            echo "data: ".json_encode($data[1]).",";
            ?>
            borderColor: 'blue',
            fill: false
        },
        {
            label: 'Z Value',
            <?php
            echo "data: ".json_encode($data[2]).",";
            ?>
            borderColor: 'green',
            fill: false
        }
    ]
};

// Get the canvas element
const ctx = document.getElementById('myChart').getContext('2d');

// Create a line chart
const myChart = new Chart(ctx, {
    type: 'line',
    data: data,
    options: {
        responsive: false,
        maintainAspectRatio: false,
        scales: {
            x: {
                display: true,
                title: {
                    display: true,
                    text: 'Time'
                }
            },
            y: {
                display: true,
                title: {
                    display: true,
                    text: 'Value'
                }
            }
        }
    }
});

</script>
<hr>
<b>Arduino</b>
<?php
//cogemos los timestamp de hoy a las 00:00 hasta las 23:59
$today = strtotime('today midnight');
$todayEnd = strtotime('tomorrow midnight')-1;
    $sql = "SELECT * FROM data WHERE user=1 AND time BETWEEN $today AND $todayEnd AND type=1 ORDER BY time ASC";
    $result = $link->query($sql);
    $data = array();
    $times = array();
    $i = 0;
    if($result){
        while($row = $result->fetch_assoc()){
            //convertimos un UTC timestamp a YYYY-MM-DD HH:MM:SS

            $data[$row["index"]][]= $row["value"];
            if($row["index"]==2){
                //convertimos un UTC timestamp a YYYY-MM-DD HH:MM:SS

                $times[] = date("H:i:s",$row["time"]);
                 $i++; //pasamos a la siguiente fila
            }
        }
    }

?>
<hr>

<canvas id="myChart2" width="1500" height="1050"></canvas>
<script>
    // Sample data for x, y, z values over time
const data2 = {
    <?php
    echo "labels: ".json_encode($times).",";
    ?>
    
    datasets: [
        {
            label: 'X Value',
            <?php
            echo "data: ".json_encode($data[0]).",";
            ?>
           
            borderColor: 'red',
            fill: false
        },
        {
            label: 'Y Value',
            <?php
            echo "data: ".json_encode($data[1]).",";
            ?>
            borderColor: 'blue',
            fill: false
        },
        {
            label: 'Z Value',
            <?php
            echo "data: ".json_encode($data[2]).",";
            ?>
            borderColor: 'green',
            fill: false
        }
    ]
};

// Get the canvas element
const ctx2 = document.getElementById('myChart2').getContext('2d');

// Create a line chart
const myChart2 = new Chart(ctx2, {
    type: 'line',
    data: data2,
    options: {
        responsive: false,
        maintainAspectRatio: false,
        scales: {
            x: {
                display: true,
                title: {
                    display: true,
                    text: 'Time'
                }
            },
            y: {
                display: true,
                title: {
                    display: true,
                    text: 'Value'
                }
            }
        }
    }
});

</script>