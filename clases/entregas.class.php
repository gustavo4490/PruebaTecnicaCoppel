<?php
require_once "conexion/conexion.php";
require_once "respuestas.class.php";


class entregas extends conexion {

  

    /**
     * Esta función devuelve listado de todas las entregas, espera el numero de pagina ya que solo trae 100 registros
     * @var pagina int
     * @access public
     * @return array
     */
    public function listaEntregas($pagina = 1){
        $inicio  = 0 ;
        $cantidad = 100;
        if($pagina > 1){
            $inicio = ($cantidad * ($pagina - 1)) +1 ;
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
    public function obtenerEntregasPorTrabajador($idTrabajador){
               
        $query= "SELECT entregas.idEntrega,trabajadores.nombreCompleto,entregas.precioEntrega,entregas.cantidadEntregas,entregas.fecha 
        FROM entregas 
        INNER JOIN trabajadores ON entregas.idTrabajador = trabajadores.idTrabajador WHERE entregas.idTrabajador = '$idTrabajador'";
        
        return parent::obtenerDatos($query);

    }


     
}





?>