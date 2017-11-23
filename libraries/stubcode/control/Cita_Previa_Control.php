<?php
defined('_JEXEC') or die('resticted aceess');
use libraries\stubcode\view\Cita_Previa_Administrador_View;

include JPATH_BASE.'/libraries/stubcode/view/Cita_Previa_Administrador_View.php';
include JPATH_BASE.'/libraries/stubcode/ddbb/Get_CitaPrevia.php';

//Devuelve código PHP del Programa
//Entrada: Código de Programa que se quiere mostrar
//Se llama a la función desde el Articulo de Joomla del programa.
// Ejemplo:
/**
 <p>{source}</p>
 <?php
 include JPATH_BASE.'/libraries/stubcode/control/Cita_Previa_Control.php';
 ___controlCitaPrevia();
 ?>
 <p>{/source}</p>
 */
function ___controlCitaPrevia(){
    //Fecha de cita previa
    $fecha = "2016-07-06";

    $bbdd = new Get_CitaPrevia();
    $array_cita_previas = $bbdd->getCitasPrevias($fecha);
    
    $view = new Cita_Previa_Administrador_View($array_cita_previas);
    $view->__print();
   
}


