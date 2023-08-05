<?php

date_default_timezone_set("Europe/Madrid");
$host		= "localhost";  // Use Local Host Only       
//$username	= "id21103083_root"; 
$username	= "root";
//$password	=  "URVESLAMILLOR@1a";  
$password	=  "";
$db_name	= "id21103083_capacitatintrinsica";  // Database 

if(mysqli_connect($host, $username, $password,$db_name)!="") {
 
       $link = mysqli_connect($host, $username, $password,$db_name ); 
    
}else{
    //recargar pagina
       echo "Error al conectar con la base de datos";
         exit;

}


?>