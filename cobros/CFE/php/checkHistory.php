<?php 
//get data
$servicio = json_decode($_POST['servicio']);
//set header
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
if($method == "OPTIONS") {
	die();
}
if(isset($servicio))
{
	if (!empty($servicio)) {
		//send json
        $datos = array(
		'servicio' => $servicio,
		'nombre' => "Hugo"
		);
    }
    else
	{
		$datos = array('h':12
		);

	}
}
else
{
	$datos = array('m':15
	);

}
//Devolvemos el array pasado a JSON como objeto
echo json_encode($datos, JSON_FORCE_OBJECT);

 ?>