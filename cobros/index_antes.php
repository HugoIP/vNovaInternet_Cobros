<?php

include 'php/sessionManager.php';
	//dependencias
	//../../php/conectorBD.php
//$graficaDiasDeServicio = createGraphQuality();
$adeudoActualByPlan;
$diasServicio = 0;
//$fechaCut = getFechaCut();
function calculaFechaActual($dateInicial, $meses) { 
	$fechaInicialActual = new DateTime($dateInicial);
	$fechaInicialActual->modify("+".$meses." month");
	return date_format($fechaInicialActual, 'Y-m-d') ;
} 
function getFechaCut($fechaDB){
   		//obtener fecha actual
		//Instanciamos de nuestra clase DateTimeSpanish la cual extiende de DateTime
   		$dateCut = new DateTimeSpanish($fechaDB);
   		//$dateCut = new DateTime("2012-08-19");
		$dateCut->modify("+1 month");
		$year=$dateCut->format('Y');
		$mont=$dateCut->format('F');
		$day =$dateCut->format('d');
		return ($day.' de '.$mont.' '.$year);

}
	class DateTimeSpanish extends DateTime {
	     public function format($format) {
	         $english = array('January', 'February', 'March', 'April', 'May', 'June', 'July','August','September','October','November','December');
	         $spanish = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
	         return str_replace($english, $spanish, parent::format($format));
	     }
	 }


function createGraphQuality($fecha){
   		//obtener fecha actual
   		//$today = date("Y-m-j");
   		$dateCut = new DateTime($fecha);
		$dateCut->modify("+1 month");
		$dateCut->format('Y-m-d');
		$date = new DateTime($fecha);
		$dateToday = date('Y-m-d');

		$diasMes = date_diff(date_format($date, 'Y-m-d'), date_format($dateCut, 'Y-m-d'));
		$GLOBALS['diasServicio'] = date_diff(date_format($date, 'Y-m-d'),$dateToday);
		$boxMark=0;
		for($i=0;$i<$diasMes;$i++)
		{
			if($boxMark<$GLOBALS['diasServicio']){
				$graficaDiasDeServicio .= ('
                                             <div id="'.$i.'" class="boxDay"><div id="qos" class="good">&nbsp;</div></div>'.PHP_EOL);
			}else{
				$graficaDiasDeServicio .= ('
                                             <div id="'.$i.'" class="boxDay"><div id="qos">&nbsp;</div></div>'.PHP_EOL);
			}
			$boxMark++;
		}
		return ($graficaDiasDeServicio);

}
function date_diff($date1, $date2) { 
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



//echo (date_diff('2012-10-20', '2013-01-19')." days <br \>"); 
$hoyYaHaRevisado = false;
$logged = false;
$planesDetails = "";
$isConected = array('isLogIn' => false, 'userName'  => '', 'idUser' => -1, 'message' => "");
if($_POST["logIn"] != ""){
	if($_POST["logIn"] == "Entrar" && $_POST['Nick']!="" && $_POST['Pass']!=""){
		$_SESSION['iniciado']='no';
		include('../php/conectorBD.php');
		include('php/helper.php'); 	 	
		$connect=conectar();
		$user=$_POST['Nick'];
		$pass=$_POST['Pass'];
		//$hoyYaHaRevisado = isTodayOnlyOne($connect);
		$isConected=logIn($user, $pass ,$connect);
		if($isConected['isLogIn']==true){
			$_SESSION['iniciado']='si';
			$ahora = date("Y-m-j H:i:s"); 
			$_SESSION["lastAcces"] = $ahora;
			$tiempo_transcurrido=0;
			$logged = true;
			//Datos de tabla  ClientePlanesinternet

			$serviceData = getClientesPlanes($isConected['idUser'],$connect);
			if($serviceData)
			{
				while($contrato =  mysql_fetch_array($serviceData)){
					$FechaActual = calculaFechaActual((string)$contrato['date_fechaInicio'], (int)$contrato['int_periodo'] );
					$graficaDiasDeServicio = createGraphQuality((string)$FechaActual);
					$fechaCut = getFechaCut((string)$FechaActual);
					$plan =  getPlan($contrato['id_planInternet'], $connect);
					$adeudoActualByPlan = 0.00;
					$costoMensual = $plan['float_costo'];
					$velocidadKbps = $plan['int_velocidad'];
					$nombrePlan = $plan['text_nombrePlan'];
					$periodo = $plan['text_periodo'];
					$imagenPlan = $plan['text_imgUrl'];

					//Se Presentara el monto a pagar una semana antes del la fecha de vencimientov
					if($GLOBALS['diasServicio'] > 23){
						$adeudoActualByPlan = $costoMensual;
					}

					$planesDetails = $planesDetails.'
			<div class="servicio">
				<img class="imgServ" src="../imagenes/'.$imagenPlan.'" width="80" alt="'.$nombrePlan.'">
				<div class="infoServ">
					<div class="lab">Servicio del periodo actual <span class="linkPopUp">?</span></div>'.
					$graficaDiasDeServicio
				  .'
                    <div class="lab">Adeudo actual:</div>
					<div class="info-txt" id="moneda">$ '.$adeudoActualByPlan.' </div>
					<div class="lab">Pr&oacute;ximo d&iacute;a de pago:</div>
					<div class="info-txt" id="moneda">'.$fechaCut.'</div>
					<div class="info-txt">&nbsp;</div>
					<div class="titl">Detalles del servicio</div>
					<div class="lab">Tipo de servicio:</div>
					<div class="info-txt">'.$nombrePlan.'</div>
					<div class="info-txt">hasta '.$velocidadKbps.' kbps</div>
					<div class="lab">Costo del servicio ('.$periodo.'):</div>
					<div class="info-txt" id="moneda">$ '.$costoMensual.'</div>
				</div>
			</div>
			';

				}
			}
			//se almaceno el detalle de los planes dentro de la variable $planesDeatails

		}else{
			if($_SESSION['iniciado']!='si'){
				$_SESSION['iniciado']='no';
				$logged = false;
			}
			//Datos incorrectos
			//$logged = false;
		}
	}
	
}
if($_POST["logOut"] != ""){
	//LogOut
	if($_POST["logOut"] == 'Salir'){
		if($_SESSION['iniciado']=='si'){
			$_SESSION['iniciado']='no';
			session_destroy();
			$logged = false;
		}
	}
}
if($_SESSION['iniciado']=='si')
{
	$logged = true;
}
$_POST['Nick']="";
$_POST['Pass']="";
$user='';
$pass='';
?>
<!DOCTYPE html>
<html>
<head>
	<title>vNova Internet</title>
	<script src="../js/jquery-1.7.1.js" type="text/javascript"></script>
	<script src="../js/prefixfree.min.js"  type="text/javascript"></script>
	<link href="../styles/estilos.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" type="text/css" href="../styles/cobros.css" media="screen">
	<link rel="stylesheet" type="text/css" href="../styles/clientsPrint.css" media="print">
	<script type="text/javascript">
        $(function () {
            $('input[type=text]').val("");
        });
    </script>
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
				<div id="logo"></div>
				<?php
				if($logged == true)
				{
				echo('
				<div id="formInit">
					<div class="info-txt">'.$isConected['userName'].'</div>
					<form id="formLogOut" action="'.$_SERVER['PHP_SELF'].'"'.' method="post">
					    <input name="logOut" id="logOut" type="submit" value="Salir" />
					</form>
				</div>
				');
				}
				?>
			</div>
		</div>
	</div>
	<div id="cuerpo">
		<div class="lineBlank">&nbsp;</div>
		<div id="content">
			
		</div>
		<div class="lineBlank">&nbsp;</div>
	</div>
	<div id="pie">
		<div id="pcontent">
			<ul>
	        	<li><a href="#">Responsabilidad social</a></li>
	        	<li><a href="#">Soporte tecnico</a></li>
	            <li><a href="#">Tecnologias de la Informaci&oacute;n</a></li>
	            <li><a href="#">Ventas</a></li>
	        </ul>
		</div>
	</div>
</body>
</html>