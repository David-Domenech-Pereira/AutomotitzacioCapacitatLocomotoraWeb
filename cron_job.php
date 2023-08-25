<?php
//carga el archivo query.sql en la base de datos
include __DIR__.'/config.php';

$sql = file_get_contents('query.sql');
if(mysqli_multi_query($link, $sql)){
    //boramos el archivo query.sql
    unlink('query.sql');
    echo "Records inserted successfully.";
} else{
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
}
?>