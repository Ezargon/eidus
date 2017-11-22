<?php

require_once('libraries/stubcode/model/Programa.php');

const ENLACES = array(
    "3001"=>"arquitectura", 
    "3002"=>"arte-y-patrimonio", 
    "3003"=>"biologia-integrada", 
    "3004"=>"biologia-molecular-biomedicina-e-investigacion-clinica", 
    "3005"=>"ciencias-economicas-empresariales-y-sociales", 
    "3006"=>"ciencias-de-la-salud", 
    "3007"=>"ciencia-y-tecnologia-de-nuevos-materiales", 
    "3008"=>"ciencias-y-tecnologias-fisicas", 
    "3009"=>"arte-y-patrimonio", 
    "3010"=>"educacion", 
    "3011"=>"electroquimica-ciencia-y-tecnologia", 
    "3012"=>"estudios-filologicos", 
    "3013"=>"farmacia", 
    "3014"=>"filosofia", 
    "3015"=>"geografia", 
    "3016"=>"gestion-estrategica-y-negocios-internacionales", 
    "3017"=>"historia", 
    "3018"=>"ingenieria-agraria-alimentaria-forestal-y-del-desarrollo-rural", 
    "3019"=>"ingenieria-automatica-electronica-y-de-telecomunicacion", 
    "3020"=>"ingenieria-energetica-quimica-y-ambiental", 
    "3021"=>"ingenieria-informatica", 
    "3022"=>"matematicas", 
    "3023"=>"psicologia", 
    "3024"=>"psicologia-de-los-recursos-humanos", 
    "3025"=>"quimica", 
    "3026"=>"recursos-naturales-y-medioambiente", 
    "3027"=>"sistemas-de-energia-electrica", 
    "3028"=>"turismo", 
    "3029"=>"derecho", 
    "3030"=>"ingenieria-mecanica-y-de-organizacion-industrial", 
    "3031"=>"quimica-teorica-y-modelizacion-computacional", 
    "3032"=>"instalaciones-y-sistemas-para-la-industria"
);


class Oferta_de_Programa_View {
    private $programas; 
    private $array_programa_ramas = array();
    private $array_ramas = array();
    
    public function __construct($programas){
        $array_rama = array();
        $this->programas = $programas;
        //INICIZA
        foreach($programas as $programa){
            $rama = $programa->getRama();
            
            if (!in_array($rama, $this->array_ramas)) {
                array_push ( $this->array_ramas ,$rama );
            }
            
            if(isset($this->array_programa_ramas[$rama])){
                $array_rama = $this->array_programa_ramas[$rama];
            }
            
            
            if($array_rama === null){
                $array_rama = array();
            }
            array_push ($array_rama ,$programa );
            $this->array_programa_ramas[$rama] = $array_rama;  
         
        }
    
        
    }
    
    function __print($curso){
        $print_ = "";
        $print_ .= $this->cabecera($curso);
        $print_ .= $this->createTable($curso);
      
        
        
        echo  $print_;
    }
    
    private function cabecera($curso){
        return "<p>Los programas de doctorado regulados por el RD 99/2011 constituyen la oferta para estudiantes de nuevo ingreso para el <strong>curso ".$curso."</strong>&nbsp;en estudios de doctorado en la Universidad de Sevilla.</p>";
    }
    private function createTable($curso){
        $TABLA_HEAD = ['Programa', 'Plazas '.$curso];
        $bootstrap_class_table = "table table-hover"; 
        
        $print_ = "";
     /*   $print_ .= "<table class=\"".$bootstrap_class_table."\">";
        $print_.="<thead>";
        $print_.="<tr>";
        foreach ($TABLA_HEAD as $head){
            $print_.="<th>".$head."</th>";
        }
        $print_.="</tr>";
        $print_.="</thead>";
        $print_.="</table>";*/
        foreach ($this->array_ramas as $rama){
            $print_ .= "<h2>".$rama."</h2>";
            $array_programas = $this->array_programa_ramas[$rama];
            
            $print_ .= "<table class=\"".$bootstrap_class_table."\">";
          
            $print_.="<tbody>";
            
            foreach($array_programas as $pr){
                $denominacion = $pr->getDenominacion();
                $codigo = $pr->getCodigo();
                $plaza__ = $pr->getArray_plaza();
                $plaza = $plaza__[$curso];
    
                $print_.="<tr>";
                $print_.="<td width=\"70%\">"."<a href=\"/eidus/estudios/oferta-programas/".ENLACES[$codigo]."\">".$denominacion."</a>"."</td>";
                $print_.="<td width=\"30%\">".$plaza->getTotal()." plazas</td>";
                $print_.="</tr>";
             
            }
            $print_.="</tbody>";
            $print_.="</table>";
            
        }
       
        return $print_;
        
        
    }
}


