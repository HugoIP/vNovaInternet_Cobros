<?php 
//include 'php/sessionManager.php';
include('../php/conectorBD.php');
include('../php/DairyCheckDB.php');
include('php/helper.php'); 	
$connect=conectar();

$tableData="";
$contentCobros="";
$cobrosData = getCobrosPlnaesinternet("activo",$connect);
			if($cobrosData)
			{
				while($contrato =  mysql_fetch_array($cobrosData)){
					$folio = $contrato['id_cobroPlaninternet'];
					$nombre = $contrato['text_nombre'];
					$cantidad = $contrato['float_monto'];
					$fechaCreacion = $contrato['date_fechaFinPeriodo'];


					$contentCobros= $contentCobros.'
						<tr>
							<td>'.$folio.'</td>
							<td>'.$nombre.'</td>
							<td>'.$cantidad.'</td>
							<td>'.$fechaCreacion.'</td>
							<td><p class="link">Cobrar</p></td>
						</tr>';
					

				}
				$tableData='
					<table id="tableCobrosPendientes">
					<thead>
						<tr>
							<th>Folio</th>
							<th>Nombre</th>
							<th>Cantidad</th>
							<th>Fecha de corte</th>
							<th>Opciones</th>
						</tr>
					</thead>'.
					$contentCobros
					.'
					<tbody>
					</tbody>
				</table>';
			}
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
        $(document).ready(function() {
	        //mark the first
	        //registrar();
	                 
	        function registrar(){
	            $.ajax({
	                type: "POST",
	                url: "../php/DairyCheckDB.php",
	                data: "id="+"1",
	                success: function(datos){
	                         //alert( "Se guardaron los datos: " + "\n" + datos);
	                }
	            });
	        }			
			
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
					<div class="info-txt">Nombre del Cliente</div>
					<form id="formLogOut" action="" method="post">
					    <input name="logOut" id="logOut" type="submit" value="Salir" />
					</form>
				</div>

			</div>
		</div>
	</div>
	<div id="cuerpo">
		<div class="lineBlank">&nbsp;</div>
		<div id="content">
			<div id="leftColumn">
				<ul id="options">
					<li>
						Cobros pendientes
						<ul>
							<li>Detalles</li>
						</ul>
					</li>
					<li>Registrar cobro</li>
				</ul>
			</div>
			<div id="centerColumn">
			<?php
			echo ($tableData);
			?>

			</div>
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