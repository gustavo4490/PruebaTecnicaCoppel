<?php
require_once "conexion/conexion.php";
require_once "respuestas.class.php";


class entregas extends conexion
{

    private $idTrabajador = "";
    private $precioEntrega = "";
    private $cantidadEntrega = "";
    private $fecha = "";



    /**
     * Esta función devuelve listado de todas las entregas, espera el numero de pagina ya que solo trae 100 registros
     * @var pagina int
     * @access public
     * @return array
     */
    public function listaEntregas($pagina = 1)
    {
        $inicio  = 0;
        $cantidad = 100;
        if ($pagina > 1) {
            $inicio = ($cantidad * ($pagina - 1)) + 1;
            $cantidad = $cantidad * $pagina;
        }
        $query = "SELECT entregas.idEntrega,trabajadores.nombreCompleto,entregas.precioEntrega,entregas.cantidadEntregas,entregas.fecha 
        FROM entregas 
        INNER JOIN trabajadores ON entregas.idTrabajador = trabajadores.idTrabajador" . " limit $inicio,$cantidad;";


        $datos = parent::obtenerDatos($query);
        return ($datos);
    }


    /**
     * Esta función devuelve las entregas por trabajador, espera el Id del trabajador
     * @var idTrabajador int
     * @access public
     * @return array
     */
    public function obtenerEntregasPorTrabajador($idTrabajador)
    {

        $query = "SELECT entregas.idEntrega,trabajadores.nombreCompleto,entregas.precioEntrega,entregas.cantidadEntregas,entregas.fecha 
        FROM entregas 
        INNER JOIN trabajadores ON entregas.idTrabajador = trabajadores.idTrabajador WHERE entregas.idTrabajador = '$idTrabajador'";

        return parent::obtenerDatos($query);
    }

    /**
     * Esta función guarda entregas utilizando verbo post.
     * @var idTrabajador
     * @var p_precioEntrega
     * @var p_cantidadEntregas
     * @var p_fecha
     * @access public
     * @return array
     */

    public function post($json)
    {
        $_respuestas = new respuestas;
        $datos = json_decode($json, true);

        if ($datos === null) {
            return $_respuestas->error_400("JSON inválido");
        }

        $requiredFields = array('idTrabajador', 'precioEntrega', 'cantidadEntrega', 'fecha');
        foreach ($requiredFields as $field) {
            if (!isset($datos[$field])) {
                return $_respuestas->error_400("El campo '$field' es obligatorio");
            }
        }

        $this->idTrabajador = $datos['idTrabajador'];
        $this->precioEntrega = $datos['precioEntrega'];
        $this->cantidadEntrega = $datos['cantidadEntrega'];
        $this->fecha = $datos['fecha'];


        if (!is_string($this->precioEntrega) || !is_string($this->cantidadEntrega) || !is_string($this->fecha)) {
            return $_respuestas->error_400("Los campos precioEntrega, cantidadEntrega y fecha deben ser de tipo string");
        }

        if (!is_int($this->idTrabajador)) {
            return $_respuestas->error_400("El campo idTrabajador  debe ser de tipo entero");
        }

        $resp = $this->insertarEntregas();
        if ($resp) {
            $respuesta = $_respuestas->ok_200_procedimientos_almacenados('Datos almacenados correctamente');
            return $respuesta;
        } else {
            return $_respuestas->error_500();
        }
    }

    /**
     * Esta función inserta a la base de datos las entregas .
     * @access private
     * @return boolen
     */

    private function insertarEntregas()
    {

        $params = array(
            //s= string , i = int
            array('type' => 'i', 'value' => $this->idTrabajador),
            array('type' => 's', 'value' => $this->precioEntrega),
            array('type' => 's', 'value' => $this->cantidadEntrega),
            array('type' => 's', 'value' => $this->fecha)

        );
        $result = $this->executeStoredProcedure('InsertarEntrega', $params);
        if ($result) {
            // echo 'El procedimiento se ejecutó correctamente.';
            return true;
        } else {
            // echo 'Ocurrió un error al ejecutar el procedimiento.';
            return false;
        }
    }
}
