<?php
//nos mandan un GET request que contiene en la URL testType, Start, End
/*devolvemos
{
    "status":"ok",
}
*/
//nos aseguramos que sea un get request
if($_SERVER['REQUEST_METHOD'] != 'GET'){
    echo json_encode(array('status' => false, 'error' => 'No es un GET request'));
    exit;
}else{
    //miramos que el token sea correcto
    include_once __DIR__.'/auth_logic.php';
    $headers = apache_request_headers();
    if(isset($headers['authorization'])){
        $headers['Authorization'] = $headers['authorization'];
    }
    if(!isset($headers['Authorization'])){
        echo json_encode(array('status' => false, 'error' => 'Token no encontrado'));
        exit;
    }
    $token = $headers['Authorization'];
    $user = token_ok($token);
    if($user<0){
        echo json_encode(array('status' => false, 'error' => 'Token incorrecto'));
        exit;
    }
    //miramos que estén los parámetros necesarios
    if(!isset($_GET['testType'])){
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
    //SI SOLO NOS MANDAN EL END
    if(!isset($_GET['start'])){
        $_GET['end'] = date("Y-m-d H:i:s", $_GET['end']);
       //hacemos un update
       $sql = "UPDATE test SET end = '".$_GET['end']."' WHERE user = '$user' AND end IS NULL";
       $link->query($sql);
       echo json_encode(array('status' => "ok"));
    }else{
        $_GET['start'] = date("Y-m-d H:i:s", $_GET['start']);
    


    $sql = "INSERT INTO test (type, start, end, user) VALUES ('".$_GET['testType']."', '".$_GET['start']."', NULL, '$user')";
    $result = mysqli_query($link, $sql);
    if($result){
        echo json_encode(array('status' => "ok"));
    }else{
        echo json_encode(array('status' => false, 'error' => 'Error en la base de datos'));
    }
    }
    
}


?>