<?php
function logIn($user, $pass ,$connect){
	$response = array('isLogIn' => false, 'userName'  => '', 'idUser' => -1, 'message' => "", 'priv' => 3);
	$isLogIn=false;
	if(!empty($user)){
		$sql=sprintf("SELECT * FROM cajeros WHERE text_nick='$user' AND text_password='$pass'",mysql_real_escape_string($user), mysql_real_escape_string($pass));
		$result= mysql_fetch_array(mysql_query($sql));
		if ($result)
		{
			$isLogIn=true;
			$response['isLogIn'] = $isLogIn;
			$response['userName'] = $result['text_nombre'];
			$response['idUser'] = $result['id_cajero'];
			$response['message'] = 'Bienvenido';
			$response['priv'] = $result['int_priv'];
			countVisitEmploy($response['idUser'] );
		}else{
			$isLogIn=false;
			$response['isLogIn'] = $isLogIn;
			$response['message'] = 'Datos incorrectos';
		}
	}
	return $response;
}
function countVisit($idUser){
	$sql=sprintf("UPDATE clientes SET int_visitas=int_visitas+1 WHERE id_cliente = '$idUser'") ;
	mysql_query($sql);
}
function logInEmploy($idUser, $pass ,$connect){
	//Se usa para confirmar el cobro realizado por algun cajero 
	$response = array('isLogIn' => false, 'userName'  => '', 'idUser' => -1, 'message' => "", 'priv' => 3);
	$isLogIn=false;
	if(!empty($idUser) && !empty($pass)){
		$sql=sprintf("SELECT * FROM cajeros WHERE id_cajero='$idUser' AND text_password='$pass'",mysql_real_escape_string($idUser), mysql_real_escape_string($pass));
		$result= mysql_fetch_array(mysql_query($sql));
		if ($result)
		{
			$isLogIn=true;
			$response['isLogIn'] = $isLogIn;
			$response['userName'] = $result['text_nombre'];
			$response['idUser'] = $result['id_cajero'];
			$response['message'] = 'Bienvenido';
			$response['priv'] = $result['int_priv'];
			countVisitEmploy($response['idUser'] );
		}else{
			$isLogIn=false;
			$response['isLogIn'] = $isLogIn;
			$response['message'] = 'Datos incorrectos';
		}
	}
	else
	{
		echo "<p>No hay cobros pendientes</p>";
	}
	return $response;
}
function countVisitEmploy($idCajero){
	$fechaCompleta = new DateTime();
	$date = ''.$fechaCompleta->format('Y-m-d H:i:s');
	$sql=sprintf("UPDATE cajeros SET int_visitas=int_visitas+1, datetime_ultimoAcceso='$date' WHERE id_cajero = '$idCajero'") ;
	mysql_query($sql);
}
function getDataPlansInternet($idUser){

}
function getRecibo($folio,$connect){
	$response = false;
	$sql="SELECT`CobrosPlanesinternet`.* ,`clientes`.`text_razonSocial`,`clientes`.`text_nombre` ,`clientes`.`text_ubicacion` ,`planesInternet`.`text_nombrePlan` ,`planesInternet`.`int_velocidad` ,`planesInternet`.`float_costo`,(SELECT `float_montoDescuento` FROM `descuentos` WHERE `CobrosPlanesinternet`.`id_descuento` = `descuentos`.`id_descuento` ) AS `descuento`,`CobrosPlanesinternet`.`float_resto` AS `saldo`
FROM`CobrosPlanesinternet` ,`ClientePlanesinternet` ,`clientes` ,`planesInternet`
WHERE`CobrosPlanesinternet`.`id_contratoInternet`=`ClientePlanesinternet`.`id_contratoInternet` 
AND`ClientePlanesinternet`.`id_cliente`=`clientes`.`id_cliente` 
AND`ClientePlanesinternet`.`id_planInternet`=`planesInternet`.`id_planInternet` 
AND`CobrosPlanesinternet`.`id_cobroPlaninternet`='$folio'";

	$result = mysql_query($sql,$connect);
	if ($result)
	{
		$response = $result;
	}
	else
	{
		$response = false;
	}
	

	return $response;
}
function getReciboServicio($folio,$connect){
	$response = false;
	$sql="SELECT`CobrosServicios`.* ,`clientes`.`text_razonSocial`,`clientes`.`text_nombre` ,`clientes`.`text_ubicacion` , `Servicios`.`text_nombreServicio` ,`Servicios`.`float_costoServicio` ,`CobrosServicios`.`float_resto`AS`saldo` 
FROM`CobrosServicios` ,`clientes` ,`Servicios` 
WHERE`CobrosServicios`.`id_cliente`=`clientes`.`id_cliente` 
AND`CobrosServicios`.`id_servicio`=`Servicios`.`id_servicio` 
AND`CobrosServicios`.`id_cobroServicio`='$folio'";

	$result = mysql_query($sql,$connect);
	if ($result)
	{
		$response = $result;
	}
	else
	{
		$response = false;
	}
	

	return $response;
}
function realizaPago($connect,$monto,$pendiente,$cajero,$folio,$formaDePago,$tipoComprobante)
{
	//$cajero - persona que realizo el cobro
	//$folio - clave del servicio a cobrar
	//$formaDePago - Efectivo, Deposito, Interbancaria
	//$tipoComprobante - Factura o Recibo
	$fecha =  date("Y-m-d");
	$fechaCompleta = new DateTime();
	$status = "pendiente";
	$response = false;
	$monto = (int)$monto;
	$pendiente = (int)$pendiente;
	if(($monto>0 && $monto<=$pendiente))
	{


	if($monto==$pendiente)
	{
		$status = "pagado";
	}
	$date = ''.$fechaCompleta->format('Y-m-d H:i:s');
	//Actualizamos el saldo de el presente contrato
	//ademas se cambia el estatus a pagado
	$sql="UPDATE `CobrosPlanesinternet`, `ClientePlanesinternet` SET `ClientePlanesinternet`.`float_saldo` = `ClientePlanesinternet`.`float_saldo` - COALESCE('$monto',0),`CobrosPlanesinternet`.`float_resto` = `CobrosPlanesinternet`.`float_resto` - COALESCE('$monto',0), `CobrosPlanesinternet`.`text_status` = '$status', `CobrosPlanesinternet`.`date_fechaCobro` = '$date' WHERE `ClientePlanesinternet`.`id_contratoInternet`=`CobrosPlanesinternet`.`id_contratoInternet` AND `CobrosPlanesinternet`.`id_cobroPlaninternet` = '$folio' AND `CobrosPlanesinternet`.`text_status` != 'pagado'";
	$result = mysql_query($sql);
	$response = false;
	if($result)
	{
		$sql = "INSERT INTO Cobros (float_monto, date_fecha, text_idRecibo, id_cajero)
		VALUES (
		'$monto','$date', '$folio' , '$cajero'
		)";
		$res = mysql_query($sql,$connect);
		mysql_close($connect);
		if($res)
		{
			$response = true;
		}
	}

	}
	return $response;

}
function realizaPagoServicio($connect,$monto,$pendiente,$cajero,$folio,$formaDePago,$tipoComprobante)
{
	//$cajero - persona que realizo el cobro
	//$folio - clave del servicio a cobrar
	//$formaDePago - Efectivo, Deposito, Interbancaria
	//$tipoComprobante - Factura o Recibo
	$fecha =  date("Y-m-d");
	$fechaCompleta = new DateTime();
	$status = "pendiente";
	$response = false;
	$monto = (int)$monto;
	$pendiente = (int)$pendiente;
	if(($monto>0 && $monto<=$pendiente))
	{


	if($monto==$pendiente)
	{
		$status = "pagado";
	}
	$date = ''.$fechaCompleta->format('Y-m-d H:i:s');
	//Actualizamos el saldo de el presente contrato
	//ademas se cambia el estatus a pagado
	$sql="UPDATE `CobrosServicios` SET `CobrosServicios`.`float_resto` = `CobrosServicios`.`float_resto` - COALESCE('$monto',0), `CobrosServicios`.`text_status` = '$status', `CobrosServicios`.`date_fechaCobro` = '$date' WHERE `CobrosServicios`.`id_cobroServicio` = '$folio' AND `CobrosServicios`.`text_status` != 'pagado'";
	$result = mysql_query($sql);
	$response = false;
	if($result)
	{
		$sql = "INSERT INTO Cobros (float_monto, date_fecha, text_idRecibo, id_cajero)
		VALUES (
		'$monto','$date', '$folio' , '$cajero'
		)";
		$res = mysql_query($sql,$connect);
		mysql_close($connect);
		if($res)
		{
			$response = true;
		}
	}

	}
	return $response;

}
function getCobrosPlnaesinternet($comand,$connect)
{
	//$response = array('id_contratoInternet' => 0, 'id_cliente'  => 0, 'id_planesInternet' => -1, 'date_fechaInicio' => "", 'date_fechaFinPeriodo' => "", 'int_periodo' => 0, 'text_observaciones' => "No existe servicio asignado a este nombre");
	$response = false;

	$sql="SELECT * FROM CobrosPlanesinternet WHERE 0";
	//Table: CobrosPlanesinternet
	//`id_contratoInternet` ,`id_cliente` ,`id_planesInternet` ,`date_fechaInicio` ,`date_fechaFinPeriodo` ,`int_periodo` ,`text_observaciones`
	if($comand =="all")
	{
		//$sql="SELECT * FROM CobrosPlanesinternet WHERE `CobrosPlanesinternet`.`text_status` = 'pendiente'";
		$sql="SELECT`CobrosPlanesinternet`.* ,`clientes`.`text_nombre`, `ClientePlanesinternet`.`id_planInternet`
FROM`CobrosPlanesinternet` ,`clientes` ,`ClientePlanesinternet` 
WHERE`clientes`.`id_cliente`=`ClientePlanesinternet`.`id_cliente` 
AND`CobrosPlanesinternet`.`id_contratoInternet`=`ClientePlanesinternet`.`id_contratoInternet`
ORDER BY `CobrosPlanesinternet`.`date_FechaFinPeriodo`DESC";

	}
	else if($comand =="pendienteypagado")
	{
		//solo podra ver pagados y pendientes
		$sql="SELECT`CobrosPlanesinternet`.* ,`clientes`.`text_nombre`,`ClientePlanesinternet`.`id_planInternet`
FROM`CobrosPlanesinternet` ,`clientes` ,`ClientePlanesinternet` 
WHERE`clientes`.`id_cliente`=`ClientePlanesinternet`.`id_cliente` 
AND`CobrosPlanesinternet`.`id_contratoInternet`=`ClientePlanesinternet`.`id_contratoInternet`
AND (`CobrosPlanesinternet`.`text_status` = 'pagado' OR `CobrosPlanesinternet`.`text_status` = 'pendiente' )
ORDER BY `CobrosPlanesinternet`.`date_FechaFinPeriodo`DESC";

	}
	else if($comand =="pendiente")
	{
		//solo podra ver pendientes de pago
		$sql="SELECT`CobrosPlanesinternet`.* ,`clientes`.`text_nombre`,`ClientePlanesinternet`.`id_planInternet`
FROM`CobrosPlanesinternet` ,`clientes` ,`ClientePlanesinternet` 
WHERE`clientes`.`id_cliente`=`ClientePlanesinternet`.`id_cliente` 
AND`CobrosPlanesinternet`.`id_contratoInternet`=`ClientePlanesinternet`.`id_contratoInternet`
AND `CobrosPlanesinternet`.`text_status` = 'pendiente' 
ORDER BY `CobrosPlanesinternet`.`date_FechaFinPeriodo`DESC";

	}


	
	$result = mysql_query($sql,$connect);
	if ($result)
	{
		$response = $result;
	}
	else
	{
		$response = false;
	}
	

	return $response;
}
function getCobroPlaninternet($contract,$comand,$connect)
{
	//$response = array('id_contratoInternet' => 0, 'id_cliente'  => 0, 'id_planesInternet' => -1, 'date_fechaInicio' => "", 'date_fechaFinPeriodo' => "", 'int_periodo' => 0, 'text_observaciones' => "No existe servicio asignado a este nombre");
	$response = false;

	$sql="SELECT * FROM CobrosPlanesinternet WHERE 0";
	//Table: CobrosPlanesinternet
	//`id_contratoInternet` ,`id_cliente` ,`id_planesInternet` ,`date_fechaInicio` ,`date_fechaFinPeriodo` ,`int_periodo` ,`text_observaciones`
	if($comand =="all")
	{
		$sql="SELECT`CobrosPlanesinternet`.*, `ClientePlanesinternet`.`id_planInternet`FROM`CobrosPlanesinternet` ,`ClientePlanesinternet` WHERE`CobrosPlanesinternet`.`id_contratoInternet`=$contract AND`CobrosPlanesinternet`.`id_contratoInternet`=`ClientePlanesinternet`.`id_contratoInternet` ORDER BY `CobrosPlanesinternet`.`date_FechaFinPeriodo`ASC";

	}
	else if($comand =="pendienteypagado")
	{
		$sql="SELECT`CobrosPlanesinternet`.*, `ClientePlanesinternet`.`id_planInternet`FROM`CobrosPlanesinternet` ,`ClientePlanesinternet` WHERE`CobrosPlanesinternet`.`id_contratoInternet`= $contract AND`CobrosPlanesinternet`.`id_contratoInternet`=`ClientePlanesinternet`.`id_contratoInternet`AND (`CobrosPlanesinternet`.`text_status` = 'pagado' OR `CobrosPlanesinternet`.`text_status` = 'pendiente' )ORDER BY `CobrosPlanesinternet`.`date_FechaFinPeriodo`ASC";

	}
	else if($comand =="pendiente")
	{
		$sql="SELECT`CobrosPlanesinternet`.*, `ClientePlanesinternet`.`id_planInternet`FROM`CobrosPlanesinternet` ,`ClientePlanesinternet` WHERE`CobrosPlanesinternet`.`id_contratoInternet`=$contract AND`CobrosPlanesinternet`.`id_contratoInternet`=`ClientePlanesinternet`.`id_contratoInternet`AND `CobrosPlanesinternet`.`text_status` = 'pendiente'ORDER BY `CobrosPlanesinternet`.`date_FechaFinPeriodo`ASC";

	}


	
	$result = mysql_query($sql,$connect);
	if ($result)
	{
		$response = $result;
	}
	else
	{
		$response = false;
	}
	

	return $response;
}
function getCobrosClientesSumados($comand, $connect)
{
	


	$sql = "SELECT`CobrosPlanesinternet`.`id_contratoInternet`AS contrato,`clientes`.`text_nombre`AS cliente, SUM(`CobrosPlanesinternet`.`float_resto`)AS saldo,`ClientePlanesinternet`.`id_planInternet`AS plan,`ClientePlanesinternet`.`date_fechaInicioPeriodo`AS fechaCorte, COUNT(`CobrosPlanesinternet`.`id_contratoInternet`)AS periodos
FROM`CobrosPlanesinternet` ,`clientes` ,`ClientePlanesinternet` 
WHERE`clientes`.`id_cliente`=`ClientePlanesinternet`.`id_cliente` 
AND`CobrosPlanesinternet`.`id_contratoInternet`=`ClientePlanesinternet`.`id_contratoInternet` 
GROUP BY`CobrosPlanesinternet`.`id_contratoInternet` 
ORDER BY fechaCorte DESC";
	//Table: CobrosPlanesinternet
	//`id_contratoInternet` ,`id_cliente` ,`id_planesInternet` ,`date_fechaInicio` ,`date_fechaFinPeriodo` ,`int_periodo` ,`text_observaciones`
	if($comand =="all")
	{
		$sql = "SELECT`CobrosPlanesinternet`.`id_contratoInternet`AS contrato,`clientes`.`text_nombre`AS cliente, SUM(`CobrosPlanesinternet`.`float_resto`)AS saldo,`ClientePlanesinternet`.`id_planInternet`AS plan,`ClientePlanesinternet`.`date_fechaInicioPeriodo`AS fechaCorte, COUNT(`CobrosPlanesinternet`.`id_contratoInternet`)AS periodos
FROM`CobrosPlanesinternet` ,`clientes` ,`ClientePlanesinternet` 
WHERE`clientes`.`id_cliente`=`ClientePlanesinternet`.`id_cliente` 
AND`CobrosPlanesinternet`.`id_contratoInternet`=`ClientePlanesinternet`.`id_contratoInternet` 
AND`ClientePlanesinternet`.`text_status`='activo'
GROUP BY`CobrosPlanesinternet`.`id_contratoInternet` 
ORDER BY fechaCorte DESC";
	}
	else if($comand =="pendienteypagado")
	{
		$sql = "SELECT`CobrosPlanesinternet`.`id_contratoInternet`AS contrato,`clientes`.`text_nombre`AS cliente, SUM(`CobrosPlanesinternet`.`float_resto`)AS saldo,`ClientePlanesinternet`.`id_planInternet`AS plan,`ClientePlanesinternet`.`date_fechaInicioPeriodo`AS fechaCorte, COUNT(`CobrosPlanesinternet`.`id_contratoInternet`)AS periodos
FROM`CobrosPlanesinternet` ,`clientes` ,`ClientePlanesinternet` 
WHERE`clientes`.`id_cliente`=`ClientePlanesinternet`.`id_cliente` 
AND`CobrosPlanesinternet`.`id_contratoInternet`=`ClientePlanesinternet`.`id_contratoInternet` 
AND (`CobrosPlanesinternet`.`text_status`='pendiente' OR `CobrosPlanesinternet`.`text_status`='pagado')
AND`ClientePlanesinternet`.`text_status`='activo'
GROUP BY`CobrosPlanesinternet`.`id_contratoInternet` 
ORDER BY fechaCorte DESC";

	}
	else if($comand =="pendiente")
	{
		$sql = "SELECT`CobrosPlanesinternet`.`id_contratoInternet`AS contrato,`clientes`.`text_nombre`AS cliente, SUM(`CobrosPlanesinternet`.`float_resto`)AS saldo,`ClientePlanesinternet`.`id_planInternet`AS plan,`ClientePlanesinternet`.`date_fechaInicioPeriodo`AS fechaCorte, COUNT(`CobrosPlanesinternet`.`id_contratoInternet`)AS periodos
FROM`CobrosPlanesinternet` ,`clientes` ,`ClientePlanesinternet` 
WHERE`clientes`.`id_cliente`=`ClientePlanesinternet`.`id_cliente` 
AND`CobrosPlanesinternet`.`id_contratoInternet`=`ClientePlanesinternet`.`id_contratoInternet` 
AND`CobrosPlanesinternet`.`text_status`='pendiente'
AND`ClientePlanesinternet`.`text_status`='activo'
GROUP BY`CobrosPlanesinternet`.`id_contratoInternet` 
ORDER BY fechaCorte DESC";

	}


	$result = mysql_query($sql,$connect);
	if ($result)
	{
		$response = $result;
		//get new total adding other sells
		//getServices();
	}
	else
	{
		$response = false;
	}
	

	return $response;
}
function getClientesPlanes($idUser, $connect)
{
	//$response = array('id_contratoInternet' => 0, 'id_cliente'  => 0, 'id_planesInternet' => -1, 'date_fechaInicio' => "", 'date_fechaFinPeriodo' => "", 'int_periodo' => 0, 'text_observaciones' => "No existe servicio asignado a este nombre");
	$response = false;

	//Hare un calculo del periodo de todos los usuarios
	//Esta consulta debera ser STORED PROCEDURE
	$sql="UPDATE ClientePlanesinternet SET int_periodo = TIMESTAMPDIFF(MONTH ,date_fechaInicio , CURDATE())";
	mysql_query($sql);
	//Table: ClientePlanesinternet
	//`id_contratoInternet` ,`id_cliente` ,`id_planesInternet` ,`date_fechaInicio` ,`date_fechaFinPeriodo` ,`int_periodo` ,`text_observaciones` 
	if(!empty($idUser))
	{
		$sql="SELECT * FROM ClientePlanesinternet WHERE id_cliente = '$idUser'";
		$result = mysql_query($sql,$connect);
		if ($result)
		{
			$response = $result;
		}
		else
		{
			$response = false;
		}
	}
	//calculamosel periodo actual
	return $response;
}
function getPlan($idPlan, $connect)
{
	$response = false;
	//Table: planesInternet
	//`id_planInternet`, `text_nombrePlan`, `text_descripcion`, `int_velocidad`, `int_limiteEquipos`, `text_periodo`, `int_costo`, `text_status` 
	if(!empty($idPlan))
	{
		$sql="SELECT * FROM planesInternet WHERE id_planInternet = '$idPlan'";
		$result = mysql_fetch_array(mysql_query($sql,$connect));
		if ($result)
		{

			$response = $result;
		}
		else
		{
			$response = false;
		}
	}
	return $response;
}
//Obtine el nombre del cajero que realizo el cobro y el monto de dicho cobro
function getDataCobro($connect, $idUser, $idFolio)
{
	$response = array('cajero' => "HIP", 'monto' => 0);
	$sql =  "SELECT `text_nombre` FROM `cajeros` WHERE `id_cajero` = '$idUser'";
	$result = mysql_fetch_array(mysql_query($sql,$connect));
	$response["cajero"] =  $result["text_nombre"];
	$sql2="SELECT `float_monto` FROM `Cobros` WHERE `text_idRecibo` = '$idFolio'";
	$result = mysql_fetch_array(mysql_query($sql2,$connect));
	$response["monto"] =  $result["float_monto"];

	return $response;
}
//INSERT
/*function isTodayOnlyOne($connect)
{	
	//compruebo si ya existe este username
	$today = date('Y-m-d');
	$sql="SELECT idFecha FROM DiasRevisados WHERE idFecha = '$today'";
	if (mysql_num_rows(mysql_query($sql,$connect))>0)
	{
		//Existe y no haremos nada
		return true;
	}
	else 
	{
		return false;
	} 	
}*/
function getClientes($connect,$status)
{
	$response = false;
	$sql="SELECT `clientes`.`id_cliente`, `clientes`.`text_nombre` FROM  `clientes` WHERE `clientes`.`text_status`='activo'";
	switch($status)
	{
		case "activo":
			$sql="SELECT `clientes`.`id_cliente`, `clientes`.`text_nombre` FROM  `clientes` WHERE `clientes`.`text_status`='activo'";
		break;
		case "all":
			$sql="SELECT `clientes`.`id_cliente`, `clientes`.`text_nombre` FROM  `clientes`";
		break;

	}
	
	$sql="SELECT * FROM  `clientes`";
	$result = mysql_query($sql,$connect);
	if ($result)
	{
		$response = $result;
	}
	else
	{
		$response = false;
	}

	return $response;
}

function getCobroServicios($client,$comand,$connect)
{
	//$response = array('id_contratoInternet' => 0, 'id_cliente'  => 0, 'id_planesInternet' => -1, 'date_fechaInicio' => "", 'date_fechaFinPeriodo' => "", 'int_periodo' => 0, 'text_observaciones' => "No existe servicio asignado a este nombre");
	$response = false;

	$sql="SELECT * FROM CobrosServicios WHERE `CobrosServicios`.`id_cliente`=$client AND `CobrosServicios`.`text_status` = 'pendiente'";
	//Table: CobrosPlanesinternet
	//`id_contratoInternet` ,`id_cliente` ,`id_planesInternet` ,`date_fechaInicio` ,`date_fechaFinPeriodo` ,`int_periodo` ,`text_observaciones`
	if($comand =="all")
	{
		$sql="SELECT * FROM `CobrosServicios` WHERE `CobrosServicios`.`id_cliente`=$client";

	}
	else if($comand =="pendienteypagado")
	{
		$sql="SELECT * FROM `CobrosServicios` WHERE `CobrosServicios`.`id_cliente`=$client";

	}
	else if($comand =="pendiente")
	{
		$sql="SELECT * FROM `CobrosServicios` WHERE `CobrosServicios`.`id_cliente`=$client AND `CobrosServicios`.`text_status` = 'pendiente'";

	}


	
	$result = mysql_query($sql,$connect);
	if ($result)
	{
		$response = $result;
	}
	else
	{
		$response = false;
	}
	

	return $response;
}
function getSaldoServicios($connect,$idCliente)
{
	$sql="SELECT COALESCE(SUM(`CobrosServicios`. `float_resto`),0) as `saldo`  FROM `clientes` , `CobrosServicios` WHERE `CobrosServicios`. `id_cliente` = `clientes`.`id_cliente` AND  `clientes`.`id_cliente`= $idCliente";
	$result = mysql_query($sql,$connect);
	if ($result)
	{
		$response = $result;
	}
	else
	{
		$response = false;
	}
	return $response;
}
function getSaldoInternet($connect,$idContrato)
{
	$sql="SELECT COALESCE(SUM(`CobrosPlanesinternet`. `float_resto`),0) AS `saldo` FROM `CobrosPlanesinternet` WHERE `CobrosPlanesinternet`.`id_contratoInternet`= $idContrato";
	$result = mysql_query($sql,$connect);
	if ($result)
	{
		$response = $result;
	}
	else
	{
		$response = false;
	}
	return $response;
}
function hasAdeudo($connect,$idCliente)
{
	$sql="SELECT * FROM `clientes` , `CobrosServicios` WHERE `CobrosServicios`. `id_cliente` = `clientes`.`id_cliente` AND `CobrosServicios`.`text_status`='pendiente' AND  `clientes`.`id_cliente`= $idCliente";
	$result = mysql_query($sql,$connect);
	if ($result)
	{
		$response = $result;
	}
	else
	{
		$response = false;
	}
	return $response;
}
function hasContratosInternet($connect,$idCliente)
{
	//SELECT * FROM `ClientePlanesinternet`, `clientes` WHERE `clientes`.`id_cliente`= `ClientePlanesinternet`.`id_cliente` AND `ClientePlanesinternet`.`text_status`="activo" AND `clientes`.`id_cliente`=4
	$sql="SELECT `ClientePlanesinternet`.* FROM `ClientePlanesinternet`, `clientes` WHERE `clientes`.`id_cliente`= `ClientePlanesinternet`.`id_cliente` AND `ClientePlanesinternet`.`text_status`='activo' AND `clientes`.`id_cliente`= $idCliente";
	$result = mysql_query($sql,$connect);
	if ($result)
	{
		$response = $result;
	}
	else
	{
		$response = false;
	}
	return $response;
}
?>