<?php
/*Recibimos el siguiente JSON:
{
    "sensor":1,
    "data":[
        {
            "timestamp":19299.94,
            "values": [14,5,6]
        }
    ]
}
Donde respondemos 
{
    "status":"ok",
    "valuesRecieved":150
}

*/
//nos aseguramos que sea un post request
if($_SERVER['REQUEST_METHOD'] != 'POST'){
    echo json_encode(array('status' => false, 'error' => 'Método de petición incorrecto'));
    exit;
}else{
    include_once __DIR__.'/auth_logic.php';
    //comprobamos que el authorization bearer sea un token correcto
    $headers = apache_request_headers();
    //si hay el authorization, lo cogemos
    if(isset($headers['authorization'])){
        $headers['Authorization'] = $headers['authorization'];
    }
    if(!isset($headers['Authorization'])){
        echo json_encode(array('status' => false, 'error' => 'Token no encontrado', 'headers' => json_encode($headers)));
        exit;
    }
    $token = $headers['Authorization'];
    $customer = token_ok($token);
    if($customer<0){
        echo json_encode(array('status' => false, 'error' => 'Token incorrecto','Token:'=>$token));
        exit;
    }
    //insertamos los datos en la base de datos
    include __DIR__.'/../config.php';
    $data = json_decode(file_get_contents('php://input'), true);
    //guardamos el json que nos llega
    $json = json_encode($data);
    //guardamos el json en un archivo
    $file = fopen('data.json', 'w');
    fwrite($file, $json);
    fclose($file);
    $sensor = $data['sensor'];
    $data = $data['data'];
    $valuesRecieved = 0;
    foreach($data as $d){
        $timestamp = $d['timestamp'];
        //convertimos el timestamp a un formato legible
        $timestamp = date('Y-m-d H:i:s', $timestamp);
        $values = $d['values'];
        $index = 0;
        foreach($values as $v){

            $sql = "INSERT INTO data (user, type, time, value, `index`) VALUES ('$customer', '$sensor', '$timestamp', '$v',$index)";
            
            $result = mysqli_query($link, $sql);
            if($result){
                $valuesRecieved++;
                $index++;
            }
        }
    }
    //devolvemos
    echo json_encode(array('status' => true, 'valuesRecieved' => $valuesRecieved));
}
?>