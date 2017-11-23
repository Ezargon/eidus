<?php
defined('_JEXEC') or die('resticted aceess');
include JPATH_BASE.'/libraries/stubcode/view/articulos_programa_view.php';
include JPATH_BASE.'/libraries/stubcode/ddbb/Get_elements.php';

//Devuelve código PHP del Programa 
//Entrada: Código de Programa que se quiere mostrar
//Se llama a la función desde el Articulo de Joomla del programa.
// Ejemplo:
/**
<p>{source}</p>
<?php
include JPATH_BASE.'/libraries/stubcode/control/Articulos_Programa_Control.php';
    //Arquitectura
___print('3001');
?>
<p>{/source}</p>
*/

function ___print($codigo_programa){
    
    $bbdd = new Get_elements();

    //Obtengo el objeto del Programa
    $p = $bbdd->get_Programa($codigo_programa);
   
    //Creo la vista del Objeto de Programa
    $view = new Articulos_Programa_View($p);
    $view->__print();
  
}


