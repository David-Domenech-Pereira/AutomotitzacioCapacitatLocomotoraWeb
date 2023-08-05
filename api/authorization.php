<?php

include_once __DIR__ . '/../config.php';

//nos aseguramos que sea un post requestç

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(array('status' => false, 'error' => 'Método de petición incorrecto','METHOD' => $_SERVER['REQUEST_METHOD']));
    exit;
} else {


    //recibimos los datos del post en un json
    $json = file_get_contents('php://input');
    //cogemos la publicKey del json
    $json = json_decode($json, true);
    $publickey = $json['publicKey'];
    //miramos en la tabla User si hay algún usuario con esa publicKey
    $sql = "SELECT * FROM user WHERE publicKey = '$publickey'";
    $result = $link->query($sql);
    if (!isset($result)||$result->num_rows == 0) {

        echo json_encode(array('status' => false, 'error' => 'Public key incorrecta', 'sql' => $sql));
        exit;
    } else {
        //leemos toda la información del usuario
        $row = $result->fetch_assoc();
        //ponemos la hora de España
        date_default_timezone_set('Europe/Madrid');
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
        $privateKeyFile = __DIR__.'/../private/privateKey_unprotected.pem';

        // Leer la clave privada desde el archivo
        $privateKey = openssl_get_privatekey(file_get_contents($privateKeyFile));

        // Firmar el resumen del mensaje con la clave privada
        openssl_sign($digest, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        // Codificar la firma en base64 para obtener una representación legible
        $encodedSignature = base64_encode($signature);

        // Concatenar el resumen y la firma para formar el token final
        $token = $digest . '.' . $encodedSignature;
        //lo limitamos a 50 carácteres
        $token = substr($token, 0, 50);
        //insertem el token a la base de datos
        //borramos los tokens que ya hayan expirado
        $sql = "DELETE FROM token WHERE expires < '" . date('Y-m-d H:i:s', time()) . "'";
        mysqli_query($link, $sql);
        //caduca en 10 minutos, formato de la fecha: YYYY-MM-DD HH:MM:SS
        $sql = "INSERT INTO token (token, user, expires) VALUES ('$token', " . $row['id'] . ", '" . date('Y-m-d H:i:s', time() + 600) . "')";
        mysqli_query($link, $sql);
        // Mostrar o devolver el token
        echo json_encode(array('status' => true, 'token' => $token));
    }
}
