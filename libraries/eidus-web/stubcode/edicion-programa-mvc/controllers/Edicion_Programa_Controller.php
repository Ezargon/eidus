<?php
include JPATH_BASE.'/libraries/eidus-web/stubcode/edicion-programa-mvc/models/programa_tabla.php';
include JPATH_BASE.'/libraries/eidus-web/stubcode/edicion-programa-mvc/models/programa.php';
include JPATH_BASE.'/libraries/eidus-web/stubcode/edicion-programa-mvc/views/Programa_View.php';

//Llamada al modelo
$programa_tabla = new programa_tabla(); 
$programas = $programa_tabla->get_programas2011();
$idprograma = null;
$programa = null;

/**
 * HTML names, ids, ..
 */
$selectName = 'sel_programa';

try {
      if(isset($_POST[$selectName]) ){
        $select = (explode("_",$_POST[$selectName]));
        $idprograma = $select[0];
        $programa = new programa($idprograma);  
       }
       
     
       
Programa_View::SelectHTML($programas, $idprograma,$selectName);
if(isset($programa)){
        Programa_View::tablaHTML($programa-> get_programa());
}
    
    
    
    
    
    
}catch(Exception $e){
      
}




 
