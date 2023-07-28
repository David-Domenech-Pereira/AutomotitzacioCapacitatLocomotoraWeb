<?php

date_default_timezone_set("Europe/Madrid");
$host		= getenv('DB_HOST');  // Use Local Host Only       
$username	= getenv('DB_USERNAME'); 
$password	=  getenv('DB_PASSWORD');  
$db_name	= getenv('DB_NAME');  // Database 
if(mysqli_connect($host, $username, $password,$db_name)!="") {
 
       $link = mysqli_connect($host, $username, $password,$db_name ); 
    
}else{
    //recargar pagina
       echo "Error al conectar con la base de datos";
         exit;

}
?>