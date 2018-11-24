<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">
<title>Recibos</title>
<script src="http://code.jquery.com/jquery-2.1.4.min.js" type="text/javascript"></script>
<!-- Bootstrap core CSS -->
<link href="dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Custom styles for this template -->
<link href="assets/css/sticky-footer-navbar.css" rel="stylesheet">
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css" />
<script type="text/javascript">
$(document).ready(function () {
   (function($) {
       $('#FiltrarContenido').keyup(function () {
            var ValorBusqueda = new RegExp($(this).val(), 'i');
            $('.BusquedaRapida tr').hide();
             $('.BusquedaRapida tr').filter(function () {
                return ValorBusqueda.test($(this).text());
              }).show();
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
            <a class="nav-link" href="entregar.php">Entregar <span class="sr-only">(current)</span></a>
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
 <h1 class="mt-5">Busqueda</h1>
 <hr>
<?php
if(isset($_GET["option"])){?>
 <div class="alert alert-success" role="alert">
  <strong>Hecho!</strong> El registro ha sido guardado con exito.
</div>
<?php }?>
<div class="row">
  <div class="col-12 col-md-12">

   <!-- Contenido -->    
			

<div class="input-group mb-3">
  <div class="input-group-prepend">
    <span class="input-group-text" id="basic-addon1">Buscar</span>
  </div>
  <input id="FiltrarContenido" type="text" class="form-control" placeholder="Ingrese Nombre del servicio" aria-label="Alumno" aria-describedby="basic-addon1">
</div>
	    <table class="table table-hover">
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Codigo de barras</th>
            <th>Servicio</th>            
            <th>Monto</th>
            <th>Vence</th>
            <th>Grupo</th>
            <th>Entregado</th>
          </tr>
        </thead>
        <tbody class="BusquedaRapida">
<?php
include "db.php";
include('helper.php');  

$connect = connect();


  $response = false;

  $sql="SELECT * FROM CobrosPlanesinternet WHERE 0";
  //Table: CobrosPlanesinternet
  //`id_contratoInternet` ,`id_cliente` ,`id_planesInternet` ,`date_fechaInicio` ,`date_fechaFinPeriodo` ,`int_periodo` ,`text_observaciones`

    //$sql="SELECT * FROM CobrosPlanesinternet WHERE `CobrosPlanesinternet`.`text_status` = 'pendiente'";
    $sql="SELECT`CobrosPlanesinternet`.* ,`clientes`.`text_nombre`, `ClientePlanesinternet`.`id_planInternet`
FROM`CobrosPlanesinternet` ,`clientes` ,`ClientePlanesinternet` 
WHERE`clientes`.`id_cliente`=`ClientePlanesinternet`.`id_cliente` 
AND`CobrosPlanesinternet`.`id_contratoInternet`=`ClientePlanesinternet`.`id_contratoInternet`
ORDER BY `CobrosPlanesinternet`.`date_FechaFinPeriodo`DESC";



  
  $result = mysqli_query($sql,$connect);

  $contador=0;

while($misdatos = mysqli_fetch_assoc($result)){ $contador++;?>
<tr>
  <td><?php echo $misdatos["text_nombre"]; ?></td>
  <td><?php echo $misdatos["id_planInternet"]; ?></td>
  <td><?php echo $misdatos["text_status"]; ?></td>
  <td><?php echo $misdatos["float_monto"]; ?></td>
  <td><?php echo $misdatos["id_cobroPlaninternet"]; ?></td>
  </tr>


?>          

</tbody>
      </table>		
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
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="assets/js/vendor/jquery-slim.min.js"><\/script>')</script>
    <script src="assets/js/vendor/popper.min.js"></script>
    <script src="dist/js/bootstrap.min.js"></script>
    
  </body>
</html>