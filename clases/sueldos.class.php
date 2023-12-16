<?php
require_once "conexion/conexion.php";
require_once "respuestas.class.php";


class sueldo extends conexion
{



    private $idSueldo = "";
    private $idTrabajador = "";
    private $mes = "";
    private $year = "";





    /**
     * Esta función devuelve listado todos los sueldos, espera el numero de pagina ya que solo trae 100 registros
     * @var pagina int
     * @access public
     * @return array
     */
    public function listaTodosLosSueldos($pagina = 1)
    {
        $inicio  = 0;
        $cantidad = 100;
        if ($pagina > 1) {
            $inicio = ($cantidad * ($pagina - 1)) + 1;
            $cantidad = $cantidad * $pagina;
        }
        $query = "SELECT sueldos.idSueldo,trabajadores.nombreCompleto,sueldos.totalSalarioBase,sueldos.totalBono,sueldos.totalEntregas,sueldos.sueldoBruto,sueldos.sueldoNeto,sueldos.totalValesDespensa,sueldos.SalarioFinaldecimal,sueldos.mesSalario,sueldos.año
        FROM sueldos 
        INNER JOIN trabajadores ON trabajadores.idTrabajador = sueldos.idTrabajador" . " limit $inicio,$cantidad";
        $datos = parent::obtenerDatos($query);
        return ($datos);
    }

    /**
     * Esta función devuelve datos de la tabla sueldos del trabajador por id, espera el Id del trabajador
     * @var idTrabajador int
     * @access public
     * @return array
     */
    public function obtenerSueldoPorID($idTrabajador)
    {

        $query = " SELECT sueldos.idSueldo,trabajadores.nombreCompleto,sueldos.totalSalarioBase,sueldos.totalBono,sueldos.totalEntregas,sueldos.sueldoBruto,sueldos.sueldoNeto,sueldos.totalValesDespensa,sueldos.SalarioFinaldecimal,sueldos.mesSalario,sueldos.año
                FROM sueldos 
                INNER JOIN trabajadores ON trabajadores.idTrabajador = sueldos.idTrabajador WHERE sueldos.idTrabajador =  '$idTrabajador'";

        return parent::obtenerDatos($query);
    }

    /**
     * Esta función calcula el sueldo del trabajador por medio de id, utilizando verbo post.
     * @var trabajadorId
     * @var mes
     * @var SalarioYear
     * @access public
     * @return array
     */

    public function calcularSueldo($json)
    {
        $_respuestas = new respuestas;
        $datos = json_decode($json, true);

        if ($datos === null) {
            return $_respuestas->error_400("JSON inválido");
        }

        $requiredFields = array('idTrabajador', 'mes', 'year');
        foreach ($requiredFields as $field) {
            if (!isset($datos[$field])) {
                return $_respuestas->error_400("El campo '$field' es obligatorio");
            }
        }

        $this->idTrabajador = $datos['idTrabajador'];
        $this->mes = $datos['mes'];
        $this->year = $datos['year'];

        
        if (!is_int($this->idTrabajador) || !is_int($this->mes) || !is_int($this->year)  ) {
            return $_respuestas->error_400("Los campors idTrabajador, mes y year  deben ser de tipo entero");
        }

        $resp = $this->insertarSalario();
        if ($resp) {
            $respuesta = $_respuestas->ok_200_procedimientos_almacenados('Datos almacenados correctamente');
            return $respuesta;
        } else {
            return $_respuestas->error_500();
        }
    }

    /**
     * Esta función inserta a la base de datos en la tabla sueldos .
     * @access private
     * @return boolen
     */

    private function insertarSalario()
    {

        $params = array(
            //s= string , i = int

            
            array('type' => 'i', 'value' => $this->idTrabajador),
            array('type' => 'i', 'value' => $this->mes),
            array('type' => 'i', 'value' => $this->year)

        );
        $result = $this->executeStoredProcedure('calcularSueldoPorId', $params);
        if ($result) {
            // echo 'El procedimiento se ejecutó correctamente.';
            return true;
        } else {
            // echo 'Ocurrió un error al ejecutar el procedimiento.';
            return false;
        }
    }

    /**
     * Esta función elimina el sueldo de la tabla sueldos por medio de su ID.
     * @var idSueldo
     * @access public
     * @return array
     */
    public function delete($json) {
        $_respuestas = new respuestas;
        $datos = json_decode($json, true);

        if ($datos === null) {
            return $_respuestas->error_400("JSON inválido");
        }

        $requiredFields = array('idSueldo');
        foreach ($requiredFields as $field) {
            if (!isset($datos[$field])) {
                return $_respuestas->error_400("El campo '$field' es obligatorio");
            }
        }
    
            
        $this->idSueldo = $datos['idSueldo'];
       

        $resp = $this->eliminarSueldo();
    
        if ($resp) {
            $respuesta = $_respuestas->ok_200_procedimientos_almacenados('Sueldo eliminado con exito');
            return $respuesta;
        } else {
            return $_respuestas->error_500();
        }
    }

    /**
     * Esta función ejecuta un procedimiento almacenado que elimina registro en la tabla sueldos por el ID
     * @access private
     * @return array
     */
    private function eliminarSueldo(){
        $params = array(
            // i = int
            array('type' => 'i', 'value' => $this->idSueldo)
        );
        $result = $this->executeStoredProcedure('eliminarSueldoPorID', $params);
        echo $result;
        if ($result) {
            // echo 'El procedimiento se ejecutó correctamente.';
            return true;
        } else {
            // echo 'Ocurrió un error al ejecutar el procedimiento.';
            return false;
        }
        
    }

   
}
