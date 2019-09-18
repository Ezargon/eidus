<?php
defined('_JEXEC') or die('resticted aceess');
include JPATH_BASE.'/libraries/stubcode/view/Oferta_de_Programa_View.php';
include JPATH_BASE.'/libraries/stubcode/ddbb/Get_elements.php';

// Devuelve código PHP del Programa
// Se llama a la función desde el Articulo de Joomla del programa.
// {Articulo}->Titulo:'Oferta de programas',Alias:'oferta-programas'.
// Agregar el siguiente código:
/**
 <p>{source}</p>
 <?php
 include JPATH_BASE.'/libraries/stubcode/control/Oferta_de_Programa_Control.php';

 ___print();
 ?>
<p>{/source}</p>
*/

function ___print(){
    
    
  $bbdd = new Get_elements();
    
    //Obtiene todos los Programas
    $programas = $bbdd->getProgramas();
    
       //Creo la vista del Objeto de Actividades
    $view = new Oferta_de_Programa_View($programas);
    //Curso
     $view->__print("2019/20");
  
   //  $view->__print("2015/16");
     
}
