<?php
//include 'php/sessionManager.php';
/*
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
$tableData = tableAdeudosClientes($connect,$isConected);
function tableAdeudosClientes($connect,$isConected)
{
	$clientList = false;
	$tableServicios="no data";
	$comand="";

	switch($isConected['priv'])
	{
		case 0:
			$clientList = getClientes($connect,"all");
			$comand="all";
		break;
		case 1:
			$clientList = getClientes($connect,"all");
			$comand="pendienteypagado";
		break;
		case 2:
			$clientList = getClientes($connect,"activo");
			$comand="pendiente";
		break;
		case 3:
			$clientList = getClientes($connect,"activo");
			$comand="pendiente";
		break;
	}
	
	if($clientList)
	{
		//listamos todos los clientes
		while($clienteFila =  mysql_fetch_array($clientList))
		{
			$idCliente = $clienteFila['id_cliente'];
			$nombreCliente = $clienteFila['text_nombre'];
			$servicios=false;
			$internet=false;
			$saldo=0;
			$hasAdeudo = hasAdeudo($connect,$idCliente) ;
			$tableAdeudos="";
			if( $hasAdeudo )
			{	
				$serviciosList=getSaldoServicios($connect,$idCliente);
				while($saldoFila =  mysql_fetch_array($serviciosList))
				{
					$servicios=true;
					$saldo = $saldo + $saldoFila['saldo'];
				}
				$tableAdeudos=$tableAdeudos.tableDataServiceByClient($idCliente,$comand,$connect,$isConected);
			}
			$hasContratosInternet = hasContratosInternet($connect,$idCliente);
			$tableAdeudosInternet="";
			if( $hasContratosInternet )
			{	
				while($contratoFila =  mysql_fetch_array($hasContratosInternet))
				{
						$internet=true;
						$contrato=$contratoFila['id_contratoInternet'];
						//obtenemos el saldo total de los recibos acumulados
						$internetList=getSaldoInternet($connect,$contrato);
						while($saldoFila =  mysql_fetch_array($internetList))
						{
							$internet=true;
							$saldo = $saldo + $saldoFila['saldo'];
						}
						$tableAdeudosInternet=$tableAdeudosInternet.tableDataByContrato($contrato,$comand,$connect,$isConected);
				}
			}
			if($tableAdeudos!="" || $tableAdeudosInternet!="" )
			{
				$contentServicios= $contentServicios.'
				<h3 class="group">
				<ul>
					<li class="left">'.$nombreCliente.'</li>
					<li> $ &nbsp;'.$saldo.'</li>
					<li>'.'-- -- ----'.'</li>
					<li>'.'Acciones'.'</li>
				</ul>
				</h3>
				<div>
					'.
					$tableAdeudos.
					$tableAdeudosInternet.
				'</div>';

				//$tableServicios=$tableServicios.tableDataServiceByClient($idCliente,$comand,$connect,$isConected);
			}
		}
	}
	$tableData='
			<ul>
				<li>Cliente</li>
				<li>Cantidad</li>
				<li>Fecha de corte</li>
				<li>Opciones</li>
			</ul>
			<div id="saldos">'.
				$contentServicios
			.'
			</div>';
		return $tableData;
}
//$tableData= tableAdeudosAcumulados($connect,$isConected);
function tableAdeudosAcumulados($connect,$isConected)
{
	$cobrosData= false;
	$contentCobros="";
	$contentCobrosWifi="";
	$comand="pendiente";

	switch($isConected['priv'])
	{
		case 0:
			$cobrosPendientesSumas=getCobrosClientesSumados("all",$connect);
			$comand="all";
		break;
		case 1:
			$cobrosPendientesSumas=getCobrosClientesSumados("pendienteypagado",$connect);
			$comand="pendienteypagado";
		break;
		case 2:
			$cobrosPendientesSumas=getCobrosClientesSumados("pendiente",$connect);
			$comand="pendiente";
		break;
		case 3:
			$cobrosPendientesSumas=getCobrosClientesSumados("pendiente",$connect);
			$comand="pendiente";
		break;

	}
	//obtenemos clientes activos
		//revisamos si cada cliente tiene adeudos por servicios
			//... totalServicios
		//revisamos si cada cliente tiene adeudos por servicio de internet activos
			//... totalInternet
		//obntenemos suma de adeudos	

	if($cobrosPendientesSumas)
	{
		while($contratoFila =  mysql_fetch_array($cobrosPendientesSumas))
		{
			$contrato = $contratoFila['contrato'];
			$cliente = $contratoFila['cliente'];
			$saldo = $contratoFila['saldo'];
			$fechaCorte = new DateTime($contratoFila['fechaCorte']);
			$plan = $contratoFila['plan'];
			$periodos = "Ver ".(int)$contratoFila['periodos']." recibo(s)";
			$acciones = "Ver";


					
			//$acciones = '<p class="link"><a href="cobro.php?folio='.$contrato.'&ses='."accept".'&c='.$isConected['idUser'].'" title="Ver">Ver</a></p>';
				
			if($plan==2 || $plan==7)
			{
				//por ser Wi Fi se hace un ajuste de fecha
				$fechaWifi = $fechaCorte;
				//$fechaWifi->modify("-1 month");
				//En caso de ser wifi se mostraran despues
				$contentCobrosWifi = $contentCobrosWifi.'
						<h3 class="group">
						<ul>
							<li>'.$cliente.'</li>
							<li>$ &nbsp;'.$saldo.'</li>
							<li>'.date_format($fechaWifi, 'd-m-Y').'</li>
							<li>'.$periodos.'</li>
						</ul>
						</h3>
						<div>'.
							tableDataByContrato($contrato,$comand,$connect,$isConected).'
						</div>';
			}
			else
			{
			$contentCobros= $contentCobros.'
				<h3 class="group">
				<ul>
					<li>'.$cliente.'</li>
					<li> $ &nbsp;'.$saldo.'</li>
					<li>'.date_format($fechaCorte,'d-m-Y').'</li>
					<li>'.$periodos.'</li>
				</ul>
				</h3>
				<div>
					'.
					tableDataByContrato($contrato,$comand,$connect,$isConected).'
				</div>';
			}

		}

		$tableData='
			<ul>
				<li>Cliente</li>
				<li>Cantidad</li>
				<li>Fecha de corte</li>
				<li>Opciones</li>
			</ul>
			<div id="saldos">
					'.
				$contentCobros
			.
				$contentCobrosWifi
					.'
			</div>';
		}
		return $tableData;
}
function tableDataByContrato($contrato,$comand,$connect,$isConected)
{
	$contentTable="";
	$cobrosPendientes=getCobroPlaninternet($contrato,$comand,$connect);
	if($cobrosPendientes)
	{

		while($cobrosFila =  mysql_fetch_array($cobrosPendientes))
		{
			$folio = $cobrosFila['id_cobroPlaninternet'];
			$cantidad = $cobrosFila['float_resto'];
			$fechaCreacion = new DateTime($cobrosFila['date_fechaFinPeriodo']);
			$idPlanInternet = $cobrosFila['id_planInternet'];
			$acciones = "";
			switch($cobrosFila['text_status'])
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
				$fechaWifi = $fechaCreacion;
				$fechaWifi->modify("-1 month");
				//En caso de ser wifi se mostraran despues
				$contentCobrosWifi = $contentCobrosWifi.'
				<ul class="filaAdeudo">
					<li>'.$folio.'</li>
					<li> $ &nbsp;'.$cantidad.'</li>
					<li>'.date_format($fechaWifi, 'd-m-Y').'</li>
					<li>'.$acciones.'</li>
				</ul>';
			}
			else
			{
				$contentCobro=$contentCobro.'
				<ul class="filaAdeudo">
					<li>'.$folio.'</li>
					<li>$ &nbsp;'.$cantidad.'</li>
					<li>'.date_format($fechaCreacion, 'd-m-Y').'</li>
					<li>'.$acciones.'</li>
				</ul>';
			}
			$contentTable='
			<div>'.
					$contentCobro
					.
					$contentCobrosWifi.
			'</div>';
		}
	}
	return $contentTable;
}
function tableDataServiceByClient($cliente,$comand,$connect,$isConected)
{
	$contentTable="";
	$cobrosServiciosPendientes=getCobroServicios($cliente,$comand,$connect);
	if($cobrosServiciosPendientes)
	{

		while($cobrosFila =  mysql_fetch_array($cobrosServiciosPendientes))
		{
			$folio = $cobrosFila['id_cobroServicio'];
			$cantidad = $cobrosFila['float_resto'];
			$fechaCreacion = new DateTime($cobrosFila['date_fechaCreacion']);
			$id_cobroServicio = $cobrosFila['id_cobroServicio'];
			$acciones = "";
			switch($cobrosFila['text_status'])
			{
				case "pagado":
					$acciones = $acciones .'<p class="link"><a href="reciboServicio.php?folio='.$folio.'" title="Ver recibo">Ver</a></p>';
				break;
				case "pendiente":
					$acciones = $acciones .'<p class="link"><a href="cobro.php?folio='.$folio.'&type=servicio&ses='."accept".'&c='.$isConected['idUser'].'" title="Cobrar">Cobrar</a></p>';
				break;
			}
				$contentCobro=$contentCobro.'
				<ul class="filaAdeudo">
					<li>'.$folio.'</li>
					<li>$ &nbsp;'.$cantidad.'</li>
					<li>'.date_format($fechaCreacion, 'd-m-Y').'</li>
					<li>'.$acciones.'</li>
				</ul>';
			$contentTable='
			<div>'.
					$contentCobro
					.
			'</div>';
		}
	}
	return $contentTable;
}
*/
?>

<!DOCTYPE html>
<html>
<head>
	<title>vNova Internet:: Cobros</title>
	<script src="../js/jquery-1.7.1.js" type="text/javascript"></script>
	<script src="../js/prefixfree.min.js"  type="text/javascript"></script>
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
	<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>


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
		$( "#saldos" ).accordion({
	    	heightStyle: "content"
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