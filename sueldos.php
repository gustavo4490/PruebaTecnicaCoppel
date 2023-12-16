<?php
require_once 'clases/respuestas.class.php';
require_once 'clases/sueldos.class.php';

$_respuestas = new respuestas;
$_sueldo = new sueldo;


if ($_SERVER['REQUEST_METHOD'] == "GET") {

    if (isset($_GET["page"])) {
        $pagina = $_GET["page"];
        $listaSueldos = $_sueldo->listaTodosLosSueldos($pagina);
        header("Content-Type: application/json");
        echo json_encode($listaSueldos);
        http_response_code(200);
    } else if (isset($_GET['id'])) {
        $IdTrabajador = $_GET['id'];
        $datosTrabajador = $_sueldo->obtenerSueldoPorID($IdTrabajador);
        header("Content-Type: application/json");
        echo json_encode($datosTrabajador);
        http_response_code(200);
    }
} else if ($_SERVER['REQUEST_METHOD'] == "POST") {
    //recibimos los datos enviados
    $postBody = file_get_contents("php://input");
    //enviamos los datos al manejador
    $datosArray = $_sueldo->calcularSueldo($postBody);
    //delvovemos una respuesta 
    header('Content-Type: application/json');
    if (isset($datosArray["result"]["error_id"])) {
        $responseCode = $datosArray["result"]["error_id"];
        http_response_code($responseCode);
    } else {
        http_response_code(200);
    }
    echo json_encode($datosArray);
} else if($_SERVER['REQUEST_METHOD'] == "DELETE"){

    $headers = getallheaders();
    if(isset($headers["idSueldo"])){
        //recibimos los datos enviados por el header
        $send = [
            "idSueldo" =>$headers["idSueldo"]
        ];
        $postBody = json_encode($send);
    }else{
        //recibimos los datos enviados
        $postBody = file_get_contents("php://input");
    }
    
    //enviamos datos al manejador
    $datosArray = $_sueldo->delete($postBody);
    //delvovemos una respuesta 
    header('Content-Type: application/json');
    if(isset($datosArray["result"]["error_id"])){
        $responseCode = $datosArray["result"]["error_id"];
        http_response_code($responseCode);
    }else{
        http_response_code(200);
    }
    echo json_encode($datosArray);
   
} else {
    header('Content-Type: application/json');
    $datosArray = $_respuestas->error_405();
    echo json_encode($datosArray);
}
