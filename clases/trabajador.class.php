<?php
require_once "conexion/conexion.php";
require_once "respuestas.class.php";


class trabajador extends conexion {



    private $nombreCompleto = "";
    private $idRol = "";
    private $numeroEmpleado = "";
    private $bonoPorHora = "";
    private $sueldoPorHora = "";
    private $valesDespensa = "";

      


    /**
     * Esta función devuelve listado todos los trabajadores con sus roles, espera el numero de pagina ya que solo trae 100 registros
     * @var pagina int
     * @access public
     * @return array
     */
    public function listaTodosLosTrabajadores($pagina = 1){
        $inicio  = 0 ;
        $cantidad = 100;
        if($pagina > 1){
            $inicio = ($cantidad * ($pagina - 1)) +1 ;
            $cantidad = $cantidad * $pagina;
        }
        $query = "SELECT trabajadores.nombreCompleto,trabajadores.numeroEmpleado,roles.rol,trabajadores.bonoPorHora,trabajadores.sueldoPorHora,trabajadores.valesDespensa
        FROM trabajadores 
        INNER JOIN roles ON trabajadores.idRol = roles.idRol" . " limit $inicio,$cantidad";
        $datos = parent::obtenerDatos($query);
        return ($datos);
    }

     /**
     * Esta función devuelve datos del trabajador por id, espera el Id del trabajador
     * @var idTrabajador int
     * @access public
     * @return array
     */
    public function obtenerDatosTrabajadorPorID($idTrabajador)
    {

        $query = "SELECT trabajadores.nombreCompleto,trabajadores.numeroEmpleado,roles.rol,trabajadores.bonoPorHora,trabajadores.sueldoPorHora,trabajadores.valesDespensa
        FROM trabajadores 
        INNER JOIN roles ON trabajadores.idRol = roles.idRol WHERE trabajadores.idTrabajador =  '$idTrabajador'";

        return parent::obtenerDatos($query);
    }

    /**
     * Esta función guarda los trabajadores utilizando verbo post.
     * @var nombreCompleto
     * @var idRol
     * @var numeroEmpleado
     * @var bonoPorHora
     * @var sueldoPorHora
     * @var valesDespensa
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
 
         $requiredFields = array( 'nombreCompleto', 'idRol','numeroEmpleado','bonoPorHora','sueldoPorHora','valesDespensa');
         foreach ($requiredFields as $field) {
             if (!isset($datos[$field])) {
                 return $_respuestas->error_400("El campo '$field' es obligatorio");
             }
         }
 
         $this->nombreCompleto = $datos['nombreCompleto'];
         $this->idRol = $datos['idRol'];
         $this->numeroEmpleado = $datos['numeroEmpleado'];
         $this->bonoPorHora = $datos['bonoPorHora'];
         $this->sueldoPorHora = $datos['sueldoPorHora'];
         $this->valesDespensa = $datos['valesDespensa'];
 
 
         if (!is_string($this->nombreCompleto) || !is_string($this->bonoPorHora) || !is_string($this->sueldoPorHora)|| !is_string($this->valesDespensa)|| !is_string($this->numeroEmpleado)) {
             return $_respuestas->error_400("Los campos nombreCompleto, sueldoPorHora, numeroEmpleado y valesDespensa deben ser de tipo string");
         }
 
         if (!is_int($this->idRol)) {
             return $_respuestas->error_400("El campo idRol  debe ser de tipo entero");
         }
 
         $resp = $this->insertarTrabajador();
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
 
     private function insertarTrabajador()
     {
 
         $params = array(
             //s= string , i = int
                                                   
             array('type' => 's', 'value' => $this->nombreCompleto),
             array('type' => 'i', 'value' => $this->idRol),
             array('type' => 's', 'value' => $this->numeroEmpleado),
             array('type' => 's', 'value' => $this->bonoPorHora),
             array('type' => 's', 'value' => $this->sueldoPorHora),
             array('type' => 's', 'value' => $this->valesDespensa)
 
         );
         $result = $this->executeStoredProcedure('InsertarTrabajador', $params);
         if ($result) {
             // echo 'El procedimiento se ejecutó correctamente.';
             return true;
         } else {
             // echo 'Ocurrió un error al ejecutar el procedimiento.';
             return false;
         }
     }


   
}





?>