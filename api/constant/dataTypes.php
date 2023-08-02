<?php
//devuelve los tipos de datos admitidos
/*
{
    "Data": [
        {
            "id": 1,
            "name": "Acceleròmetre"
        },
        {
            "id": 2,
            "name": "Ritme cardíac"
        }
    ]
}

*/
//nos aseguramos que sea un get request
if($_SERVER['REQUEST_METHOD'] != 'GET'){
    echo json_encode(array('status' => false, 'error' => 'Método HTTP no válido'));
    exit;
}else{
    include_once __DIR__.'/../auth_logic.php';
    //comprobamos que el authorization bearer sea un token correcto
    $headers = apache_request_headers();
    if(!isset($headers['Authorization'])){
        echo json_encode(array('status' => false, 'error' => 'Token no encontrado'));
        exit;
    }
    $token = $headers['Authorization'];
    if(token_ok($token)<0){
        echo json_encode(array('status' => false, 'error' => 'Token incorrecto'));
        exit;
    }

    include __DIR__.'/../../config.php';
    //miramos en la tabla DataType
    $sql = "SELECT * FROM typesofdata";
    $result = $link->query($sql);
    //si no hay ningún usuario con esa publicKey, devolvemos un json con status false
    if($result->num_rows == 0){
        echo json_encode(array('status' => false));
        exit;
    }else{
        //leemos toda la información del usuario
        $data = array();
        while($row = $result->fetch_assoc()){
            $data[] = $row;
        }
        echo json_encode(array('status' => true, 'Data' => $data));
    }
}

?>