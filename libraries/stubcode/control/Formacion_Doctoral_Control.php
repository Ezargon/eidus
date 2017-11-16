<?php
include JPATH_BASE.'/libraries/stubcode/view/Formacion_Doctoral_View.php';
include JPATH_BASE.'/libraries/stubcode/ddbb/Get_elements.php';

//Devuelve c�digo PHP del Programa
//Entrada: C�digo de Programa que se quiere mostrar
//Se llama a la funci�n desde el Articulo de Joomla del programa.
// Ejemplo:
/**
 <p>{source}</p>
 <?php
 include JPATH_BASE.'/libraries/stubcode/control/Formacion_Doctoral_Control.php';
 ___print();
 ?>
 <p>{/source}</p>
 */
function ___print(){
   

    $bbdd = new Get_elements();
    
    //Obtengo el Array de Actividad
    $array_actividad = $bbdd->get_Actividades();
    
    //Creo la vista del Objeto de Actividades
    $view = new Formacion_Doctoral_View($array_actividad);
    $view->__print();
    
    
}

