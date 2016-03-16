<?php
function conectar()
{
	$dbhost = "127.0.0.1"; // El host
	$dbuser = "root"; // El usuario
	$dbpass = "m0n1ca"; // El Pass
	$db = "alfonsoe_vnovainternet"; // Nombre de la base

	$connect=mysql_connect("$dbhost","$dbuser","$dbpass"); // se conecta con la db
	mysql_select_db("$db") or die(mysql_error());
	return $connect;

	/*
	$mysqli = new mysqli($dbhost,$dbuser,$dbpass,$db); // se conecta con la db
	if ($mysqli->connect_errno) {
		printf("<br />Connect failed: %s\n", $mysqli->connect_errno);
		exit();
	}
	return $mysqli;
	*/
}
?>