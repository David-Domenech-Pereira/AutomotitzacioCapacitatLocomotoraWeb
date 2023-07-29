<?php

date_default_timezone_set("Europe/Madrid");
$host		= "localhost";  // Use Local Host Only       
$username	= "root"; 
$password	=  "";  
$db_name	= "capacitatintrinsica";  // Database 

if(mysqli_connect($host, $username, $password,$db_name)!="") {
 
       $link = mysqli_connect($host, $username, $password,$db_name ); 
    
}else{
    //recargar pagina
       echo "Error al conectar con la base de datos";
         exit;

}


?>