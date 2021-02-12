<?php

function condicional_mail($id_equipo, $db) {  // lo uso en "/Source Files/get.php
//----------------------------1ro obtengo los id_equipo_padre
    $array_ids = array(); // voy a necesitarlo para cuando haya 2 o mas padres
// creo una query que me devuelve si esta encendido o no el PADRE.
//a continuacion, la query me devuelve los ID_EQUIPO de los padres
    $array_1 = "SELECT id_equipo_padre FROM padre_hijo  WHERE id_equipo_hijo =$id_equipo"; //obtengo los 'id_equipo_padre' de la tabla: padre_hijo
    $array_2 = $db->query($array_1);


//------------------------------- con el IF verifico si el equipo tiene padre, si no tiene, envio el mail directamente.
//   if ($array_2->num_rows > 0) { // Si entra al if, significa que el equipo tiene un padre
    while ($gett = $array_2->fetch_assoc()) {
        array_push($array_ids, $gett['id_equipo_padre']);
    }


//----------------------------2do recorro el array_ids. Obtengo el estado de cada padre (si tiene mas de uno)
    $acumula = 0;
    $tam = 0; // inicializo. 
    for ($count = 0; $count < count($array_ids); $count++) {
        $tam = count($array_ids);
        unset($estado); //supuestamente vacia el contenido de $estado
        $query_1 = "SELECT DISTINCT send_alert FROM  equipos WHERE id_equipo = '$array_ids[$count]'";
// por cada iteracion (por cada padre) devuelve el estado
        $query_2 = $db->query($query_1);
//recorro a query_padre como una matriz y guardo el resultado en $send_alert
//$send_alert tiene valores 0 (apagado) o 1 (encendido)
        $estado = $query_2->fetch_assoc();



//----------------------------3ro, voy sumando los valores que existen en las casillas de "send_alert" de c/padre

        $acumula = $acumula + ((int) $estado['send_alert']);
    }//termina for
    if ($acumula == $tam) { // si la suma acumulada es igual al tamaño de $array_ids, entonces hubo todos unos => ENVIAR MAIL
        return 1;
    } else {//pero sino, hubo un cero por lo menos y no debe enviar => NO ENVIAR MAIL
        return 0;
    }
//}//termina if ($array_2->num_rows > 0)
//else 
//    return 1; //enviar mail, este equipo no tiene padres
}
?> 