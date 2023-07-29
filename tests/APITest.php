<?php


use models\alojamiento;
use models\nextpaxReserva;
use models\reserva;
use PHPUnit\Framework\TestCase;

class APITest extends TestCase
{
    public function testAuthenitcation()
    {
        //hacemos un post request a api/authorization.php
        /**
         * {
         *        "publicKey":"TOKENDEPROVA"
         *  }
         */

        //montamos el json
        $json = array(
            'publicKey' => 'TOKENDEPROVA'
        );
        //hacemos el post request
        $ch = curl_init('http://localhost/AutomotitzacioCapacitatLocomotoraWeb/api/authorization.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

        $response = curl_exec($ch);
        curl_close($ch);
        //guaramos el response en respose.json
        file_put_contents('auth_response.json', $response);
        //comprobamos que nos ha mandado un token
        $response = json_decode($response, true);
        //miramos si status es true
        $this->assertTrue($response['status']);
        var_dump($response);
        //comprovamos que es un string
        $this->assertIsString($response['token']);
        //comprovamos que sea ok
        include __DIR__ . '/../api/auth_logic.php';
        $this->assertEquals(1, token_ok($response['token']));
    }
    public function testUser()
    {
        //hacemos un post request a api/user.php
        /**
         * {
         *        "height":159,
         *        "age":50
         * }
         */

        //montamos el json
        $json = array(
            'height' => 159,
            'age' => 50
        );
        //hacemos el post request
        $ch = curl_init('http://localhost/AutomotitzacioCapacitatLocomotoraWeb/api/user.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

        $response = curl_exec($ch);
        curl_close($ch);
        //guaramos el response en respose.json
        file_put_contents('user_response.json', $response);
        //comprobamos que nos ha mandado un token
        $response = json_decode($response, true);
        //miramos si status es true
        $this->assertTrue($response['status']);
        var_dump($response);
        //comprovamos que es un string
        $this->assertIsString($response['publicKey']);
        $publicKey = $response['publicKey'];
        //probamos a autenticarnos con la publicKey
        $json = array(
            'publicKey' => $response['publicKey']
        );
        //hacemos el post request
        $ch = curl_init('http://localhost/AutomotitzacioCapacitatLocomotoraWeb/api/authorization.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        //guardamos el response
        $response = curl_exec($ch);
        curl_close($ch);
        //comprobamos que nos ha mandado un token
        $response = json_decode($response, true);
        //miramos si status es true
        $this->assertTrue($response['status']);
        var_dump($response);
        //comprovamos que es un string
        $this->assertIsString($response['token']);
        //podem borrar el usuario
        include __DIR__ . '/../config.php';
        $sql = "DELETE FROM User WHERE publicKey = '" . $publicKey . "'";
        mysqli_query($link, $sql);
    }
    public function testConstantData()
    {
        /**Esperamos el siguiente json
         * {
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
        //pedimos el token del usuario con publicKey "TOKENDEPROVA"
        $json = array(
            'publicKey' => "TOKENDEPROVA"
        );
        //hacemos el post request
        $ch = curl_init('http://localhost/AutomotitzacioCapacitatLocomotoraWeb/api/authorization.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        //guardamos el response
        $response = curl_exec($ch);
        curl_close($ch);
        //comprobamos que nos ha mandado un token
        $response = json_decode($response, true);
        //miramos si status es true
        $this->assertTrue($response['status']);
        //guardamos el token
        file_put_contents('constantData_token.json', $response['token']);
        $token = $response['token'];
        //hacemos el GET request con el authorization bearer
        $ch = curl_init('http://localhost/AutomotitzacioCapacitatLocomotoraWeb/api/constant/dataTypes.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: ' . $token));
        //guardamos el response
        $response = curl_exec($ch);

        curl_close($ch);
        //guardamos el response en respose.json
        file_put_contents('constantData_response.json', $response);
        //comprobamos que nos ha mandado un elemento Data
        $response = json_decode($response, true);
        $this->assertArrayHasKey('Data', $response);
        //comprobamos que Data es un array
        $this->assertIsArray($response['Data']);
        //comprobamos que Data tiene al menos un elemento
        $this->assertGreaterThan(0, count($response['Data']));
    }
    public function testConstantTest()
    {
        /**Esperamos el siguiente json
         * {
    "Test": [
        {
            "id": 1,
            "name": "Aixecar-se de la cadira"
        },
        {
            "id": 2,
            "name": "Test d'equilibri"
        }
    ]
}

         */
        //pedimos el token del usuario con publicKey "TOKENDEPROVA"
        $json = array(
            'publicKey' => "TOKENDEPROVA"
        );
        //hacemos el post request
        $ch = curl_init('http://localhost/AutomotitzacioCapacitatLocomotoraWeb/api/authorization.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        //guardamos el response
        $response = curl_exec($ch);
        curl_close($ch);
        //comprobamos que nos ha mandado un token
        $response = json_decode($response, true);
        //miramos si status es true
        $this->assertTrue($response['status']);
        //guardamos el token
        file_put_contents('constantTest_token.json', $response['token']);
        $token = $response['token'];
        //hacemos el GET request con el authorization bearer
        $ch = curl_init('http://localhost/AutomotitzacioCapacitatLocomotoraWeb/api/constant/testTypes.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: ' . $token));
        //guardamos el response
        $response = curl_exec($ch);

        curl_close($ch);
        //guardamos el response en respose.json
        file_put_contents('constantTest_response.json', $response);
        //comprobamos que nos ha mandado un elemento Data
        $response = json_decode($response, true);
        $this->assertArrayHasKey('Test', $response);
        //comprobamos que Data es un array
        $this->assertIsArray($response['Test']);
        //comprobamos que Data tiene al menos un elemento
        $this->assertGreaterThan(0, count($response['Test']));
    }
    public function testDataEndpoint()
    {
        /*Mandamos el siguiente JSON:
{
    "sensor":1,
    "data":[
        {
            "timestamp":19299.94,
            "values": [14,5,6]
        },
        --Más valores--
    ]
}
Donde recibimos 
{
    "status":"ok",
    "valuesRecieved":150
}

*/


        //pedimos el token del usuario con publicKey "TOKENDEPROVA"
        $json = array(
            'publicKey' => "TOKENDEPROVA"
        );
        //hacemos el post request
        $ch = curl_init('http://localhost/AutomotitzacioCapacitatLocomotoraWeb/api/authorization.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        //guardamos el response
        $response = curl_exec($ch);
        curl_close($ch);
        //comprobamos que nos ha mandado un token
        $response = json_decode($response, true);
        //miramos si status es true
        $this->assertTrue($response['status']);
        //guardamos el token
        file_put_contents('constantTest_token.json', $response['token']);
        $token = $response['token'];
        //generamos el json con valores aleatorios
        $json = array(
            'sensor' => 1,
            'data' => array()
        );
        for ($i = 0; $i < 150; $i++) {
            $json['data'][] = array(
                'timestamp' => rand(0, 1009999900),
                'values' => array(rand(0, 100), rand(0, 100), rand(0, 100))
            );
        }

        //hacemos el POST request con el authorization del json indiciado
        $ch = curl_init('http://localhost/AutomotitzacioCapacitatLocomotoraWeb/api/sensorData.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: ' . $token));
        //POST request
        $response = curl_exec($ch);
        file_put_contents('constantData_response.json', $response);
        //lo descodificamos
        $response = json_decode($response, true);
        //guardamos el response en respose.json

        //comprobamos que nos ha mandado un status ok
        $this->assertEquals('ok', $response['status']);
        //comprobamos que nos ha mandado un valuesRecieved
        $this->assertArrayHasKey('valuesRecieved', $response);
        //comprobamos que valuesRecieved es un int
        $this->assertIsInt($response['valuesRecieved']);
        //comprobamos que valuesRecieved es igual a 150*3
        $this->assertEquals(150 * 3, $response['valuesRecieved']);
    }
    public function testTestEndpoint()
    {
        //pedimos el token del usuario con publicKey "TOKENDEPROVA"
        $json = array(
            'publicKey' => "TOKENDEPROVA"
        );
        //hacemos el post request
        $ch = curl_init('http://localhost/AutomotitzacioCapacitatLocomotoraWeb/api/authorization.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        //guardamos el response
        $response = curl_exec($ch);
        curl_close($ch);
        //comprobamos que nos ha mandado un token
        $response = json_decode($response, true);
        //miramos si status es true
        $this->assertTrue($response['status']);
        //guardamos el token
        file_put_contents('constantTest_token.json', $response['token']);
        $token = $response['token'];
        //hacemos un GET request con los siguientes parametros
        //testType=1
        //start = timestamp aleatorio entre 0 y 1009999900
        //end = start + 100000
        $ch = curl_init('http://localhost/AutomotitzacioCapacitatLocomotoraWeb/api/testData.php?testType=1&start=' . rand(0, 1009999900) . '&end=' . (rand(0, 1009999900) + 100000));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: ' . $token));
        //guardamos el response
        $response = curl_exec($ch);
        curl_close($ch);
        //guardamos el response en respose.json
        file_put_contents('constantTest_response.json', $response);
        //comprobamos que nos ha mandado un status "ok"
        $response = json_decode($response, true);
        $this->assertEquals('ok', $response['status']);
        
    }
}
