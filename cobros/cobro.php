<?php
//include 'php/sessionManager.php';
include('../php/conectorBD.php');
include('../php/DairyCheckDB.php');
include('php/helper.php'); 	
$connect=conectar();


$logged = false;
	if($_GET['ses']=='accept'){
		$logged = true;
		$type=$_GET['type'];
	}
$mess = "";
$cobro = false;

if($_POST['logIn']=='Cobrar')
{
	
	$isEmploy = array('isLogIn' => false, 'userName'  => '', 'idUser' => -1, 'message' => "");
	$isEmploy = logInEmploy($_POST['employ'], $_POST['Pass'] ,$connect);

	$type=$_POST['type'];
	if($isEmploy['isLogIn']){
		if($type=="servicio")
		{
			$cobro = realizaPagoServicio($connect,$_POST['monto'],$_POST['pendiente'],$isEmploy['idUser'],$_POST['folio'],'Efectivo','Recibo');

		}
		else
		{
			$cobro = realizaPago($connect,$_POST['monto'],$_POST['pendiente'],$isEmploy['idUser'],$_POST['folio'],'Efectivo','Recibo');
		}
		if($cobro){
			$mess ='<div>'."Pago procesado correctamente"."</div>";
			//envio a ver el recibo
			//Envio un mail de aviso de pago
			$type=$_POST['type'];
			if($type=="servicio")
			{
				sendAlertCobroServicio("bairon_2002@hotmail.com, monikahlm@hotmail.com",$connect,$isEmploy['idUser'],$_POST['folio']);
			}
			else
			{
				sendAlertCobro("bairon_2002@hotmail.com, monikahlm@hotmail.com",$connect,$isEmploy['idUser'],$_POST['folio']);
			}
		}
		else{
			$mess ='<div>'."Pago no procesado correctamente, por favor intentar una vez mas"."</div>";	
		}
	}
	
	
	if(!$cobro)
	{
		$mess ='<div class="errorLogIn">'."No se ha realizado el pago"."</div>";
	}
}

//Agregamos una funcion para enviar aviso de cobro
function sendAlertCobro($des,$connect,$idUser,$folio)
{
	$dataCobro = getDataCobro($connect, $idUser, $folio);
	$fecha=date('d-m-Y h:i:s');
	$subject = 'Cobro Internet vNovaInternet';
	$message = '';
	$message = '
		<html>
		<head>
		<title>Se ha realizado el cobro de un servico</title>
		</head>
		<body>
		<p>'.'El siguiente cajero ha realizado un cobro:</p>
		<h4>'.$dataCobro["cajero"].'</h4>
		<p>
		Por un monto de:
		'.$dataCobro["monto"].'
		</p>
		<p>'.'El concepto del pago es:</p>
		<h4> <a href="http://www.vnova.santacatarinavillanueva.com/cobros/recibo.php?folio='.$folio.'">'.$folio.'</a></h4>
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
function sendAlertCobroServicio($des,$connect,$idUser,$folio)
{
	$dataCobro = getDataCobro($connect, $idUser, $folio);
	$fecha=date('d-m-Y h:i:s');
	$subject = 'Cobro Servicios vNovaInternet ';
	$message = '';
	$message = '
		<html>
		<head>
		<title>Se ha realizado el cobro de un servico</title>
		</head>
		<body>
		<p>'.'El siguiente cajero ha realizado un cobro:</p>
		<h4>'.$dataCobro["cajero"].'</h4>
		<p>
		Por un monto de:
		'.$dataCobro["monto"].'
		</p>
		<p>'.'El concepto del pago es:</p>
		<h4> <a href="http://www.vnova.santacatarinavillanueva.com/cobros/reciboServicio.php?folio='.$folio.'">'.$folio.'</a></h4>
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

<!DOCTYPE html>
<html>
<head>
	<title>vNova Internet:: Cobrar</title>
	<script src="../js/jquery-1.7.1.js" type="text/javascript"></script>
	<script src="../js/prefixfree.min.js"  type="text/javascript"></script>
	<link href="../styles/estilos.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" type="text/css" href="../styles/cobros.css" media="screen">
	<link rel="stylesheet" type="text/css" href="../styles/clients.css" media="screen">
	<script type="text/javascript">
        $(function () {
            $('input[type=password]').val("");
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
				<div id="formInit">
				<a href="index.php">Regresar</a>
				</div>
			</div>
		</div>
	</div>
	<div id="cuerpo">
		<div class="lineBlank">&nbsp;</div>
		<div id="content">
			<?php
				if($cobro){
					$type=$_POST['type'];
					echo '<div>'.'<p class="thanks">El pago ha sido aplicado exitosamente </p>'."</div>";
 
				//if($status=="pagado"){ 
					/*Pendiente*/
					/*Redirigir solo a un recibo parcial en caso de no hacer pagos completos*/
					$ref='recibo.php?folio='.$_POST['folio'];
					if($type=="servicio")
					{
						$ref='reciboServicio.php?folio='.$_POST['folio'];
					}
					else
					{
						if((int)$_POST['monto'] != (int)$_POST['pendiente'])
						{
							$ref='notaRemision.php?folio='.$_POST['folio'].'&pago='.$_POST['monto'];
						}
					}
					echo '<div id="printButton">'
					.'<p><a href="'.$ref.'" title="Ver recibo">Ver comprobante</a></p>'.'
					</div>';
				//} 

					//echo '<div class="botonOp">'.'<p><a href="recibo.php?folio='.$_POST['folio'].'" title="Ver recibo">Ver recibo</a></p>'."</div>";

				}
				else if($logged == true)
				{
					$type=$_GET['type'];
					//Pago exito
					//Se muestra formulario de acceso
					if($type=="servicio")
					{
						$dataRecibo = getReciboServicio($_GET['folio'],$connect);
					}
					else
					{
						$dataRecibo = getRecibo($_GET['folio'],$connect);
					}
					
					if($dataRecibo){
						$recibo =  mysql_fetch_array($dataRecibo);
					}
			echo('
			<div id="formLogInClient">
				<div>Cliente: '.$recibo ['text_nombre'].'</div>
				<form id="formLogIn" action="'.$_SERVER['PHP_SELF'].'"'.' method="post">
					<div>Total a pagar: </div>
			        <input type="text" name="monto" size="15" value="'.$recibo ['saldo'].'"/>
			        <br>
			        <div>Para confirmar el pago, introduce contrase&ntilde;a y presiona "Cobrar" </div>
			        <input type="password" name="Pass" size="15" />
			        '.$mess.'
			        <input type="hidden" name="folio" value="'.$_GET['folio'].'">
			        <input type="hidden" name="employ" value="'.$_GET['c'].'">
			        <input type="hidden" name="pendiente" value="'.$recibo ['saldo'].'">
			        <input type="hidden" name="type" value="'.$_GET ['type'].'">
			        <input name="logIn" id="logIn" type="submit" value="Cobrar" />
			    </form>
			</div>
			');
				}
				else if($logged==false)
				{
					echo '<div id="User">Error en los datos </div>';
					echo '<div id="printButton">'
					.'<p><a href="../cobros" title="Ver recibo">Regresar</a></p>'.'
					</div>';
					//No ha recibido la informacion correcta para ingresar

				}
				else
				{
					echo '<div id="User">El pago que desea realizar no existe</div>';
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