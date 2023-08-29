<?php
header('Content-Type: application/json');
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
http_response_code(500);
//nos aseguramos que sea un post request
if($_SERVER['REQUEST_METHOD'] != 'POST'){
    http_response_code(405);
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
        http_response_code(401);
        echo json_encode(array('status' => false, 'error' => 'Token no encontrado', 'headers' => json_encode($headers)));
        exit;
    }
    $token = $headers['Authorization'];
    $customer = token_ok($token);
    if($customer<0){
        http_response_code(401);
        echo json_encode(array('status' => false, 'error' => 'Token incorrecto','Token:'=>$token));
        exit;
    }
    //insertamos los datos en la base de datos
    include __DIR__.'/../config.php';
    $file = file_get_contents('php://input');
    //le ponemos el $customer al json
    
    //guardamos el json que nos llega en ../pendingFiles/XX.json
    $name = rand(0,1000000)."_$customer.json";
    file_put_contents(__DIR__."/../pendingFiles/".$name,$file);
    http_response_code(200);
    echo json_encode(array('status' => true, 'msg' => 'Datos recibidos correctamente', 'fileName'=>$name));
    
    
}
?>