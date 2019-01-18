<?php
include "db.php";

if(isset($_POST["btnguardar"])){
	$con = connect();
	$serviceName=$_POST['serviceName'];
	$barCode=$_POST['barCode'];
  $limitPay=$_POST['limitPay'];
	$serviceNum=$_POST['serviceNum'];
  $pay=$_POST['pay'];
  $orderGrup=$_POST['orderGrup'];
  $dateIntro=date("Y-m-d");
  $texStatus="-No-";
	
	$con->query("INSERT INTO Servicios (serviceName, barCode, serviceNum, pay, limitPay, dateIntro,texStatus, orderGrup) VALUES ('$serviceName', '$barCode', '$serviceNum','$pay','$limitPay','$dateIntro','$texStatus', '$orderGrup')");
	header("Location: index.php?option=ok");
}

?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">

    <title>Registrar Recibos - vNova Internet</title>
    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <!-- Bootstrap core CSS -->
    <link href="dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="assets/css/sticky-footer-navbar.css" rel="stylesheet">
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css" />
<script type="text/javascript">
$(document).ready(function () {
  var getBarCode;
  var getServiceNum;
  var getPay;
  var getDate;
  var contentString;
  var orderG;
  var texSta;
  var getName;

   (function($) {
       $('#getContenido').keyup(function () {
        contentString= String($(this).val());
        if(contentString.length==30)
        {
          //Validar la existencia previa
            getBarCode = contentString;
            getServiceNum = contentString.substring(2,14);
            getPay = parseInt(contentString.substring(20,29));
            getDate = "20"+contentString.substring(14,16)+"-"+contentString.substring(16,18)+"-"+contentString.substring(18,20);
            orderG=1;
            texSta="-No-";
           

            $("#SNUM" ).val(getServiceNum);
            $("#ORDE" ).val(orderG);
            $("#SPAY" ).val(getPay);
            $("#LIMI" ).val(getDate);
        }
       })     
      }(jQuery));
});
</script> 
  </head>

  <body>

    <header>
      <!-- Fixed navbar -->
      <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
        <a class="navbar-brand" href="index.php">RecibosComision</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
          <ul class="navbar-nav mr-auto">
 
            <li class="nav-item active">
              <a class="nav-link" href="actualizar.php">Actualizar <span class="sr-only">(current)</span></a>
              <a class="nav-link" href="registro.php">Registrar <span class="sr-only">(current)</span></a>
            </li>  
                  
          </ul>
          <form class="form-inline mt-2 mt-md-0">
            <input class="form-control mr-sm-2" type="text" placeholder="Buscar" aria-label="Search">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Busqueda</button>
          </form>
        </div>
      </nav>
    </header>

    <!-- Begin page content -->

<div class="container">
 <h1 class="mt-5">Registrar Recibo</h1>
 <hr>

<div class="row">
  <div class="col-12 col-md-6">

   <!-- Contenido --> 

<form id="frmLogin" action="" method="post">
  <fieldset>
        <div class="form-group">
    <label for="serviceName">Nombre:</label>
    <input id="NAME" required type="text" class="form-control" name="serviceName" placeholder="Nombre Como esta escrito en su recibo" value="">
 	   </div>
        <div class="form-group">
    <label for="barCode">Codigo de barras:</label>
    <input id="getContenido" required class="form-control" type="text" name="barCode"  placeholder="Codigo de barras" value="">
 	   </div>

        <div class="form-group">
    <label for="serviceNum">Numero de servicio:</label>
    <input id="SNUM" class="form-control" type="text" name="serviceNum"  placeholder="Numero de servicio 12 d" value="">
 	   </div>

     <div class="form-group">
    <label for="pay">Cantidad:</label>
    <input id="SPAY" class="form-control" type="text" name="pay"  placeholder="Monto" value="">
     </div>

     <div class="form-group">
    <label for="limitPay">Fecha de vencimiento:</label>
    <input id="LIMI" class="form-control" type="text" name="limitPay"  placeholder="Vencimiento" value="">
     </div>
       
       <div class="form-group">
    <label for="pay">Grupo:</label>
    <input id="ORDE" class="form-control" type="text" name="orderGrup"  placeholder="Cantidad a pagar" value="1">
     </div>
       
       
    <input type="hidden" name="btnguardar" value="v">
<input class="btn btn-primary" type="submit" value="Registrar recibo">
             
  </fieldset>

</form>

 <!-- Fin Contenido --> 
</div>
</div><!-- Fin row -->


</div><!-- Fin container -->
    <footer class="footer">
      <div class="container">
        <span class="text-muted"><p>Ayuda <a href="https://www.google.com/" target="_blank">vNova Internet</a></p></span>
      </div>
    </footer>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
 
    <script>window.jQuery || document.write('<script src="assets/js/vendor/jquery-slim.min.js"><\/script>')</script>
    <script src="assets/js/vendor/popper.min.js"></script>
    <script src="dist/js/bootstrap.min.js"></script>
  </body>
</html>