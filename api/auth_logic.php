<?php
function token_ok($token){
    include __DIR__.'/../config.php';
    //ponemos la hora de españa
    date_default_timezone_set('Europe/Madrid');
    //miramos si el token existe en la tabla token y no ha expirado
    $sql = "SELECT * FROM token WHERE token = '$token' AND expires >= '".date('Y-m-d H:i:s', time())."'";
    $result = $link->query($sql);
    //si no hay ningún usuario con ese token, devolvemos -1, de otro modo devolvemos el valor de user
    if($result->num_rows == 0){
        return -1;
    }else{
              $row =  $result->fetch_assoc();
              return $row['user'];
       }

}

?>