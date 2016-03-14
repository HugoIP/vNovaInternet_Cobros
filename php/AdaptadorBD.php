<?php
	//Base de datos
include_once('conectorBD.php');

//INSERT
function nuevoCliente($razonSocial,$nombre,$rfc,$email,$telefono,$celular,$numEquipos, $fechaAlta, $ubicacion, $observaciones,$status){
	$connect=conectar();
	$sql = "INSERT INTO clientes (text_razonSocial,text_nombre, text_rfc, text_email, text_telefono,text_celular, int_numEquipos, date_fechaAlta, text_ubicacion, text_observaciones,text_status)
	VALUES (
	'$razonSocial','$nombre','$rfc','$email','$telefono','$celular','$numEquipos', '$fechaAlta', '$ubicacion', '$observaciones', '$status'
	)";
	$res = mysql_query($sql,$connect);
	mysql_close($connect);
	$resText = "";
	if($res==true){
		$resText = "";
	}else{
		//mensaje negativo	
		$resText = " No se ha guardado correctamente la informacion";
	}
	//return $resText;
}



function nuevoPlanInternet($nombrePlan,$descripcion,$velocidad,$limite,$periodo,$costo,$status){
	$connect=conectar();
	$sql = "INSERT INTO planesInternet (text_nombrePlan,text_descripcion,int_velocidad, int_limiteEquipos, text_periodo, float_costo,text_status)
	VALUES (
	'$nombrePlan','$descripcion','$velocidad','$limite','$periodo','$costo','$status'
	)";
	$res = mysql_query($sql,$connect);
	mysql_close($connect);
	$resText = "";
	if($res==true){
		$resText = "";
	}else{
		//mensaje negativo	
		$resText = " No se ha guardado correctamente la informacion";
	}
	//return $resText;
}
function nuevoTipoEquipo($nombre,$area,$descripcion,$imagen){
	$connect=conectar();
	$sql = "INSERT INTO tiposEquipos (text_nombre,text_area, text_descripcion, text_imagen)
	VALUES (
	'$nombre','$area','$descripcion','$imagen'
	)";
	$res = mysql_query($sql,$connect);
	mysql_close($connect);
	$resText = "";
	if($res==true){
		$resText = "";
	}else{
		//mensaje negativo	
		$resText = " No se ha guardado correctamente la informacion";
	}
	//return $resText;
}
function nuevoEquipo($nombre,$modelo,$tipo,$numSerie,$fechaAdquisicion,$costo, $proveedor, $limiteGarantia, $descripcion,$status){
	$connect=conectar();
	$sql = "INSERT INTO equipos (text_nombre, text_modelo, int_tipo, text_numSerie, date_fechaAdquisicion,int_costo, int_proveedor, date_limiteGarantia, text_descripcion, text_status)
	VALUES (
	'$nombre','$modelo','$tipo','$numSerie','$fechaAdquisicion','$costo', '$proveedor', '$limiteGarantia', '$descripcion','$status'
	)";
	$res = mysql_query($sql,$connect);
	mysql_close($connect);
	$resText = "";
	if($res==true){
		//$resText = "";
		$resText = " Correcto ".$res;
	}else{
		//mensaje negativo	
		$resText = " No se ha guardado correctamente la informacion:   ".$res;
	}
	//echo($resText);
	//return $resText;
}
function nuevoProveedor($razonSocial,$giro,$rfc,$contacto,$direccion,$telefono, $email, $pagina, $horario,$status){
	$connect=conectar();
	$sql = "INSERT INTO proveedores (text_razonSocial, text_giro, text_rfc, text_contacto, text_direccion,text_telefono, text_email, text_pagina, text_horario, text_status)
	VALUES (
	'$razonSocial','$giro','$rfc','$contacto','$direccion','$telefono', '$email', '$pagina', '$horario','$status'
	)";
	$res = mysql_query($sql,$connect);
	mysql_close($connect);
	$resText = "";
	if($res==true){
		$resText = "";
	}else{
		//mensaje negativo	
		$resText = " No se ha guardado correctamente la informacion";
	}
	//return $resText;
}
//SELECT
function obtenerTodosClientes(){
	$connect=conectar();
	$sql = "SELECT * FROM clientes WHERE text_status = 'activo'";
	$result= mysql_query($sql,$connect);
	return $result;
}
function obtenerClientes($id_cliente){
	$sql = "SELECT * FROM clientes WHERE id_cliente ='$id_cliente'";
	$result= mysql_fetch_array(mysql_query($sql,$connect));
	return $result;
}
function obtenerTodosPlanesInternet(){
	$connect=conectar();
	$sql = "SELECT * FROM planesInternet WHERE text_status = 'activo'";
	$result= mysql_query($sql,$connect);
	return $result;
}
function obtenerTodosTiposEquipos(){
	$connect=conectar();
	$sql = "SELECT * FROM tiposEquipos";
	$result= mysql_query($sql,$connect);
	return $result;
}
	//obtine solo un atributo del campo que corresponda al id
	function obtenerTipoEquipoAtr($id,$field){
		switch($field){
			case "nombre":
				$field = "text_nombre";
			break;
		}
		$connect=conectar();
		$sql = "SELECT ".$field." FROM tiposEquipos WHERE id_tipo = '$id'";
		$result= mysql_fetch_array(mysql_query($sql,$connect));
		return $result[$field];
	}
function obtenerTodosEquipos(){
	$connect=conectar();
	$sql = "SELECT * FROM equipos WHERE text_status = 'activo'";
	$result= mysql_query($sql,$connect);
	return $result;
}
function obtenerTodosProveedores(){
	$connect=conectar();
	$sql = "SELECT * FROM proveedores WHERE text_status = 'activo'";
	$result= mysql_query($sql,$connect);
	return $result;
}
	//obtine solo un atributo del campo que corresponda al id
	function obtenerProveedorAtr($id,$field){
		switch($field){
			case "nombre":
				$field = "text_razonSocial";
			break;
		}
		$connect=conectar();
		$sql = "SELECT ".$field." FROM proveedores WHERE id_proveedor = '$id'";
		$result= mysql_fetch_array(mysql_query($sql,$connect));
		return $result[$field];
	}
?>