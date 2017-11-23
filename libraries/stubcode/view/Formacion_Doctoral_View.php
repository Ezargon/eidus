<?php

const TABLA_ID = "cursos_";
const HREF_IMPRESO = "impresos/Instrucciones_formacion_doctoral.pdf";
const HREF_AFOROS = "https://sfep.us.es/wsfep/sfep/cursos_aforos.html";

const TABLA_HEAD = ['Inicio', 'Fin', 'Nombre de la acci&oacute;n formativa', 'Localizaci&oacute;n', 'Gestion', 'Enlace'];

class Formacion_Doctoral_View {
    private $array_actividad = array();
    
    public function __print(){
        $print_ = "";
        $print_ .= $this->print_head();
        $print_ .= $this->print_Oferta_Formativa();
        echo $print_;
        
    }
    
    public function __construct($array){
        $this->array_actividad = $array;
    }
    
    private function print_head(){
        $print_ = "";
        $print_ .= "<p>La Escuela Internacional de Doctorado de la Universidad de Sevilla (EIDUS) oferta en este a&ntilde;o la posibilidad de acceder a las acciones formativas organizadas por el Secretariado de Formaci&oacute;n y Evaluaci&oacute;n (SFE) a trav&eacute;s del ICE (Instituto de Ciencias de la Educaci&oacute;n) dentro del III Plan Propio de Docencia a los estudiantes de doctorado. Esta colaboraci&oacute;n se inici&oacute; en el a&ntilde;o 2016 y este a&ntilde;o se vuelve a poner a disposici&oacute;n de los estudiantes de doctorado de la Universidad de Sevilla.</p>";
        $print_ .= "<p><a href=\"".HREF_IMPRESO."\" target=\"_blank\">Informaci&oacute;n: Inscripci&oacute;n e instrucciones</a></p>";
        return $print_;
    }
    private function print_Oferta_Formativa(){
     
        $print_ = "";
        $print_.= $this->includeCSS(); 
        $print_.= $this->includeJavaScript(); 
        $print_ .= "<h3>Oferta de acciones formativas<a href=\"".HREF_AFOROS."\"><br /></a></h3>";
       // $print_ .= "<table id=\"".TABLA_ID."\" class=\"sortable\">";
       
        $print_ .= "<table id=\"".TABLA_ID."\" class=\"table table-striped table-bordered\" width=\"100%\" cellspacing=\"0\">";
        $print_ .= "<thead>";
        $print_ .= "<tr>";
        foreach(TABLA_HEAD as $item ) {
            $print_ .= "<th>" . $item . "</th>";
        }
        $print_ .= "</tr>";
        $print_ .= "</thead>";
        
        $print_ .= "<tfoot>";
        $print_ .= "<tr>";
        foreach(TABLA_HEAD as $item ) {
            $print_ .= "<th>" . $item . "</th>";
        }
        $print_ .= "</tr>";
        $print_ .= "</tfoot>";
       
        $print_ .= "<tbody>";
        $count=1;
        foreach($this->array_actividad as $actividad ) {
            if($count%2==0){
                $paridad="par";
            }else{
                $paridad="impar";
            }
            $print_ .= "<tr class=\"".$paridad."\">";
            $print_ .= "<td nowrap=\"nowrap\">".$this->fix_fecha($actividad->getFecha_ini())."</td>";
            $print_ .= "<td nowrap=\"nowrap\">".$this->fix_fecha($actividad->getFecha_fin())."</td>";
            $print_ .= "<td>".$actividad->getTitulo()."</td>";
            $print_ .= "<td>".$actividad->getLugar()."</td>";
            $print_ .= "<td>".$actividad->getGestion()."</td>";
            $obj =json_decode($actividad->getEnlace());
            if(is_null($obj)){
                $print_ .= "<td></td>";
            }else{

                $print_ .= "<td><a target=\"blank\" href=/".$obj->{'link'}."\">".$obj->{'label'}."</a><br><span style=\"font-size: 10px;\">Fin inscripci&oacute;n: 11-05-2017</span></td>";
            }
           
            $print_ .= "</tr>";
            $count++;
        }
        $print_ .= "</tbody>";
        $print_ .= "</table>";
        $print_ .= "<script>$(document).ready(function(){\$(\"#".TABLA_ID."\").DataTable();} );</script> "; 

        return $print_;
    }
    
    private function fix_fecha($fecha){
        $dd = substr($fecha,8,2);
        $mm =  substr($fecha,5,2);
        $aaaa = substr($fecha,0,4);
        return $dd . "/" . $mm . "/" . $aaaa;
    }
    private function includeJavaScript(){
        $js1_ = "//code.jquery.com/jquery-1.12.4.js";
        $js1 = "http://". $_SERVER['SERVER_NAME'].'\\eidus\libraries\stubcode\includes\js\jquery-1.12.4.js'; //
        $js2 = "http://". $_SERVER['SERVER_NAME'].'\\eidus\libraries\stubcode\includes\js\jquery.dataTables.min.js'; //https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js
        $print_ = "";
        $print_.= "<script src=\"".$js1_."\"></script>";
        $print_.= "<script src=\"".$js2."\"></script>";
        $print_.= "<script src=\"https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js\"></script>";
        return $print_;
    }
    private function includeCSS(){
        $print_ = "";
       $print_.= "<head><link rel=\"stylesheet\" type=\"text/css\" href=\"//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css\"></head>";
        $print_.= "<head><link rel=\"stylesheet\" type=\"text/css\" href=\"https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css\"></head>";
        return $print_;
    }
  
    
    
}

