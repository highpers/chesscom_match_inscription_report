<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />

 <title>Facturación por Especialidad</title>
 <style type="text/css">
 body { margin: 11px; margin-bottom: 0px; font-family: verdana, arial; font-weight: bold; font-size: 13px; }
 </style>

</head>
<body>
<?php
ini_set('display_errors', '1');
error_reporting(2047);
$especialidadesAmostrar =  array();
$montoFactEspSeleccionadas = 0 ;


$tiposGrafico = array('PieChart' , 'PieChart' , 'Bar' , 'BarChart') ;

die('En ' . __FILE__ . ' -  Linea: ' . __LINE__ . ":  <h1>si uso google charts, solamente torta y rosquilla");


$tipoGrafico = $tiposGrafico[$_POST['tipoGrafico']];

//die('En ' . __FILE__ . ' -  Linea: ' . __LINE__ . ": $tipoGrafico");


if($_POST['tipoGrafico'] == 1){ // rosquilla
    $agujero = 0.3 ;
    $es3d = 'false' ;
}else{
    $agujero = 0 ;
    $es3d = 'true' ;
} 



foreach($_POST['especialidadesGrafico'] as $k=>$idEsp){
    
    $especialidadesAmostrar[$k]['descrEspecialidad'] = $_POST['descrEsp'][$idEsp] ;
    $especialidadesAmostrar[$k]['monto'] = $_POST['montoXesp'][$idEsp] ;
    
    // de paso, calculamos en esta recorrida el monto de facturación de las especialidades seleccionadas para calcular por difrencia el monto de "otras"
    $montoFactEspSeleccionadas += $_POST['montoXesp'][$idEsp] ;
}


// calculo del monto de "otras"

$montoOtras = $_POST['montoFacturadoTotal'] - $montoFactEspSeleccionadas ;


if($montoOtras > 0){ // agregamos el registro de "otras"
    
    $indiceOtras = count($especialidadesAmostrar) ;
    
    $especialidadesAmostrar[$indiceOtras]['descrEspecialidad'] = 'No seleccionadas';
    $especialidadesAmostrar[$indiceOtras]['monto'] = $montoOtras;
}

// Calculamos el porcentaje de cada item con respecto al total y generamos el string de datos:
$data = "[ ['Especialidad', 'Porcentaje'],";
//          ['Work',     11],
//          ['Eat',      2],
//          ['Commute',  2],
//          ['Watch TV', 2],
//          ['Sleep',    7]
//        ]'
foreach($especialidadesAmostrar as $esp){

    $monto = (float) $esp['monto'] ;
   $porcentaje = ($monto / $_POST['montoFacturadoTotal']) * 100 ;

    $monto = number_format($esp['monto'] , 2 , ',' , '.') ; // parece que esto tiene un problemita
    $monto = $esp['monto'] ;
    // formateamos el valor de porcentaje
    
    $porcentaje = number_format($porcentaje, 2 , '.' , ',') ;
    
    $data .= "['".$esp['descrEspecialidad']." ($monto)' , $porcentaje],";
       
}
$data = rtrim($data, ','). ']';

/// graficación

echo   '<h2>'.$_POST['hospital'].'</h2><h3>Facturación por especialidad.</h3>Desde '.$_POST['fechaDesde'].' hasta '.$_POST['fechaHasta'].'<p>&nbsp;<br>';

$tituloGrafico = 'Porcentaje facturado por especialidad';


?>

  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable(<?=$data?>);
//        var data = google.visualization.arrayToDataTable([['Especialidad', 'Porcentaje'],['CIRUGIA  GRAL (1200033.00)' , 8],['LABORATORIO (559717.00)' ,4],['PEDIATRIA  GRAL (461171.00)' , 3],['TERAPIA INTENSIVA (700240.00)' , 5],['Otras (10700280.24)' , 78]]);
//         var data = google.visualization.arrayToDataTable([
//          ['Task', 'Hours per Day'],
//          ['Work',     11],
//          ['Eat',      2],
//          ['Commute',  2],
//          ['Watch TV', 2],
//          ['Sleep',    7]
//        ]);
        var options = {
          title: '<?=$tituloGrafico?>',
          pieHole : 0.3,
          is3D: <?=$es3d?>,
          sliceVisibilityThreshold: .025,
          pieResidueSliceLabel : 'Otras (seleccionadas con bajo porcentaje)'

        };

        var chart = new google.visualization.<?=$tipoGrafico?>(document.getElementById('piechart_3d'));
        chart.draw(data, options);
        
    }
    
 </script>
    
    
    <div id="piechart_3d" style="width: 100%; height: 500px;border:1px sold"></div>
    
    