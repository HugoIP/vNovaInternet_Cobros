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

$clientList = getClientes($connect,"activo");

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
        <tr>
          <td class="left">'.$nombreCliente.'</td>
          <td> $ &nbsp;'.$saldo.'</td>
          <td>'.'-- -- ----'.'</td>
          <td>'.'Acciones'.'</td>
        </tr>
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
      <tr>
        <td>Cliente</td>
        <td>Cantidad</td>
        <td>Fecha de corte</td>
        <td>Opciones</td>
      </tr>
      <div id="saldos">'.
        $contentServicios
      .'
      </div>';
echo ($tableData):

//$isConected = array('isLogIn' => true, 'userName'  => 'HugoIP', 'idUser' => -1, 'message' => "",'priv' => 3);
//$tableData = tableAdeudosClientes($connect,$isConected);

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
        <tr>
          <td class="left">'.$nombreCliente.'</td>
          <td> $ &nbsp;'.$saldo.'</td>
          <td>'.'-- -- ----'.'</td>
          <td>'.'Acciones'.'</td>
        </tr>
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
      <tr>
        <td>Cliente</td>
        <td>Cantidad</td>
        <td>Fecha de corte</td>
        <td>Opciones</td>
      </tr>
      <div id="saldos">'.
        $contentServicios
      .'
      </div>';
    return $tableData;
}
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
        /*$fechaWifi = $fechaCorte;*/
        //$fechaWifi->modify("-1 month");
        //En caso de ser wifi se mostraran despues
        /*$contentCobrosWifi = $contentCobrosWifi.'
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
            </div>';*/
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