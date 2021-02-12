<?php

// funcion agregada 20/11/18
function eliminar_todo($id_equipo, $link) {
    echo '<script>
            alert("eliminar_todo");
            </script>';

    $query_1 = "DELETE FROM padre_hijo WHERE id_equipo_hijo = '$id_equipo' ";
    $query_2 = $link->query($query_1);

    $query_3 = "DELETE FROM padre_hijo WHERE id_equipo_padre = '$id_equipo' ";
    $query_4 = $link->query($query_3);
}

function eliminar_padre($id_equipo, $link) { //$id_equipo=3
    //echo'<script type="text/javascript">
    //        alert("eliminar_padre");
    //        </script>';
    $array_padre = array();
    //si pongo que un equipo "no tiene" padres, tengo que eliminar de la tabla padre_hijo, todos los padres de ese equipo
    // y tambien los elimino de sus hijos
    //asi que primero armo una lista de los padres del equipo
    $query_5 = "SELECT id_equipo_padre FROM padre_hijo WHERE id_equipo_hijo = '$id_equipo'"; //$id_equipo=3, elimino los padres de 3, elimino 1
    $query_6 = $link->query($query_5);
    while ($gett = $query_6->fetch_assoc()) { //guardo la lista de padres de 3
        array_push($array_padre, $gett['id_equipo_padre']);
    }// padres son 1 y 2
    //ahora busco todos los hijos de 3
    $array_hijo = array();
    $array_padres = array();
    $query_7 = "SELECT id_equipo_hijo FROM padre_hijo WHERE id_equipo_padre = $id_equipo"; //$id_equipo=3,
    $query_8 = $link->query($query_7);
    while ($gett = $query_8->fetch_assoc()) {
        array_push($array_hijo, $gett['id_equipo_hijo']); //tiene como hijo a 5 7 9 16
    }
    for ($count = 0; $count < count($array_hijo); $count++) { // $array_hijo(5 7 9 16), busco eliminar a sus padres 1 y 2 solamente
        $query_7 = "SELECT id_equipo_padre FROM padre_hijo WHERE id_equipo_hijo = '$array_hijo[$count]'"; //$array_hijo[0]=5
        // guardo los padres de 5 que son 1, 2 y 3
        $query_8 = $link->query($query_7);
        while ($gett = $query_8->fetch_assoc()) {
            array_push($array_padres, $gett['id_equipo_padre']); // $array_padres (1 2 3)
        }
        for ($count2 = 0; $count2 < count($array_padres); $count2++) {//$array_padres[0]=1
            //
            for ($count3 = 0; $count3 < count($array_padre); $count3++) {//$array_padres[0]=1
                //
            
                if ($array_padres[$count2] == $array_padre[$count3]) {// si ( $array_padres[0]=1) == ($array_padre[0]=1)
                    $query_9 = "DELETE FROM padre_hijo WHERE id_equipo_padre ='$array_padres[$count2]' AND id_equipo_hijo ='$array_hijo[$count]'";
                    $query_10 = $link->query($query_9); //elimina la fila cuando id_equipo_padre de 3 es == a id_equipo_padre de 5
                }
            }
        }
    }
    //por ultimo elimino como hijo a id_equipo
    $query_11 = " DELETE FROM padre_hijo WHERE id_equipo_hijo = '$id_equipo'"; //$id_equipo=3, elimino los padres de 3, elimino 1
    $query_12 = $link->query($query_11);
}
?>





