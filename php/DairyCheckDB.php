<?php
include_once('conectorBD.php');
$conn=conectar();
$hoyYaHaRevisado = isTodayOnlyOne($conn);
if(!$hoyYaHaRevisado)
{
	$actua = mysql_query("CALL ActualizaPeriodosDePagos()",$conn);
    if ($actua === false) {
    	die(mysql_error());
	}

	$diasFaltantes = mysql_query("SELECT TIMESTAMPDIFF( 
DAY , CURDATE() , `ClientePlanesinternet`.`date_fechaInicioPeriodo`) AS dias,`ClientePlanesinternet`.`id_contratoInternet`, `ClientePlanesinternet`.`id_planInternet`
FROM`ClientePlanesinternet` WHERE `ClientePlanesinternet`.`text_status`='activo'",$conn);

	while ($row = mysql_fetch_array($diasFaltantes)){
		if($row["dias"] == 0){
			$sql ="";
			/*PENDIENTES*/
			//Obtener total de dias sin servicio
			//Obtener total de dias faltantes para este contrato
			if($row["id_planInternet"] == 2 || $row["id_planInternet"] == 7 || $row["id_planInternet"] == 9 || $row["id_planInternet"] == 12 || $row["id_planInternet"] == 13)
			{
				//wifi
				//Cobros wi-fi
				//prepago
				//Se debe indicar que hoy inicia el periodo
		   $sql="INSERT INTO `CobrosPlanesinternet` ( `id_cobroPlaninternet` , `id_contratoInternet` , `text_concepto` , `id_descuento`,`float_monto`,`date_FechaInicioPeriodo` ,`date_FechaFinPeriodo` , `text_status` )
				SELECT (CONCAT('C', `ClientePlanesinternet`.`id_contratoInternet`,`ClientePlanesinternet`.`date_fechaInicioPeriodo`,'U',`ClientePlanesinternet`.`id_cliente`)) ,`ClientePlanesinternet`.`id_contratoInternet`,  `planesInternet`.`text_nombrePlan`, `ClientePlanesinternet`.`id_descuento`,COALESCE(`planesInternet`.`float_costo`-COALESCE((SELECT `descuentos`.`float_montoDescuento` FROM`descuentos` ,`ClientePlanesinternet` WHERE`ClientePlanesinternet`.`id_descuento`=`descuentos`.`id_descuento` AND`ClientePlanesinternet`.`id_contratoInternet`=". $row["id_contratoInternet"]."),0),0),`ClientePlanesinternet`.`date_fechaInicioPeriodo`,`ClientePlanesinternet`.`date_fechaFinPeriodo`,'pendiente'
				FROM `planesInternet`, `ClientePlanesinternet` WHERE `planesInternet`.`id_planInternet`=  `ClientePlanesinternet`.`id_planInternet` AND  `ClientePlanesinternet`.`id_contratoInternet` =". $row["id_contratoInternet"];
			}
			else
			{
				//Cobros siguiente mes
				//pospago
			$sql="INSERT INTO `CobrosPlanesinternet` ( `id_cobroPlaninternet` , `id_contratoInternet` , `text_concepto` , `id_descuento`,`float_monto`,`date_FechaInicioPeriodo` ,`date_FechaFinPeriodo` , `text_status` )
				SELECT (CONCAT('C', `ClientePlanesinternet`.`id_contratoInternet`,`ClientePlanesinternet`.`date_fechaInicioPeriodo`,'U',`ClientePlanesinternet`.`id_cliente`)) ,`ClientePlanesinternet`.`id_contratoInternet`,  `planesInternet`.`text_nombrePlan`, `ClientePlanesinternet`.`id_descuento`,COALESCE(`planesInternet`.`float_costo`-COALESCE((SELECT`descuentos`.`float_montoDescuento` FROM`descuentos` ,`ClientePlanesinternet` WHERE`ClientePlanesinternet`.`id_descuento`=`descuentos`.`id_descuento` AND`ClientePlanesinternet`.`id_contratoInternet`=". $row["id_contratoInternet"]."),0),0),DATE_ADD(`ClientePlanesinternet`.`date_fechaInicioPeriodo` ,INTERVAL-1
MONTH),`ClientePlanesinternet`.`date_fechaInicioPeriodo`,'pendiente'
				FROM `planesInternet`, `ClientePlanesinternet` WHERE `planesInternet`.`id_planInternet`=  `ClientePlanesinternet`.`id_planInternet` AND  `ClientePlanesinternet`.`id_contratoInternet` =". $row["id_contratoInternet"];
			}
			$creat = mysql_query($sql,$conn);
			if($creat)
			{
				$sql ="";

				if($row["id_planInternet"] == 2 || $row["id_planInternet"] == 7 || $row["id_planInternet"] == 9 || $row["id_planInternet"] == 12 || $row["id_planInternet"] == 13)
				{
					////wifi
					//Actualizamos el saldo total del cliente
					$sql="UPDATE `ClientePlanesinternet` ,`CobrosPlanesinternet` SET `ClientePlanesinternet`.`float_saldo` = `ClientePlanesinternet`.`float_saldo` + `CobrosPlanesinternet`.`float_monto`,`CobrosPlanesinternet`.`float_resto` = `CobrosPlanesinternet`.`float_monto` WHERE`CobrosPlanesinternet`.`id_contratoInternet`=`ClientePlanesinternet`.`id_contratoInternet` AND `CobrosPlanesinternet`.`date_fechaInicioPeriodo`= `ClientePlanesinternet`.`date_fechaInicioPeriodo` AND `ClientePlanesinternet`.`id_contratoInternet`=". $row["id_contratoInternet"];

				}
				else
				{
					//clientes normales
					//Actualizamos el saldo total del cliente
					$sql="UPDATE `ClientePlanesinternet` ,`CobrosPlanesinternet` SET `ClientePlanesinternet`.`float_saldo` = `ClientePlanesinternet`.`float_saldo` + `CobrosPlanesinternet`.`float_monto`,`CobrosPlanesinternet`.`float_resto` = `CobrosPlanesinternet`.`float_monto` WHERE`CobrosPlanesinternet`.`id_contratoInternet`=`ClientePlanesinternet`.`id_contratoInternet` AND `CobrosPlanesinternet`.`date_fechaFinPeriodo`= `ClientePlanesinternet`.`date_fechaInicioPeriodo` AND `ClientePlanesinternet`.`id_contratoInternet`=". $row["id_contratoInternet"];

				}





				mysql_query($sql,$conn);

				$sql="SELECT (CONCAT('C', `ClientePlanesinternet`.`id_contratoInternet`,`ClientePlanesinternet`.`date_fechaInicioPeriodo`,'U',`ClientePlanesinternet`.`id_cliente`))
				FROM `planesInternet`, `ClientePlanesinternet` WHERE `planesInternet`.`id_planInternet`=  `ClientePlanesinternet`.`id_planInternet` AND  `ClientePlanesinternet`.`id_contratoInternet` =". $row["id_contratoInternet"];

				$idrecibo = mysql_query($sql,$conn);
				$subrow = mysql_fetch_array($idrecibo);

				sendAlert("bairon_2002@hotmail.com, monikahlm@hotmail.com",$subrow[0]);
			}
		}
			
	}
	/*
    if ($creat === false) {
    	die(mysql_error());
	}

	$creat = mysql_query("CALL CreacionDePagos()",$conn);
    if ($creat === false) {
    	die(mysql_error());
	}
	*/

	//Marcamos la fecha actual para evitar volver a ejecutar
	$today = date('Y-m-d');

	$sql = "INSERT INTO DiasRevisados (idFecha)
	VALUES (
	'$today'
	)";
	$res = mysql_query($sql,$conn);

	mysql_close();
}


function isTodayOnlyOne($conn)
{	
	//compruebo si ya existe este username
	//DiasRevisados
	// |date::idFecha||text::text_nota|
	$today = date('Y-m-d');
	$sql="SELECT idFecha FROM DiasRevisados WHERE idFecha = '$today'";
	if (mysql_num_rows(mysql_query($sql,$conn))>0)
	{
		//Existe y no haremos nada
		return true;
	}
	else 
	{
		return false;
	} 	
}
function sendAlert($des,$newfolio)
{
	$fecha=date('d-m-Y h:i:s');
	$subject = 'Recibo vNovaInternet';
	$message = '';
	$message = '
		<html>
		<head>
		<title>Recibo</title>
		</head>
		<body>
		<p>'.'Se ha creado automaticamente un recibo:</p>
		<h4> <a href="http://www.vnova.santacatarinavillanueva.com/cobros/recibo.php?folio='.$newfolio.'">'.$newfolio.'</a></h4>
		<p>
		Fecha y hora:
		'.$fecha.'
		</p>
		</body>
		</html>
		';
			
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers .= 'Bcc: bairon_2002@hotmail.com' . "\r\n";
		mail($des, $subject, $message, $headers, '-fvNovaInternet.recibos@santacatarinavillanueva.com');
}
?>