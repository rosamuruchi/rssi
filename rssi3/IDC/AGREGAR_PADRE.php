

<?php

// funcion agregada  16/11/18
function agregar_padre($ip, $id_padre, $db) {// lo utilizo en "IDC/agregar_ip.php"
    ////$ip= al ip del hijo (equipo nuevo)
    //
    //el programa busca el nuevo equipo dentro de la tabla: equipos
    //pero como "agregar_ip.php" no esta funcionando
    //porque no tiene SNMP
    //Por lo tanto, agrega un eqpipo pero como nulo, en cambio si agrega su padre y los 
    //demas datos que no tienen que ver con el equipo nuevo
    //
    // busco el id_equipo_hijo ( el equipo nuevo ingresado)
    if (($ip == NULL)or ( $id_padre == NULL)) {
        // en el caso de que no se haya ingresado el padre, entonces no se hace nada
    } else {

        //$query_1 = "SELECT *FROM equipos WHERE ip= '$ip'";
        $query_1 = "SELECT  id_equipo, device_name, send_alert FROM equipos WHERE ip= '$ip'";
        $query_2 = $db->query($query_1);
        $query_id_hijo = $query_2->fetch_assoc();
        $id_hijo = $query_id_hijo['id_equipo'];
        $device_name_hijo = $query_id_hijo['device_name'];
        $send = $query_id_hijo['send_alert'];
        //--------------------------------------------------------------------------
        //busca el id_padre, busca si ya exite en la tabla pero como hijo
        //(si el padre no tiene padre, lo agregara, al final del codigo) 
        //
        $array_ids = array();
        $query_5 = "SELECT id_equipo_padre FROM padre_hijo WHERE id_equipo_hijo= '$id_padre'"; //busco los padres de 21, (el hijo ingresado fue el  18)
        $query_6 = $db->query($query_5);
        while ($gett = $query_6->fetch_assoc()) { // utilizo while, sino de otra manera me guarda solo el 1er valor encontrado
            array_push($array_ids, $gett['id_equipo_padre']); // el 'id_equipo_padre' ser 13, 16,14 , ...etc, por ejemplo. Porque un hijo puede tener muchos padres.
        }

        //--------------------------------------------------------------------------
        // ahora tengo que agregar al equipo nuevo y asignarle un puesto en la cola del arbol
        //uso devuelta el for para recorrer la lista de padres de 21
        for ($count = 0; $count < count($array_ids); $count++) { //recorro el array con la lista de padres 
            //----------busco el device_name  de $array_ids[$count]
            //$query_aux = "SELECT * FROM equipos WHERE id_equipo= '$array_ids[$count]'";
            $query_aux = "SELECT device_name, send_alert  FROM equipos WHERE id_equipo= '$array_ids[$count]'";
            $query_temp = $db->query($query_aux);
            $query_padre = $query_temp->fetch_assoc();
            $device_name_padre = $query_padre['device_name'];
            $send_alert = $query_padre['send_alert'];


            $query_7 = "INSERT INTO padre_hijo 
            (id_equipo_padre, id_equipo_hijo,device_name_hijo, device_name_padre, send_alert_padre, send_alert_hijo)
                                            VALUES ('$array_ids[$count]', ' $id_hijo ', ' $device_name_hijo', ' $device_name_padre', '$send_alert ' , '$send')";
            // ejecuto la query y la guardo
            $query_8 = $db->query($query_7);
        }

        //ahora agrego el padre (ingresado por teclado) y el hijo (ingresado por teclado) 
        if (($id_padre == 0)and ( $id_hijo == 0)) { //si los valores son nulos, no hace nada
        } else {
            // $query_aux2 = "SELECT * FROM equipos WHERE id_equipo= '$id_padre'";
            $query_aux2 = "SELECT device_name, send_alert FROM equipos WHERE id_equipo= '$id_padre'";
            $query_temp2 = $db->query($query_aux2);
            $query_padre2 = $query_temp2->fetch_assoc();
            $device_name_padre2 = $query_padre2['device_name'];
            $send_alert3 = $query_padre2['send_alert'];
            //----------
            $query_5 = "INSERT INTO padre_hijo 
          (id_equipo_padre, id_equipo_hijo,device_name_hijo, device_name_padre, send_alert_padre, send_alert_hijo)
                                           VALUES ('$id_padre', ' $id_hijo ', ' $device_name_hijo ', '$device_name_padre2 ', '$send_alert3', '$send')";
            $query_6 = $db->query($query_5);
        }
    }
}

function agregar_padre2($id_hijo,$send, $id_padre, $db) {  // lo utilizo en "/IDC/modificar_datos.php"
    // busco el id_equipo_hijo ( el equipo nuevo ingresado)
    if ($id_padre == NULL) {
        // en el caso de que no se haya ingresado el padre, entonces no se hace nada
       
    } else {
        //$query_1 = "SELECT * FROM equipos WHERE id_equipo= '$id_hijo'";
        $query_1 = "SELECT device_name FROM equipos WHERE id_equipo= '$id_hijo'";
        $query_2 = $db->query($query_1);
        $query_id_hijo = $query_2->fetch_assoc();
        $device_name_hijo = $query_id_hijo['device_name'];
        //$send = $query_id_hijo['send_alert'];
        //--------------------------------------------------------------------------
        //busca el id_padre, busca si ya exite en la tabla pero como hijo
        //(si el padre no tiene padre, lo agregara, al final del codigo) 
        //
        $array_ids = array();
        $query_5 = "SELECT id_equipo_padre FROM padre_hijo WHERE id_equipo_hijo= '$id_padre'";
        $query_6 = $db->query($query_5);
        while ($gett = $query_6->fetch_assoc()) { // utilizo while, sino de otra manera me guarda solo el 1er valor encontrado
            array_push($array_ids, $gett['id_equipo_padre']); // el 'id_equipo_padre' ser 13, 16,14 , ...etc, por ejemplo. Porque un hijo puede tener muchos padres.
        }
        //--------------------------------------------------------------------------
        // ahora tengo que agregar al equipo nuevo y asignarle un puesto en la cola del arbol
        //uso devuelta el for para recorrer la lista de padres
        for ($count = 0; $count < count($array_ids); $count++) { //recorro el array con la lista de padres 
            //----------busco el device_name  de $array_ids[$count]
            //$query_aux = "SELECT * FROM equipos WHERE id_equipo= '$array_ids[$count]'";
            $query_aux = "SELECT device_name, send_alert FROM equipos WHERE id_equipo= '$array_ids[$count]'";
            $query_temp = $db->query($query_aux);
            $query_padre = $query_temp->fetch_assoc();
            $device_name_padre = $query_padre['device_name'];
            $send_alert = $query_padre['send_alert'];


            $query_7 = "INSERT INTO padre_hijo 
            (id_equipo_padre, id_equipo_hijo,device_name_hijo, device_name_padre, send_alert_padre, send_alert_hijo)
                                            VALUES ('$array_ids[$count]', ' $id_hijo ', ' $device_name_hijo', ' $device_name_padre', '$send_alert ' , '$send')";
            // ejecuto la query y la guardo
            $query_8 = $db->query($query_7);
            //$agregar = $query_2->fetch_assoc(); //no hace falta creo
        }

        //ahora agrego el padre (ingresado por teclado) y el hijo (ingresado por teclado) 
        if (($id_padre == 0)and ( $id_hijo == 0)) { //si los valores son nulos, no hace nada
        } else {
            //$query_aux2 = "SELECT *FROM equipos WHERE id_equipo= '$id_padre'";
            $query_aux2 = "SELECT device_name, send_alert FROM equipos WHERE id_equipo= '$id_padre'";
            $query_temp2 = $db->query($query_aux2);
            $query_padre2 = $query_temp2->fetch_assoc();
            $device_name_padre2 = $query_padre2['device_name'];
            $send_alert3 = $query_padre2['send_alert'];
            //----------
            $query_5 = "INSERT INTO padre_hijo 
          (id_equipo_padre, id_equipo_hijo,device_name_hijo, device_name_padre, send_alert_padre, send_alert_hijo)
                                           VALUES ('$id_padre', ' $id_hijo ', ' $device_name_hijo ', '$device_name_padre2 ', '$send_alert3', '$send')";
            $query_6 = $db->query($query_5);
        }
    }
}
function agregar_padre3($id_equipo, $send, $db) {  // se actualiza el registro si se modifico el valor de $send_alert
    if ($send == "") {
        
    } else {
        //$query_1 = "SELECT *FROM equipos WHERE id_equipo= '$id_equipo'";
        $query_1 = "SELECT send_alert FROM equipos WHERE id_equipo= '$id_equipo'";
        $query_2 = $db->query($query_1);
        $query_hijo = $query_2->fetch_assoc();
        $send_alert_viejo = $query_hijo['send_alert'];
        if ($send_alert_viejo == $send) {
            
        } else { // SI ($send_alert_viejo <> $send ) entonces hay que actualizar la el registro en tabla padre_hijo
            $query_3 = "UPDATE padre_hijo SET send_alert_hijo = '$send' WHERE id_equipo_hijo ='$id_equipo' ";
            $query_4 = $db->query($query_3);
            //$query_hijo = $query_2->fetch_assoc();
            $query_5 = "UPDATE padre_hijo SET send_alert_padre = '$send' WHERE id_equipo_padre ='$id_equipo' ";
            $query_6 = $db->query($query_5);
        }
    }
}

?> 

</body>
</html>
