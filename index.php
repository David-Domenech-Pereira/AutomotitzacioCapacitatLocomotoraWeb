<?php
include 'config.php';



?>
 <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <?php
        $sql = "SELECT * FROM test";
        $result = $link->query($sql);
        if($result){
            while($row = $result->fetch_assoc()){
               
                echo "<p>Usuari:".$row["user"]."<p>";
                echo "<p>Inici: ".$row["start"]. "</p><p>Final: ".$row["end"]."</p>";

                //el type lo cogemos de la tabla typesoftest
                $sql2 = "SELECT * FROM typesoftest WHERE id=".$row["type"];
                $result2 = $link->query($sql2);
                $row2 = $result2->fetch_assoc();
                echo "<p>".$row2["name"]."</p>";
                //pasamos start y end a timestamp
                $row["start"] = strtotime($row["start"]);
              
                $row["end"] = strtotime($row["end"]);
                //al end le añadimos 30 segundos para que se vea mejor
                if($row["type"]==2){
                $row["end"] += 30;
                }
                show_graph_dates($row["start"],$row["end"],2,$row["user"],false);
                echo "<hr>";
            }
        }
        ?>

<h1>Dades d'avui</h1>
<?php
$sql = "SELECT * FROM typesofdata";
$result = $link->query($sql);
if($result){
    while($row = $result->fetch_assoc()){
        echo "<h2>".$row["name"]."</h2>";
        //cogemos los timestamp de hoy a las 00:00 hasta las 23:59
$today = strtotime('today midnight');
$todayEnd = strtotime('tomorrow midnight')-1;
        show_graph_dates($today,$todayEnd,$row["id"]);
    }
}


function show_graph_dates($start,$end,$type,$user = 1,$avg=true){
    include 'config.php';
    $sql = "SELECT * FROM data WHERE user=$user AND time > $start AND time < $end AND type=$type ORDER BY time ASC";

    $result = $link->query($sql);
    $data = array();
    $times = array();
    $arr = array();
    $i = 0;
    if($result){
        $prev_minuto = 0;

        while($row = $result->fetch_assoc()){
            //convertimos un UTC timestamp a YYYY-MM-DD HH:MM:SS
            //lo ideal es coger el promedio de los 3 valores de este minuto
            //si no, coger el primero
            //si no, coger el último
            $minuto = date("H:i:s",round($row["time"],0));
           if($avg){
            if($minuto!=$prev_minuto){
                //hacemos el promedio de los valores de este minuto y los guardamos en el array $data
                if(isset($arr["index"][$prev_minuto])){
                    $data[0][] = $arr["value"][$prev_minuto][0]/$arr["index"][$prev_minuto];
                    $data[1][] = $arr["value"][$prev_minuto][1]/$arr["index"][$prev_minuto];
                    $data[2][] = $arr["value"][$prev_minuto][2]/$arr["index"][$prev_minuto];
                }
                //para guardarlo hay que sumarle 2 horas
                $date = new DateTime($minuto);

                $times[] = $date->format('H:i:s');
                $prev_minuto = $minuto;
            }

            if(!isset($row["index"][$minuto])){
                $arr["index"][$minuto] = 1;
                $arr["value"][$minuto][$row["index"]] = $row["value"];
            }else{
                $arr["index"][$minuto]++;
                $arr["value"][$minuto][$row["index"]] += $row["value"];
            }
        }else{
            //no hacemos el promedio
            if(!isset($row["index"][$minuto])){
                $arr["index"][$minuto] = 1;
                $arr["value"][$minuto][$row["index"]] = $row["value"];
            }else{
                $arr["index"][$minuto]++;
                $arr["value"][$minuto][$row["index"]] += $row["value"];
            }
           if($row["index"] == 2){
            $data[0][] = $arr["value"][$minuto][0];
            $data[1][] = $arr["value"][$minuto][1];
            $data[2][] = $arr["value"][$minuto][2];
            $date = new DateTime($minuto);

            $times[] = $date->format('H:i:s');
            $prev_minuto = $minuto;
           }
          
        }
        }
        if(isset($arr["index"][$prev_minuto])){
            $data[0][] = $arr["value"][$prev_minuto][0]/$arr["index"][$prev_minuto];
            $data[1][] = $arr["value"][$prev_minuto][1]/$arr["index"][$prev_minuto];
            $data[2][] = $arr["value"][$prev_minuto][2]/$arr["index"][$prev_minuto];
        }
        if(isset($minuto)){
        //para guardarlo hay que sumarle 2 horas
        $date = new DateTime($minuto);

        $times[] = $date->format('H:i:s');
        }
    }
    if(isset($minuto)){
$id = rand(0,100000) % 1000000;
echo "


<canvas id=\"myChart_$id\" width=\"800\" height=\"500\"></canvas>
<script>
    // Sample data for x, y, z values over time
const data_$id = {";

    echo "labels: ".json_encode($times).",
    
    datasets: [
        {
            label: 'X Value',
            ";
            echo "data: ".json_encode($data[0]).",
            
           
            borderColor: 'red',
            fill: false
        },
        {
            label: 'Y Value',
            ";
            echo "data: ".json_encode($data[1]).",
            
            borderColor: 'blue',
            fill: false
        },
        {
            label: 'Z Value',
            ";
            echo "data: ".json_encode($data[2]).",
            borderColor: 'green',
            fill: false
        }
    ]
};

// Get the canvas element
const ctx_$id = document.getElementById('myChart_$id').getContext('2d');

// Create a line chart
const myChart_$id = new Chart(ctx_$id, {
    type: 'line',
    data: data_$id,
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

</script>";
    }
}
?>
<hr>
