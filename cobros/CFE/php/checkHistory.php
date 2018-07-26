<?php 
//get data

//set header
header('Content-Type: application/json');

//sen json
$datos = array(
'nombre' => "Hugo"
);
//Devolvemos el array pasado a JSON como objeto
echo json_encode($datos, JSON_FORCE_OBJECT);

 ?>