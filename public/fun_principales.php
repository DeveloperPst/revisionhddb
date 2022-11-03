<?php

Class Sesiones {

    // FUNCIÓN CONEXIÓN CINTR DB
    function conexion($valor){

        $servername = "";
        $username = "";
        $password = "";

        switch($valor){
            case 1: //CINTR
                $servername = "dbprod-cintr.consorciopst.com:1521/CINTR";
                $username = "gr4f1c0";
                $password = "P5T123-gr4f";
            break;

            case 2: //DEI
                $servername = "10.1.2.81:1522/DEI";
                $username = "gr4f1c0";
                $password = "P5T123-gr4f";
            break;

            case 3: //WEB
                $servername = "10.1.2.81:1521/WEB";
                $username = "gr4f1c0";
                $password = "P5T123-gr4f";
            break;
        }

        $c = oci_connect($username, $password, $servername);
        if (!$c) {
           return 0;
        } else {
           return $c;
        }
    }

    // FUNCIÓN PARA CONSULTAR ESTADOS DE HD EN CINTR
    function consulta_estado($valor){
        $c = $this->conexion(1);

        switch($valor){
            case 1: //CINTR
                $query = 'select * from v$recovery_file_dest';
            break;

            case 2: //DEI
                $query = 'select * from v$recovery_file_dest';
            break;

            case 3: //WEB
                $query = 'select * from v$recovery_file_dest';
            break;
        }
       
        $results = oci_parse($c, $query);
        oci_execute($results);
        $row = oci_fetch_array($results, OCI_BOTH);

        if(empty($row)){
        return 0;
        } else {
        return $results;
        }

    oci_close($c);
    }
}
?>