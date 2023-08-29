<?php
//carga el archivo query.sql en la base de datos
include __DIR__.'/config.php';

//leemos todos los json de la carpeta pendingFiles
$files = scandir(__DIR__.'/pendingFiles');
foreach($files as $file){
    if($file != '.' && $file != '..'){
        //leemos el json
        $json = file_get_contents(__DIR__.'/pendingFiles/'.$file);
        //lo convertimos a array
        $json = json_decode($json,true);
        //el titulo del documento es numero_customer.json
        //el customer es el id del usuario
        //el numero es un numero aleatorio
        //sacamos el customer
        $name = explode("_",$file);
        if(isset($json["start"])){
            $start = $json['start'];
            }
        //le quitamos el .json
        $json['customer'] = explode(".",$name[1])[0];
        //lo insertamos en la base de datos
        $sql = "INSERT INTO data (user, time, value, `index`, type) VALUES ";
        $timestamp_anterior = "";
        $timestamps = array();
        $i = 0;
        $json["ms"]=1;
        foreach($json['data'] as $data){
            if(isset($start)){
                //ens passen ms a sumar
                $timestamp = $start + round($data["timestamp"]/1000,2);
                $data['timestamp'] = $timestamp;
            }
            if(isset($json["ms"])&&!isset($start)){
                $data['timestamp'] = round($data['timestamp']/1000,6);
            }
           
            if(!in_array($data['timestamp'],$timestamps)){
                if($i>0){
                    $sql .= ", ";
                }
                $i++;
                //evitamos que se inserten datos repetidos
                $sql .= "(".$json['customer'].", ".$data['timestamp'].", ".$data['values'][0].", 0,".$json["sensor"]."), (".$json['customer'].", ".$data['timestamp'].", ".$data['values'][1].", 1,".$json["sensor"]."), (".$json['customer'].", ".$data['timestamp'].", ".$data['values'][2].", 2,".$json["sensor"].")";
                $timestamps[] = $data['timestamp']; //lo añadimos al array
            }
        }
        if($link->query($sql)){
            echo "Datos insertados correctamente";
            //borramos el archivo
            unlink(__DIR__.'/pendingFiles/'.$file);
        }else{
            echo $json['customer'];
            echo "Error al insertar los datos";
            echo $link->error;
            
        }
    }
}
?>