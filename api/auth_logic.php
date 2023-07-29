<?php
function token_ok($token){
    include __DIR__.'/../config.php';
    //miramos si el token existe en la tabla token y no ha expirado
    $sql = "SELECT * FROM token WHERE token = '$token' AND expires >= '".date('Y-m-d H:i:s', time())."'";
    $result = mysqli_query($link, $sql);
    //si no hay ningún usuario con ese token, devolvemos -1, de otro modo devolvemos el valor de user
    if(mysqli_num_rows($result) == 0){
        return -1;
    }else{
              $row = mysqli_fetch_assoc($result);
              return $row['user'];
       }

}

?>