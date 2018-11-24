<!DOCTYPE html>
<html>
<head>
	<title>vNova Internet:: Cobros</title>
	<script src="../js/jquery-1.7.1.js" type="text/javascript"></script>
	<script src="../js/prefixfree.min.js"  type="text/javascript"></script>
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>



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
					echo "tesy";
				?>
			</div>
		</div>
	</div>
	<div id="cuerpo">
		<div class="lineBlank">&nbsp;</div>
		<div id="content">
			

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