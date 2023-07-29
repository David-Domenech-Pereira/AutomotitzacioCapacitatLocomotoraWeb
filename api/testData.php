<?php
//nos mandan un GET request que contiene en la URL testType, Start, End
/*devolvemos
{
    "status":"ok",
}
*/
//nos aseguramos que sea un get request
if($_SERVER['REQUEST_METHOD'] != 'GET'){
    echo json_encode(array('status' => false));
    exit;
}else{
    //miramos que el token sea correcto
    include_once __DIR__.'/auth_logic.php';
    $headers = apache_request_headers();
    if(!isset($headers['Authorization'])){
        echo json_encode(array('status' => false));
        exit;
    }
    $token = $headers['Authorization'];
    $user = token_ok($token);
    if($user<0){
        echo json_encode(array('status' => false, 'error' => 'Token incorrecto'));
        exit;
    }
    //miramos que estén los parámetros necesarios
    if(!isset($_GET['testType']) || !isset($_GET['start']) || !isset($_GET['end'])){
        echo json_encode(array('status' => false, 'error' => 'Faltan parámetros'));
        exit;
    }
    //insertamos el test en la base de datos
    include __DIR__.'/../config.php';
    //nos aseguramos que end sea mayor que start
    if($_GET['end']<$_GET['start']){
        echo json_encode(array('status' => false, 'error' => 'End debe ser mayor que start'));
        exit;
    }
    //los start y end los ponemos en formao YYYY-MM-DD HH:MM:SS
    $_GET['start'] = date("Y-m-d H:i:s", $_GET['start']);
    $_GET['end'] = date("Y-m-d H:i:s", $_GET['end']);
    $sql = "INSERT INTO test (type, start, end, user) VALUES ('".$_GET['testType']."', '".$_GET['start']."', '".$_GET['end']."', '$user')";
    $result = mysqli_query($link, $sql);
    if($result){
        echo json_encode(array('status' => "ok"));
    }else{
        echo json_encode(array('status' => false, 'error' => 'Error en la base de datos'));
    }
}


?>