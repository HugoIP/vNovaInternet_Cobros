<?php 
require_once('../php/conectorBD.php');
require_once('../php/DairyCheckDB.php');
require_once('php/helper.php'); 	
$connect=conectar();
$folio = $_GET['folio'];
//$folio = "C32013-04-11U1";
$infoRecibo = getRecibo($folio,$connect);
if($infoRecibo){
	$recibo =  mysql_fetch_array($infoRecibo);
	$nombreCliente = $recibo['text_nombre'];
	if($recibo['text_razonSocial']=="")
	{
		$nombreCliente = $recibo['text_nombre'];
	}
	else
	{

		$nombreCliente = $recibo['text_razonSocial'];
	}
	$domicilioCliente = $recibo['text_ubicacion'];
	$nombrePlan = $recibo['text_nombrePlan'];
	$velocidadPlan =  $recibo['int_velocidad'];
	$costoPlan = $recibo['float_costo'];
	$descuento = $recibo['descuento'];
	$status = $recibo['text_status'];

	$periodoCobro =  formatFechaCobro((string)$recibo['date_fechaInicioPeriodo'])." - ". formatFechaCobro((string)$recibo['date_fechaFinPeriodo']);

	$auxDateInit = new DateTime((string)$recibo['date_fechaInicioPeriodo']);
	$auxDateFin = new DateTime((string)$recibo['date_fechaFinPeriodo']);
	$diasPeriodo = date_diff_r(date_format($auxDateInit, 'Y-m-d'),date_format($auxDateFin, 'Y-m-d'));
	$diasCobrados = $diasPeriodo - $recibo['int_diasSinServicio'] ;
	$ajusteNum = $recibo['float_ajuste'];
	$sumaSubtotal = ($costoPlan -($descuento + $ajusteNum));
	$totalCobro = "$ 0.00";
	if($sumaSubtotal>0){
		$totalCobro = $sumaSubtotal.".00";
	}
	$ajuste = "-".(string)$recibo['float_ajuste'].".00";
	$fechaCobro =  formatFechaCobro((string)$recibo['date_fechaCobro']);
}
else{
	$nombreCliente ="";
	$domicilioCliente = "";

	$nombrePlan = "";
	$velocidadPlan =  "";
	$costoPlan = "";

	$periodoCobro =  "";
	$diasPeriodo = "";
	$diasCobrados = "" ;
	$ajuste = "";
	$totalCobro = "";

	$fechaCobro =  "";
}

function formatFechaCobro($dateInicial) { 
	$fechaInicialActual = new DateTime($dateInicial);
	//obtener fecha actual
		//Instanciamos de nuestra clase DateTimeSpanish la cual extiende de DateTime
   		$dateCut = new DateTimeSpanish(date_format($fechaInicialActual, 'Y-m-d'));
   		//$dateCut = new DateTime("2012-08-19");
		$year=$dateCut->format('Y');
		$mont=$dateCut->format('F');
		$day =$dateCut->format('d');
		return ($day.' de '.$mont.' '.$year);
} 
function date_diff_r($date1, $date2) { 
	//Contara cuantos dias hay en el intervalo de fechas marcadas
	//$date1 debe ser una fecha anterior a $date2
    $current = $date1; 
    $datetime2 = date_create($date2); 
    $count = 0; 
    while(date_create($current) < $datetime2){ 
        $current = gmdate("Y-m-d", strtotime("+1 day", strtotime($current))); 
        $count++; 
    } 
    return $count; 
}
	class DateTimeSpanish extends DateTime 
	{
	     public function format($format) {
	         $english = array('January', 'February', 'March', 'April', 'May', 'June', 'July','August','September','October','November','December');
	         $spanish = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
	         return str_replace($english, $spanish, parent::format($format));
	     }
	 }

?>
<!DOCTYPE html>
<html>
<head>
	<title>vNova Internet</title>
	<script src="../js/jquery-1.7.1.js" type="text/javascript"></script>
	<script src="../js/prefixfree.min.js"  type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" href="../styles/estilos.css" media="screen"/>
	<link rel="stylesheet" type="text/css" href="../styles/clients.css" media="screen">
	<link rel="stylesheet" type="text/css" href="../styles/clientsPrint.css" media="print">
</head>
<body>
	<div id="encabezado">
		<div id="hcontent">
	    	<div id="anim">
	            <div id="cloud1">
	                <div class="circuloC"></div>
	                <div class="circuloI"></div>
	                <div class="circuloD"></div>
	            </div>
	            <div id="cloud2">
	                <div class="circuloC"></div>
	                <div class="circuloI1"></div>
	                <div class="circuloD1"></div>
	            </div>
	            <div id="cloud3">
	                <div class="circuloC"></div>
	                <div class="circuloI"></div>
	                <div class="circuloD"></div>
	            </div>
	        </div>
	        <div id="helements">
				<a href="../" title="Inicio"><div id="logo"></div></a>
	            <div id="formInit">
	            <a href="index.php">Regresar</a>
	            </div>
			</div>
		</div>
	</div>
	<div id="cuerpo">
		<div class="lineBlank">&nbsp;</div>
		<div id="content">
			<div id="detallesEmpresa">
				<div id="logoEncabezado">
					<img src="../imagenes/vNovaCE.png" alt="vNova Internet">
				</div>
				<div id="empresa">
					<h1>Servicios de Tecnolog&iacute;a, Internet y Entretenimiento</h1>
					<div class="info-txt">Av. Nacional #10</div>
					<div class="info-txt">Santa Catarina Villanueva, Quecholac, Puebla</div>
					<div class="info-txt">&nbsp;</div>
					<div class="info-txt">RFC: LUMM780503TZ3</div>
					<div class="info-txt">Tel:  01(249)4235037</div>
				</div>
				<div id="date">
					<h2>Recibo de pago</h2>
					<div id="secure">Estatus: <?php echo($status); ?></div>
					<div id="secure">F: <?php echo($folio); ?></div>
					<div id="fecha"><?php if($status=="pagado"){ echo "Fecha: ".$fechaCobro;}else{ echo "&nbsp;";} ?></div>
				</div>
			</div>
			<div class="lineSeparator">&nbsp;</div>
			<div id="client">
				<div class="lab">Nombre:</div>
				<h2><?php echo($nombreCliente); ?></h2>
				<div class="lab">Domicilio:</div>
				<div id="domic"><?php echo($domicilioCliente); ?></div>
				<div id="printButton">
				<?php 
				//if($status=="pagado"){ 
					echo '
					<a href="javascript:window.print(); void 0;">Imprimir</a>';
				//} 
				?></div>
			</div>
			<div id="service">
				<div class="titl"><img src="../styles/img/detailServ.png" alt="Detalles del servicio"></div>
				<div class="lab">Tipo de servicio:</div>
				<div class="info-txt"><?php echo($nombrePlan); ?></div>
				<div class="info-txt">hasta <?php echo($velocidadPlan); ?> kbps, <?php if($nombrePlan=="Institucion-LineE" || $nombrePlan=="Hogar-LineE"){echo("<br/> Intalalci&oacute;n 5 pagos");} ?></div>
				<div class="lab">Costo del servicio (Mensual):</div>
				<div class="info-txt" id="moneda">$ <?php echo($costoPlan.'.00'); ?></div>
				<?php
				if($descuento>0){
				echo '<div class="lab">Descuentos o beneficios:</div>';
				echo '<div class="info-txt" id="moneda">$ -'.$descuento.'.00'.'</div>';
				}
				?>
			</div>
			<div id="payment">
				<div class="titl"><img src="../styles/img/detailCobr.png" alt="Detalles del cobro"></div>
				<div class="lab">Periodo de cobro:</div>
				<div class="info-txt"><?php echo($periodoCobro); ?></div>
				<div class="lab">Dias transcurridos:</div>
				<div class="info-txt"><?php echo($diasPeriodo); ?> dias</div>
				<div class="lab">Dias cobrados:</div>
				<div class="info-txt"><?php echo($diasCobrados); ?> dias</div>
				<div class="lab">Ajuste:</div>
				<div class="info-txt" id="moneda">$ <?php echo($ajuste); ?></div>
				<div class="lab">Total a pagar:</div>
				<div class="info-txt" id="moneda">$ <?php echo($totalCobro); ?></div>
				<div class="textVertical">Este documento no es un comprobante fiscal</div>
			</div>
			<div id="notice">
				<div class="notice-txt">Dudas y aclaraciones:</div>
				<div class="notice-txt">Cel:  045 (81)16013876   Al llamar nosotros le devolveremos la llamada para evitar el posible costo que la marcaci&oacute;n le generar&iacute;a.</div>
				<div class="notice-txt">&nbsp;</div>
				<div class="notice-txt">Le recordamos que en nuestra pagina de internet podra conocer detalles de nuestros servicios.</div>
				<div class="notice-txt" id="linkUrl">vnova.santacatarinavillanueva.com</div>
			</div>
			<div id="printBannerBn">
				<img src="../imagenes/anuncioRe.jpg" height="200" alt="Anuncios 2017" />
			</div>
			<div id="printBannerCo">
				<img src="../imagenes/anuncioRe.jpg" height="200" alt="Anuncios 2017" />
			</div>
			
		</div>
	</div>
	<div id="pie">
		<div id="pcontent">
	        <div class="footerSection">
	            <div class="optionfs titfs">Secciones</div>
	            <div class="optionfs"><a href="../clientes/">Clientes</a></div>
	            <div class="optionfs"><a href="../cobros/">Cobros</a></div> 
	        </div>
	        <div class="footerSection">
	            <div class="optionfs"><a href="#">Informes</a></div>
	        </div>
	        <div class="footerSection">
	            <div class="optionfs"><a href="#">Soporte t&eacute;cnico</a></div>
	        </div>
	        <div class="footerSection">
	            <div class="optionfs"><a href="#">Tecnolog&iacute;as de la Informaci&oacute;n</a></div>
	        </div>
	        <div class="footerSection">
	            <div class="optionfs"><a href="#">Responsabilidad social</a></div>
	        </div>
		</div>
	</div>
</body>
</html>