<?php
//devuelve los tipos de tests
/*
{
    "Test": [
        {
            "id": 1,
            "name": "Aixecar-se de la cadira"
        },
        {
            "id": 2,
            "name": "Test d'equilibri"
        }
    ]
}


*/
//nos aseguramos que sea un get request
if($_SERVER['REQUEST_METHOD'] != 'GET'){
    echo json_encode(array('status' => false));
    exit;
}else{
    include_once __DIR__.'/../auth_logic.php';
    //comprobamos que el authorization bearer sea un token correcto
    $headers = apache_request_headers();
    if(!isset($headers['Authorization'])){
        echo json_encode(array('status' => false));
        exit;
    }
    $token = $headers['Authorization'];
    if(token_ok($token)<0){
        echo json_encode(array('status' => false, 'error' => 'Token incorrecto'));
        exit;
    }

    include __DIR__.'/../../config.php';
    //miramos en la tabla DataType
    $sql = "SELECT * FROM typesoftest";
    $result = mysqli_query($link, $sql);
    //si no hay ningún usuario con esa publicKey, devolvemos un json con status false
    if(mysqli_num_rows($result) == 0){
        echo json_encode(array('status' => false));
        exit;
    }else{
        //leemos toda la información del usuario
        $data = array();
        while($row = mysqli_fetch_assoc($result)){
            $data[] = $row;
        }
        echo json_encode(array('status' => true, 'Test' => $data));
    }
}

?>