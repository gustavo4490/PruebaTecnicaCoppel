<?php
require_once 'clases/respuestas.class.php';
require_once 'clases/entregas.class.php';

$_respuestas = new respuestas;
$_entregas = new entregas;

// Habilita CORS para permitir solicitudes desde cualquier origen
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");

if($_SERVER['REQUEST_METHOD'] == "GET"){

    if(isset($_GET["page"])){
        $pagina = $_GET["page"];
        $listaEntregas = $_entregas->listaEntregas($pagina);
        header("Content-Type: application/json");
        echo json_encode($listaEntregas);
        http_response_code(200);
    }else if(isset($_GET['id'])){
        $IdTrabajador = $_GET['id'];
        $datosTrabajador = $_entregas->obtenerEntregasPorTrabajador($IdTrabajador);
        header("Content-Type: application/json");
        echo json_encode($datosTrabajador);
        http_response_code(200);
    }
    else{
        header('Content-Type: application/json');
        echo json_encode($_respuestas->error_400());

    }
    
}else if($_SERVER['REQUEST_METHOD'] == "POST"){
    //recibimos los datos enviados
    $postBody = file_get_contents("php://input");
    //enviamos los datos al manejador
    $datosArray = $_entregas->post($postBody);
    //delvovemos una respuesta 
     header('Content-Type: application/json');
     if(isset($datosArray["result"]["error_id"])){
         $responseCode = $datosArray["result"]["error_id"];
         http_response_code($responseCode);
     }else{
         http_response_code(200);
     }
     echo json_encode($datosArray);
    
}else{
    header('Content-Type: application/json');
    $datosArray = $_respuestas->error_405();
    echo json_encode($datosArray);
}
?>
