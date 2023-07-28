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
        //leemos toda la información del usuario
        $row = mysqli_fetch_assoc($result);

        //Información a incluir en el token
        $data = array(
            'user_id' => $row['id'],
            'expires' => time() + 3600 // Token expira en una hora (tiempo actual + 3600 segundos)
        );

        // Convertir la información a formato JSON
        $jsonData = json_encode($data);

        // Generar el resumen (digest) del mensaje utilizando SHA-256
        $digest = hash('sha256', $jsonData);

        // Ruta del archivo que contiene la clave privada
        $privateKeyFile = '/private/privateKey.pem';

        // Leer la clave privada desde el archivo
        $privateKey = openssl_get_privatekey(file_get_contents($privateKeyFile));

        // Firmar el resumen del mensaje con la clave privada
        openssl_sign($digest, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        // Codificar la firma en base64 para obtener una representación legible
        $encodedSignature = base64_encode($signature);

        // Concatenar el resumen y la firma para formar el token final
        $token = $digest . '.' . $encodedSignature;

        // Mostrar o devolver el token
        echo json_encode(array('status' => true, 'token' => $token));

    }
}
?>