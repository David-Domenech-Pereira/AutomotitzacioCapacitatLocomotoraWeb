<?php

include_once __DIR__.'/../config.php';

//nos aseguramos que sea un post request
if($_SERVER['REQUEST_METHOD'] != 'POST'){
    echo json_encode(array('status' => false));
    exit;
}else{
    //recibimos los datos del post en un json
    $json = file_get_contents('php://input');
    //cogemos la publicKey del json
    $json = json_decode($json, true);
    $publickey = $json['publicKey'];
    //miramos en la tabla User si hay algún usuario con esa publicKey
    $sql = "SELECT * FROM User WHERE publicKey = '$publickey'";
    $result = mysqli_query($link, $sql);
    //si no hay ningún usuario con esa publicKey, devolvemos un json con status false
    if(mysqli_num_rows($result) == 0){
        echo json_encode(array('status' => false));
        exit;
    }else{
        //si hay algún usuario con esa publicKey, generamos el token
    }
}
?>