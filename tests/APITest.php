<?php


use models\alojamiento;
use models\nextpaxReserva;
use models\reserva;
use PHPUnit\Framework\TestCase;
class APITest extends TestCase{
    public function testAuthenitcation(){
        //hacemos un post request a api/authorization.php
        /**
         * {
         *        "publicKey":"JAJFJFJFIGINF"
          *  }
         */
        
         //montamos el json
            $json = array(
                'publicKey' => 'JAJFJFJFIGINF'
            );
            //hacemos el post request
            $ch = curl_init('http://localhost/AutomotitzacioCapacitatLocomotoraWeb/api/authorization.php');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));
            $response = curl_exec($ch);
            curl_close($ch);    
            echo $response;
            //comprobamos que nos ha mandado un token
            $response = json_decode($response, true);
            
            var_dump($response);
            //comprovamos que es un string
            $this->assertIsString($response['token']);

    }
}