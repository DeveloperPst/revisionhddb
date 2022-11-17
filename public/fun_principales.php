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
    function consulta_estado($valor, $valor2){
        $c = $this->conexion($valor);

        switch($valor2){
            case 1: //CINTR FRA

                $query = 'select * from v$recovery_file_dest';
                $results = oci_parse($c, $query);
                oci_execute($results);
                $row = oci_fetch_array($results, OCI_BOTH);

                if(empty($row)){
                return 0;
                } else {
                    $id = 1;
                    $usado = round($row[2] / 1024 / 1024 / 1024);
                    $libre = round($row[3] / 1024 / 1024 / 1024);
                    $total = $row[2]+$row[3];
                    $totalf = round($total / 1024 / 1024 / 1024);
                    $this->visualizar_gráfico($id, $usado, $libre, $totalf);
                    echo "Usado: ".$usado." GB<br>
                    Libre: ".$libre." GB<br>
                    Total: ".$totalf." GB";
                }
            break;

            case 2: //CINTR DISC01 Y DISC02

                $query = 'SELECT ROUND(SUM(USED.BYTES) / 1024 / 1024 / 1024) "DATABASE SIZE IN GB",
                                    ROUND(SUM(USED.BYTES) / 1024 / 1024 / 1024) - ROUND(FREE.P / 1024 / 1024 / 1024) "USED SPACE IN GB",
                                    ROUND(FREE.P / 1024 / 1024 / 1024) "FREE SPACE IN GB"
                            FROM (SELECT BYTES FROM V$DATAFILE
                                UNION ALL
                                SELECT BYTES
                                FROM V$TEMPFILE
                                UNION ALL
                                SELECT BYTES
                                FROM V$LOG) USED, (SELECT SUM(BYTES) AS P
                                FROM DBA_FREE_SPACE) FREE GROUP BY FREE.P';
                $results = oci_parse($c, $query);
                oci_execute($results);
                $row = oci_fetch_array($results, OCI_BOTH);

                if(empty($row)){
                return 0;
                } else {

                    $id = 2;
                    $usado = $row[1];
                    $libre = $row[2];
                    $total = $row[0];

                    $this->visualizar_gráfico($id, $usado, $libre, $total);
                    echo "Usado: ".round($usado)." GB<br>
                    Libre: ".round($libre)." GB<br>
                    Total: ".round($total)." GB";                
                }
                break;

            case 3: //DEI DISC01
                $tabla = 'v$asm_diskgroup';

                $query = "SELECT name,free_mb,total_mb, CEIL(free_mb/total_mb*100) as percentage,(TOTAL_MB-free_mb) as usado FROM $tabla WHERE name = 'DATA01'";
                $results = oci_parse($c, $query);
                oci_execute($results);
                $row = oci_fetch_array($results, OCI_BOTH);

                if(empty($row)){
                return 0;
                } else {

                    $id = 3;
                    $usado = round($row[4] / 1024);
                    $libre = round($row[1] / 1024);
                    $total = round($usado + $libre);

                    $this->visualizar_gráfico($id, $usado, $libre, $total);
                    echo "Usado: ".$usado." GB<br>
                    Libre: ".$libre." GB<br>
                    Total: ".$total." GB";
                }
                break;

            case 4: //DEI DISC02
                $tabla = 'v$asm_diskgroup';

                $query = "SELECT name,free_mb,total_mb, CEIL(free_mb/total_mb*100) as percentage,(TOTAL_MB-free_mb) as usado FROM $tabla WHERE name = 'DATA02'";
                $results = oci_parse($c, $query);
                oci_execute($results);
                $row = oci_fetch_array($results, OCI_BOTH);
    
                if(empty($row)){
                return 0;
                } else {
                    $id = 4;
                    $usado = round($row[4] / 1024);
                    $libre = round($row[1] / 1024);
                    $total = round($usado + $libre);

                    $this->visualizar_gráfico($id, $usado, $libre, $total);
                    echo "Usado: ".$usado." GB<br>
                    Libre: ".$libre." GB<br>
                    Total: ".$total." GB";
                }
                break; 

                case 5: //DEI FRA
                    $tabla = 'v$asm_diskgroup';

                    $query = "SELECT name,free_mb,total_mb, CEIL(free_mb/total_mb*100) as percentage,(TOTAL_MB-free_mb) as usado FROM $tabla WHERE name = 'FRA'";
                    $results = oci_parse($c, $query);
                    oci_execute($results);
                    $row = oci_fetch_array($results, OCI_BOTH);
        
                    if(empty($row)){
                    return 0;
                    } else {
                    $id = 5;
                    $usado = round($row[4] / 1024);
                    $libre = round($row[1] / 1024);
                    $total = round($usado + $libre);

                    $this->visualizar_gráfico($id, $usado, $libre, $total);
                    echo "Usado: ".$usado." GB<br>
                    Libre: ".$libre." GB<br>
                    Total: ".$total." GB";
                }
                break;

                case 6: //WEB DISC01
                    $tabla = 'v$asm_diskgroup';
    
                    $query = "SELECT name,free_mb,total_mb, CEIL(free_mb/total_mb*100) as percentage,(TOTAL_MB-free_mb) as usado FROM $tabla WHERE name = 'DATA01'";
                    $results = oci_parse($c, $query);
                    oci_execute($results);
                    $row = oci_fetch_array($results, OCI_BOTH);
    
                    if(empty($row)){
                    return 0;
                    } else {
    
                        $id = 6;
                        $usado = round($row[4] / 1024);
                        $libre = round($row[1] / 1024);
                        $total = round($usado + $libre);
    
                        $this->visualizar_gráfico($id, $usado, $libre, $total);
                        echo "Usado: ".$usado." GB<br>
                        Libre: ".$libre." GB<br>
                        Total: ".$total." GB";
                    }
                    break;
    
                    case 7: //WEB DISC02
                        $tabla = 'v$asm_diskgroup';
        
                        $query = "SELECT name,free_mb,total_mb, CEIL(free_mb/total_mb*100) as percentage,(TOTAL_MB-free_mb) as usado FROM $tabla WHERE name = 'DATA02'";
                        $results = oci_parse($c, $query);
                        oci_execute($results);
                        $row = oci_fetch_array($results, OCI_BOTH);
            
                        if(empty($row)){
                        return 0;
                        } else {
                            $id = 7;
                            $usado = round($row[4] / 1024);
                            $libre = round($row[1] / 1024);
                            $total = round($usado + $libre);
        
                            $this->visualizar_gráfico($id, $usado, $libre, $total);
                            echo "Usado: ".$usado." GB<br>
                            Libre: ".$libre." GB<br>
                            Total: ".$total." GB";
                        }
                        break; 
    
                    case 8: //WEB FRA
                        $tabla = 'v$asm_diskgroup';
    
                        $query = "SELECT name,free_mb,total_mb, CEIL(free_mb/total_mb*100) as percentage,(TOTAL_MB-free_mb) as usado FROM $tabla WHERE name = 'FRA'";
                        $results = oci_parse($c, $query);
                        oci_execute($results);
                        $row = oci_fetch_array($results, OCI_BOTH);
            
                        if(empty($row)){
                        return 0;
                        } else {
                        $id = 8;
                        $usado = round($row[4] / 1024);
                        $libre = round($row[1] / 1024);
                        $total = round($usado + $libre);
    
                        $this->visualizar_gráfico($id, $usado, $libre, $total);
                        echo "Usado: ".$usado." GB<br>
                        Libre: ".$libre." GB<br>
                        Total: ".$total." GB";
                    }
                    break;
                oci_close($c);
        }
    }

    public function visualizar_gráfico($id, $usado, $libre, $total){
echo "
<style type='text/css'>
.highcharts-figure-".$id." .chart-container {
    width: 150px;
    height: 90px;
    border-radius: 7px;
}
</style>

<script src='code/highcharts.js'></script>
<script src='code/highcharts-more.js'></script>
<script src='code/modules/solid-gauge.js'></script>
<script src='code/modules/accessibility.js'></script>

<figure class='highcharts-figure-".$id."'>
    <div id='container-speed-".$id."' class='chart-container'></div>
</figure>

<script type='text/javascript'>
var gaugeOptions = {
    chart: {
        type: 'solidgauge'
    },

    title: null,

    pane: {
        center: ['50%', '85%'],
        size: '170%',
        startAngle: -90,
        endAngle: 90,
        background: {
            backgroundColor:
                Highcharts.defaultOptions.legend.backgroundColor || '#EEE',
            innerRadius: '60%',
            outerRadius: '100%',
            shape: 'arc'
        }
    },

    exporting: {
        enabled: false
    },

    tooltip: {
        enabled: false
    },

    // the value axis
    yAxis: {
        stops: [
            [0.1, '#55BF3B'], // green
            [0.5, '#DDDF0D'], // yellow
            [0.9, '#DF5353'] // red
        ],
        lineWidth: 0,
        tickWidth: 0,
        minorTickInterval: null,
        tickAmount: 2,
        title: {
            y: -70
        },
        labels: {
            y: 16
        }
    },

    plotOptions: {
        solidgauge: {
            dataLabels: {
                y: 5,
                borderWidth: 0,
                useHTML: true
            }
        }
    }
};

// The speed gauge
var chartSpeed = Highcharts.chart('container-speed-".$id."', Highcharts.merge(gaugeOptions, {
    yAxis: {
        min: 0,
        max: ".$total.",
    },

    credits: {
        enabled: false
    },

    series: [{
        data: [".$usado."]
    }]

}));
</script>";

    }
}
?>