<?php

/**
 * Description of data-toggle
 *
 * @author jdominguez10
 */


function tabArticulos(){
   $opciones = array(
    "#descripcion"    => "Descripci&oacute;n",
    "#acceso"  => "Acceso al programa",
    "#lineas" => "L&iacute;neas de investigaci&oacute;n y profesores",
    "#contacto" => "Contacto",
    "#reglamento" => "Reglamento relativo a la Tesis Doctoral"
    );
    print_tab($opciones);
}
function print_tab($opciones){
     $print_ = "";
     $print_.="<ul class='nav nav-tabs'>";
      foreach($opciones as $etiqueta=>$valor)  {
           $print_.="<li><a href='".$etiqueta."' data-toggle='tab'>".$valor."</a></li>";
       }
      $print_.="</ul>";
      echo $print_;
    
}
