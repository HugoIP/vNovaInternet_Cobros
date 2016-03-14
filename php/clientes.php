<!DOCTYPE html> 
<html>
<head>
<title>vNova Internet Builder</title>
<script src="js/jquery-1.7.1.js" type="text/javascript"></script>
<script src="js/prefixfree.min.js"  type="text/javascript"></script>
<link href="styles/estilos.css" rel="stylesheet" type="text/css" />


<script type="text/javascript">                          
        $(document).ready(function() {
		
		//$("#encabezado").hide("slow");
		//$("#cuerpo").hide("slow");
		$("#pie").click(function(){
			//$("#encabezado").show("slow");
			$("#cuerpo").show("slow");
		});
		$("#cuerpo").click(function(){
			//$("#encabezado").hide("slow");
			//$("#cuerpo").hide("slow");
		});				
		
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
            <div id="formInit">
            <form id="formContact" method="post">
                    <div id="Nombre-l">Usuario:</div>
                    <div id="Email-l">E-mail:</div>
                    <input type="text" id="Nombre" name="Nombre" size="20" />
                    <input type="text" id="Email" name="Email" size="20" />
                <input name="Eviar" id="Enviar" type="submit" value="Enviar" />
            </form>
            </div>
		</div>
	</div>
</div>
<div id="nav">
	<div id="ncontent">
	<ul id="main-menu">
        <li><a href="#">Inicio</a></li>
        <li><a href="#">Servicios</a></li>
        <li><a href="#">Tienda Virtual</a></li>
		<li><a href="#">Promociones</a></li>
		<li><a href="#">Eventos</a></li>
        <li><a href="#">Contacto</a></li>
     </ul>
	</div>
</div>
<div id="cuerpo">
	
	<div id="ccontent">
		<form id="formContact" method="post">
        //nuevoCliente($nombre,$rfc,$conexion,$numEquipos, $fechaAlta, $ubicacion, $observaciones,$status);
                <input type="text" id="nombre" name="nombre" size="20" />
                <input type="text" name="rfc" size="20" />
                <input type="text" name="conexion" size="10" />
                <input type="text" name="numEquipos" size="4" />
                <input type="text" name="fechaAlta" size="10" />
                <input type="text" name="ubicacion" size="20" />
                <input type="text" name="observaciones" size="20" />
                <input type="text" name="status" size="10" />
            <input name="Eviar" id="Enviar" type="submit" value="Enviar" />
        </form>
	</div>
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

<?php
	include_once('AdaptadorBD.php');
	//$clientes = obtenerTodosClientes();
	nuevoCliente($nombre,$rfc,$conexion,$numEquipos, $fechaAlta, $ubicacion, $observaciones,$status);
	echo($clientes["text_nombre"]);
?>