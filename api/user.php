<?php
// Path: api\user.php
//admitimos POST request de un json con el siguiente formato
/*
{
    "height":159,
    "age":50
}
Devolvemos:
{
    "publicKey":"992ADU48GGF92939"
}

*/
//nos aseguramos que sea un post request
if($_SERVER['REQUEST_METHOD'] != 'POST'){
    echo json_encode(array('status' => false));
    exit;
}else{
    //generamos una publicKey
    include __DIR__.'/../config.php';
    $publicKey = bin2hex(random_bytes(16));
    //recibimos los datos del post en un json
    $json = file_get_contents('php://input');
    //cogemos los datos del json
    $json = json_decode($json, true);
    $height = $json['height'];
    $weight = $json['age'];
    //insertamos los datos en la base de datos
    $sql = "INSERT INTO user (height, age, publicKey) VALUES ($height, $weight, '$publicKey')";
    mysqli_query($link, $sql);
    //devolvemos la publicKey
    echo json_encode(array('status' => true, 'publicKey' => $publicKey));
}
?>