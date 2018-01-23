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

?>

<!DOCTYPE html>
<html>
<head>
	<title>vNova Internet:: Cobros</title>
	<script src="../js/jquery-1.7.1.js" type="text/javascript"></script>
	<script src="../js/prefixfree.min.js"  type="text/javascript"></script>
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>



	<link rel="stylesheet" type="text/css" href="../styles/estilos.css" media="screen">
	<link rel="stylesheet" type="text/css" href="../styles/cobros.css" media="screen">
	<link rel="stylesheet" type="text/css" href="../styles/sello.css" media="screen">
	<link rel="stylesheet" type="text/css" href="../styles/clientsPrint.css" media="print">
	<link rel="stylesheet" type="text/css" href="../styles/sello.css" media="print">
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
					$datePayVar=date_format(new DateTime(),'d / m / Y');
					$timePayVar=date_format(new DateTime(),'H:i:s');

					echo('<div class="selo">');
					echo('
				<div class="contentPay">
					<div class="circlePay"></div>
					<div class="logoPay"></div>
					<div class="datePay"><p>Fecha:'.$datePayVar.'</p></div>
	                <div class="timePay">'.$timePayVar.'</div>
	                <div class="dataPayDom">Calle del Jardin #4</div>
	                <div class="dataPayLoc">Santa Catarina Villanueva</div>
	                <div class="dataPaySta">Quecholac, Puebla.</div>
	            </div>');

					echo('</div>');
						
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