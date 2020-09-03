<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
 <title>Facturación por Especialidad</title>
 <script src="../incs/d3.min.js"></script>
<script src="../incs/d3pie.min.js"></script>
 <style type="text/css">
 body { margin: 11px; margin-bottom: 0px; font-family: verdana, arial; font-weight: bold; font-size: 13px; }
 </style>
</head>
<body>
<?php
ini_set('display_errors', '1');
error_reporting(2047);

//print_r($_POST);
function colorAlAzar(){
    
    $color = '#';
    $color .= str_pad(dechex(rand(0x000000, 0xFFFFFF)), 6, 0, STR_PAD_LEFT); return $color ;
    for($i = 1 ; $i<4; ++$i){
        $color .= str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
    }
    return $color ;
}

function setColores($cant){
    
    $colores = array() ;

for($i = 0 ; $i <= $cant+1 ; ++$i){    
    $newColor = colorAlAzar() ;
    
    if(!in_array($newColor, $colores)) { // para asegurar que no se repitan
        $colores[] = $newColor ;
    }
    
}    
    return($colores) ;
    
    
}

$especialidadesAmostrar =  array();
$montoFactEspSeleccionadas = 0 ;

if($_POST['tipoGrafico'] == 1){ // rosquilla
    $agujero = 43 ;
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
    $especialidadesAmostrar[$indiceOtras]['descrEspecialidad'] = 'Otras (no seleccionadas)';
    $especialidadesAmostrar[$indiceOtras]['monto'] = $montoOtras;
}

$colores = setColores(count($_POST['especialidadesGrafico'])) ;
 $colores_bak =array('#0000c0' ,'#FF0000' , '#a05c56' , '#efefef' , '#FC9933 ' ,'#9618D8' , '#7b6688' , '#2081c1' , '#661818' , '#b96181' , '#309c36' , '#d3e233' , '#6b69Fa' ,'#000000', '#0000ff' , '#22e300', '#e6194b', '#3cb44b', '#ffe119', '#4363d8', '#00ff00', '#911eb4', '#46f0f0', '#f032e6', '#bcf60c', '#fabebe', '#008080', '#06be2f', '#9a6324', '#fffac8', '#800000', '#aaffc3', '#808000', '#ffd8b1', '#000095', '#808080' , '#CCFF33' , '#c69999', '#699ff', '#003333' , '#CC9900' , '#660000') ;

$dat = '';
$i = 0;

foreach($especialidadesAmostrar as $esp){
//    if($esp['descrEspecialidad'] == 'INFECTOLOGIA')        die('En ' . __FILE__ . ' -  Linea: ' . __LINE__ . ": $i");
//    if($esp['descrEspecialidad'] == 'INFECTOLOGIA')        die('En ' . __FILE__ . ' -  Linea: ' . __LINE__ . ": $i");
//echo $i. ' - ' .$esp['descrEspecialidad']. '<br>';
    $monto = (float) $esp['monto'] ;
    $montoAmostrar = '$ '.number_format($esp['monto'] , 2 , ',' , '.') ; // parece que esto tiene un problemita
    
$dat .= '{"label": "'.$esp['descrEspecialidad'].'  ('.$montoAmostrar.')" , "value" : '.$monto.', "color": "'.$colores[$i].'" },'  ; 
//$dat .= '{"label": "'.$esp['descrEspecialidad'].'  ('.$montoAmostrar.')" , "value" : '.$monto.', "color": "'.colorAlAzar().'" },'  ; 
    ++$i ;
}
$data = rtrim($dat, ','); // elimino la coma final

echo   '<h2>'.$_POST['hospital'].'</h2><h3>Facturación por especialidad</h3>&nbsp;<br>';

/// graficación
?>
 <div id="pieChart" style="border:1px solid; border-radius:9px"></div>

<script>
var pie = new d3pie("pieChart", {
	"header": {
		"title": {
			"text": "Porcentaje facturado por especialidad",
			"fontSize": 20,
			"font": "open sans"
		},
		"subtitle": {
			"text":  "Desde <?=$_POST['fechaDesde']?> hasta <?=$_POST['fechaHasta']?>",
			"color": "#666699",
			"fontSize": 12,
			"font": "open sans"
		},
		"location": "top-left",
		"titleSubtitlePadding": 8
	},
	"footer": {
		"color": "#999999",
		"fontSize": 10,
		"font": "open sans",
		"location": "bottom-left"
	},
	"size": {
		"canvasWidth": 945,
		"pieOuterRadius": "100%",
		"pieInnerRadius": "<?=$agujero?>%",
	},
	"data": {
		"sortOrder": "<?=$_POST['orden']?>", // "sortOrder": "random", "sortOrder": "value-asc", "sortOrder": "value-desc", "label-asc" , "label-desc"
		"smallSegmentGrouping": {
			"enabled": true,
			"value": 2,
			"label": "Otras (bajo %)",
			"color": "<?=$colores[count($colores)-1] ?>"
		},
		"content": [
                                                    <?=$data ?>
		]
	},
	"labels": {
		"outer": {
			"pieDistance": 21   
		},
		"mainLabel": {
			"fontSize": 10
		},
		"percentage": {
			"color": "#ffffff",
			"decimalPlaces": 0
		},
		"value": {
			"color": "#666666",
			"fontSize": 11
		},
		"lines": {
			"enabled": true,
			"color": "#333333"
		},
		"truncation": {
			"enabled": true,
			"truncateLength": 52
		}
	},
	"tooltips": {
		"enabled": true,
		"type": "placeholder",
		"string": "{label}:  {percentage}%",
		"styles": {
			"fadeInSpeed": 65,
			"backgroundColor": "#e7e9ee",
			"backgroundOpacity": 0.84,
			"color": "#000000",
			"borderRadius": 4,
			"fontSize": 9
		}
	},
        
	"effects": {
		"load": {
			"speed": 1770
		},
		"pullOutSegmentOnClick": {
                                                                "effect": "linear",
			"speed": 170,
			"size": 14
		}
	},
	"misc": {
		"gradient": {
			"enabled": true,
			"percentage": 100
		},
		"canvasPadding": {
			"top": 4,
			"right": 220,
			"bottom": 36,
			"left": 260
		}
	}
});
</script>
