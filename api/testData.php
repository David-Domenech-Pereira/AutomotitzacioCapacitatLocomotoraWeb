<?php
//nos mandan un GET request que contiene en la URL testType, Start, End
/*devolvemos
{
    "status":"ok",
}
*/
//nos aseguramos que sea un get request
if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    echo json_encode(array('status' => false, 'error' => 'No es un GET request'));
    exit;
} else {
    //miramos que el token sea correcto
    include_once __DIR__ . '/auth_logic.php';
    $headers = apache_request_headers();
    if (isset($headers['authorization'])) {
        $headers['Authorization'] = $headers['authorization'];
    }
    if (!isset($headers['Authorization'])) {
        echo json_encode(array('status' => false, 'error' => 'Token no encontrado'));
        exit;
    }
    $token = $headers['Authorization'];
    $user = token_ok($token);
    if ($user < 0) {
        echo json_encode(array('status' => false, 'error' => 'Token incorrecto'));
        exit;
    }
    //miramos que estén los parámetros necesarios
    if (!isset($_GET['testType']) || (!isset($_GET['start']) && !isset($_GET['end']))) {
        echo json_encode(array('status' => false, 'error' => 'Faltan parámetros'));
        exit;
    }
    //insertamos el test en la base de datos
    include __DIR__ . '/../config.php';

    //los start y end los ponemos en formao YYYY-MM-DD HH:MM:SS
    //SI SOLO NOS MANDAN EL END
    if (!isset($_GET['start'])) {
        if(!isset($_GET["float"])){
        $_GET['end'] = round($_GET['end'] / 1000, 2);
        }
        $_GET['end'] = date("Y-m-d H:i:s", $_GET['end']);
        //hacemos un update
        $sql = "UPDATE test SET end = '" . $_GET['end'] . "' WHERE user = '$user' AND end IS NULL AND type='" . $_GET['testType'] . "'";
        $result = $link->query($sql);
        if ($result) {
            echo json_encode(array('status' => true, 'msg' => "Test finalizado","Hora"=>$_GET['end'], 'sql' => $sql));
        } else {
            echo json_encode(array('status' => false, 'error' => 'Error en la base de datos', 'sql' => $sql));
        }
    } else {
        if(!isset($_GET["float"])){
        $_GET['start'] = round($_GET['start'] / 1000, 2);
    }
        $_GET['start'] = date("Y-m-d H:i:s", $_GET['start']);



        $sql = "INSERT INTO test (type, start, end, user) VALUES ('" . $_GET['testType'] . "', '" . $_GET['start'] . "', NULL, '$user')";
        $result = mysqli_query($link, $sql);
        if ($result) {
            echo json_encode(array('status' => true, 'msg' => "Test iniciado","Hora"=>$_GET['start'], 'sql' => $sql));
        } else {
            echo json_encode(array('status' => false, 'error' => 'Error en la base de datos', 'sql' => $sql));
        }
    }
}
