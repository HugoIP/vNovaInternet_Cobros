<?php
//include 'php/sessionManager.php';
include('../php/conectorBD.php');
include('../php/DairyCheckDB.php');
include('php/helper.php'); 	
$connect=conectar();

$logged = false;
$isConected = array('isLogIn' => false, 'userName'  => '', 'idUser' => -1, 'message' => "",'priv' => 3);
if($_POST["logIn"] != ""){
	if($_POST["logIn"] == "Entrar" && $_POST['Nick']!="" && $_POST['Pass']!=""){
		$isConected = logIn($_POST['Nick'], $_POST['Pass'] ,$connect);
		$logged = $isConected['isLogIn'];
	}
}
$tableData= tableDataAdeudos($connect,$isConected);
function tableDataAdeudos($connect,$isConected){
	$cobrosData= false;
	$contentCobros="";
	$contentCobrosWifi="";
	switch($isConected['priv'])
	{
		case 0:
			$cobrosData = getCobrosPlnaesinternet("all",$connect);
		break;
		case 1:
			$cobrosData = getCobrosPlnaesinternet("pendienteypagado",$connect);
		break;
		case 2:
			$cobrosData = getCobrosPlnaesinternet("pendiente",$connect);
		break;
		case 3:
			$cobrosData = getCobrosPlnaesinternet("pendiente",$connect);
		break;

	}
	
			if($cobrosData)
			{
				while($contrato =  mysql_fetch_array($cobrosData)){
					$folio = $contrato['id_cobroPlaninternet'];
					$nombre = $contrato['text_nombre'];
					$cantidad = $contrato['float_resto'];
					$fechaCreacion = $contrato['date_fechaFinPeriodo'];
					$idPlanInternet = $contrato['id_planInternet'];
					$acciones = "";
					
					switch($contrato['text_status'])
					{
						case "pagado":
							$acciones = $acciones .'<p class="link"><a href="recibo.php?folio='.$folio.'" title="Ver recibo">Ver</a></p>';
						break;
						case "pendiente":
							$acciones = $acciones .'<p class="link"><a href="cobro.php?folio='.$folio.'&ses='."accept".'&c='.$isConected['idUser'].'" title="Cobrar">Cobrar</a></p>';
						break;
					}
					if($idPlanInternet==2 || $idPlanInternet==7)
					{
						//por ser Wi Fi se hace un ajuste de fecha
						$fechaWifi = new DateTime($fechaCreacion);
						$fechaWifi->modify("-1 month");
						//En caso de ser wifi se mostraran despues
						$contentCobrosWifi = $contentCobrosWifi.'
						<tr>
							<td>'.$folio.'</td>
							<td>'.$nombre.'</td>
							<td>'.$cantidad.'</td>
							<td>'.date_format($fechaWifi, 'Y-m-d').'</td>
							<td>'.$acciones.'</td>
						</tr>';
					}
					else
					{
						$contentCobros= $contentCobros.'
						<tr>
							<td>'.$folio.'</td>
							<td>'.$nombre.'</td>
							<td>'.$cantidad.'</td>
							<td>'.$fechaCreacion.'</td>
							<td>'.$acciones.'</td>
						</tr>';
					}

				}
				$tituloWifi="";
				if($contentCobrosWifi!="")
				{
					$tituloWifi='
					<tr>
					<td class="separador"></td>
					<td class="separador">Cobros Wi-Fi </td>
					<td class="separador"></td>
					<td class="separador"></td>
					<td class="separador"></td>
					</tr>
					';
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
					</thead>
					<tbody>
					'.
					$contentCobros
					.'
					'.
					$tituloWifi.
					$contentCobrosWifi
					.'
					<tbody>
					</tbody>
				</table>';
			}
		return $tableData;
	}


?>

<!DOCTYPE html>
<html>
<head>
	<title>vNova Internet:: Cobros</title>
	<script src="../js/jquery-1.7.1.js" type="text/javascript"></script>
	<script src="../js/prefixfree.min.js"  type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" href="../styles/estilos.css" media="screen">
	<link rel="stylesheet" type="text/css" href="../styles/cobros.css" media="screen">
	<link rel="stylesheet" type="text/css" href="../styles/clientsPrint.css" media="print">
	<script type="text/javascript">
    $(document).ready(function() 
    {

            //mark the first
            registrar();
                 
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
		
		//$("#encabezado").hide("slow");
		//$("#cuerpo").hide("slow");
		$("#pie").click(function(){
			//$("#encabezado").show("slow");
			//$("#cuerpo").show("slow");
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
				<a href="../" title="Inicio"><div id="logo"></div></a>
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
			<?php
				if($logged == true)
				{
			echo ($tableData);
				}
				else
				{

			//Se muestra formulario de acceso
			$mess ="";
			if($isConected['message']=='Datos incorrectos'){
				$mess ='<div class="errorLogIn">'.$isConected['message']."</div>";
			}
			echo('
			<div id="formLogInClient">
				<form id="formLogIn" action="'.$_SERVER['PHP_SELF'].'"'.' method="post">
			        <div id="User">Usuario:</div>
			        <input type="text" name="Nick" size="15" />
			        <div id="Pass">Contrase&ntilde;a:</div>
			        <input type="password" name="Pass" size="15" />
			        '.$mess.'
			        <input name="logIn" id="logIn" type="submit" value="Entrar" />
			    </form>
			</div>
			');

				}
			?>

		</div>
		<div class="lineBlank">&nbsp;</div>
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