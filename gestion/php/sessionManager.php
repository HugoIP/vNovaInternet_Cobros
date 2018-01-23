<?php
	//ini_set("session.use_only_cookies","1"); 
	//ini_set("session.use_trans_sid","0"); 
	session_start();
	$fechaGuardada = $_SESSION["lastAcces"];
	//echo("last acces ". $fechaGuardada); 
    $ahora = date("Y-m-j H:i:s"); 
    $tiempo_transcurrido = (strtotime($ahora)-strtotime($fechaGuardada)); 
	$tiempo_limite=1800;
     if($tiempo_transcurrido >= $tiempo_limite && $_SESSION['iniciado']=="si") { 
     	//si pasaron 10 minutos o más 
		$_SESSION['iniciado']='no';
		 session_destroy(); // destruyo la sesión  //envío al usuario a la pag. de autenticación 
      	//sino, actualizo la fecha de la sesión 
    }else { 
		$_SESSION["lastAcces"] = $ahora;
   }
?>