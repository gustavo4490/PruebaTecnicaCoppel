<?php
require_once "conexion/conexion.php";
require_once "respuestas.class.php";


class trabajador extends conexion {

    

   
    


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


   
}





?>