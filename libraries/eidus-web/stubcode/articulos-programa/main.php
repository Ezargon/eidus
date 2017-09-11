<?php
include JPATH_BASE.'/libraries/eidus-web/stubcode/articulos-programa/prints.php';

##############
# Constantes #
##############

const ID_PROGRAMA = array(
    ///Arte y Patrimonio
"3002" => "2",
///Psicología de los Recursos Humanos
"3024" => "26",
///Arquitectura
"3001" => "1",
///Psicología
"3023" => "25",
///Matemáticas
"3022" => "24",
///Ingeniería Informática
"3021" => "22",
///Ingeniería Energética, Química y Ambiental
"3020" => "21",
///Ingeniería Automática, Electrónica y de Telecomunicación
"3019" => "20",
///Ingeniería Agraria, Alimentaria, Forestal y del Desarrollo Rural
"3018" => "19",
///Historia
"3017" => "18",
///Gestión Estratégica y Negocios Internacionales
"3016" => "17",
///Geografía
"3015" => "16",
///Filosofía
"3014" => "15",
///Farmacia
"3013" => "14",
///Estudios Filológicos
"3012" => "13",
///Electroquímica, Ciencia y Tecnología
"3011" => "12",
///Educación
"3010" => "11",
///Química Teórica y Modelización Computacional
"3031" => "28",
///Ingeniería Mecánica y de Organización Industrial
"3030" => "23",
///Comunicación
"3009" => "9",
///Ciencias y Tecnologías Físicas
"3008" => "8",
///Ciencia y Tecnología de Nuevos Materiales
"3007" => "5",
///Derecho
"3029" => "10",
///Ciencias de la Salud
"3006" => "6",
///Turismo
"3028" => "31",
///Ciencias Económicas, Empresariales y Sociales
"3005" => "7",
///Sistemas de Energía Eléctrica
"3027" => "30",
///Biologia Molecular, Biomedicina e Investigación Clínica
"3004" => "4",
///Recursos Naturales y Medioambiente
"3026" => "29",
///Biología Integrada
"3003" => "3",
///Química
"3025" => "27"
);
function printArticulo($uxxi){
    printMainTab(ID_PROGRAMA[$uxxi]);
}


function printdiv(){
     $print_ = "";
    $print_ .= "<div class=\"lt-variation-content-text\">";
    /*$print_ .= "<h1 class=\"counter\">30</h1>";*/
    /*$print_ .= "<p>PROGRAMAS</p>";*/
    $print_ .= "</div>";
    echo $print_;
}
function printMainTab($id_programa){
    $print_ = "";
    $print_ .= "<ul class='nav nav-tabs'>";
    $print_ .= "<li class='active'><a href='#descripcion' data-toggle='tab'>Descripci&oacute;n</a></li>";
    $print_ .= "<li><a href='#acceso' data-toggle='tab'>Acceso al programa</a></li>";
    $print_ .= "<li><a href='#lineas' data-toggle='tab'>L&iacute;neas de investigaci&oacute;n y profesores</a></li>";
    $print_ .= "<li><a href='#contacto' data-toggle='tab'>Contacto</a></li>";
    $print_ .= "<li><a href='#reglamento' data-toggle='tab'>Reglamento relativo a la Tesis Doctoral</a></li>";
    $print_ .= "</ul>";
    $print_ .= "<div class='tab-content pill-content'>";
    /**
     * Tab #descripcion
     */
    $print_ .= "<div id='descripcion' class='tab-pane fade in active pill-pane'><!--DESCRIPCION :: #descripcion-->";
    $print_ .= print_tab_descripcion($id_programa);
    $print_ .= "</div>";
    /**
     * Tab #acceso
     */
    $print_ .="<div id='acceso' class='tab-pane fade'>";
    $print_ .= print_tab_acceso($id_programa);
    $print_ .="</div>";
     /**
     * Tab #lineas
     */
    $print_ .="<div id='lineas' class='tab-pane fade'>";
    $print_ .=printProfesorLinea($id_programa);
    $print_ .="</div>";
    /**
     * Tab #contacto
     */
   $print_ .="<div id='contacto' class='tab-pane fade'>";
   $print_ .=print_tab_contacto($id_programa);
   $print_ .="</div>";
    /**
     * Tab #reglamento
     */
    $print_ .="<div id='reglamento' class='tab-pane fade'>";
    $print_ .=print_tab_reglamento($id_programa);
    $print_ .="</div>";

    $print_ .="</div>";
    
    echo $print_;
}
/**
 * Imprime una constante HTML al Reglamento Relativo.
 * @param type $id_programa
 */
function printReglamentoRelativo($id_programa){
    $print_ = "";
    $print_ .= "<h3>Reglamento Relativo a la Tesis Doctoral</h3>";
    $print_ .= "<p><span>Visitar la <a href='index.php?option=com_content&amp;view=article&amp;id=62&amp;Itemid=185' title='Normativa de R&eacute;gimen de Tesis Doctoral de la Universidad de Sevilla'>Normativa de R&eacute;gimen de Tesis Doctoral de la Universidad de Sevilla</a></span></p>";
    $print_ .= "<p><span><a href='http://bous.us.es/2011/numero-4/pdf/archivo-12.pdf' target='_blank' class='pdf'>Acuerdo 7.2/CG 17-6-11</a> por el que se aprueba la Normativa de Estudios de Doctorado conforme a lo establecido en el R.D. 99/2011.</span></p>";
//    $print_ .= "<p>&nbsp;</p>";
    return $print_;
}

//@TODO 
//Crear Objeto DATAGOOGLE (titulo, etiqueta, contenido)
//Agregar contenido para crear PDF automaticos
//<div id="descripcion" class="tab-pane fade in active pill-pane">
function print_tab_descripcion($id_programa){
    $print_ = "";
    try {
    $print_ .=printTablaDescripcion($id_programa);
    $print_ .=printTablaComision($id_programa);
    $print_ .=printTablaOrganosParticipantes($id_programa);
    $print_ .=printCentroAdministrativo($id_programa);
    $print_ .=printRegimenPermanencia($id_programa);
    $print_ .=printPlazas($id_programa);
    }catch(Exception $e){
          echo 'Excepción capturada articulos-programa·php print_tab_descripcion:: ',  $e->getMessage(), "\n";
    }finally{
        return $print_ ;
    }
    
}
//<div id="lineas" class="tab-pane fade">
function print_tab_linea($id_programa){
    $print_ = "";
    try {
       $print_ .=printProfesorLinea($id_programa);

    }catch(Exception $e){
          echo 'Excepción capturada articulos-programa·php print_tab_linea:: ',  $e->getMessage(), "\n";
}finally{
    return $print_;
}
}
//<div id="acceso" class="tab-pane fade">
function print_tab_acceso($id_programa){
    $print_ = "";
    try {
        $print_ .=printPlazas($id_programa);
        $print_ .=printCentroAdministrativo($id_programa);
        $print_ .=printPerfilIngreso($id_programa);

    }catch(Exception $e){
          echo 'Excepción capturada articulos-programa·php print_tab_acceso:: ',  $e->getMessage(), "\n";
    }finally{
        return $print_;
    }
   
}
//<div id="contacto" class="tab-pane fade">
function print_tab_contacto($id_programa){
   return printContacto($id_programa);
}

function print_tab_reglamento($id_programa){
    return printReglamentoRelativo($id_programa);
}