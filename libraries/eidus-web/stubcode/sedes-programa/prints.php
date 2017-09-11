<?php
include  JPATH_BASE.'/libraries/eidus-web/mysql/eidus-fabrik/extract.php';

##############
# Constantes #
##############
const OFERTA_PLAN_2011 = "oferta-estudios-doctorado/oferta-plan-2011";
const URL_PROGRAMA = array(
    /*Arquitectura (3001)*/
"3001" => "/arquitectura-plan-2011",
/*Arte y Patrimonio (3002)*/
"3002" => "/arte-y-patrimonio-plan-2011",
/*Biología Integrada (3003)*/
"3003" => "/biologia-integrada-plan-2011",
/*Biologia Molecular, Biomedicina e Investigación Clínica (3004)*/
"3004" => "/biologia-molecular-biomedicina-e-investigacion-clinica-plan-2011",
/*Ciencias Económicas, Empresariales y Sociales (3005)*/
"3005" => "/ciencias-economicas-empresariales-y-sociales-plan-2011",
/*Ciencias de la Salud (3006)*/
"3006" => "/ciencias-de-la-salud-plan-2011",
/*Ciencia y Tecnología de Nuevos Materiales (3007)*/
"3007" => "/ciencia-y-tecnologia-de-nuevos-materiales-plan-2011",
/*Ciencias y Tecnologías Físicas (3008)*/
"3008" => "/ciencias-y-tecnologias-fisicas-plan-2011",
/*Comunicación (3009)*/
"3009" => "/comunicacion-plan-2011",
/*Educación (3010)*/
"3010" => "/educacion-plan-2011",
/*Electroquímica, Ciencia y Tecnología (3011)*/
"3011" => "/electroquimica-ciencia-y-tecnologia-plan-2011",
/*Estudios Filológicos (3012)*/
"3012" => "/estudios-filologicos-plan-2011",
/*Farmacia (3013)*/
"3013" => "/farmacia-plan-2011",
/*Filosofía (3014)*/
"3014" => "/filosofia-plan-2011",
/*Geografía (3015)*/
"3015" => "/geografia-plan-2011",
/*Gestión Estratégica y Negocios Internacionales (3016)*/
"3016" => "/gestion-estrategica-y-negocios-internacionales-plan-2011",
/*Historia (3017)*/
"3017" => "/historia-plan-2011",
/*Ingeniería Agraria, Alimentaria, Forestal y del Desarrollo Rural (3018)*/
"3018" => "/ingenieria-agraria-alimentaria-forestal-y-del-desarrollo-rural-plan-2011",
/*Ingeniería Automática, Electrónica y de Telecomunicación (3019)*/
"3019" => "/ingenieria-automatica-electronica-y-de-telecomunicacion-plan-2011",
/*Ingeniería Energética, Química y Ambiental (3020)*/
"3020" => "/ingenieria-energetica-quimica-y-ambiental-plan-2011",
/*Ingeniería Informática (3021)*/
"3021" => "/ingenieria-informatica-plan-2011",
/*Matemáticas (3022)*/
"3022" => "/matematicas-plan-2011",
/*Psicología (3023)*/
"3023" => "/psicologia-plan-2011",
/*Psicología de los Recursos Humanos (3024)*/
"3024" => "/psicologia-de-los-recursos-humanos-plan-2011",
/*Química (3025)*/
"3025" => "/quimica-plan-2011",
/*Recursos Naturales y Medioambiente (3026)*/
"3026" => "/recursos-naturales-y-medioambiente-plan-2011",
/*Sistemas de Energía Eléctrica (3027)*/
"3027" => "/sistemas-de-energia-electrica-plan-2011",
/*Turismo (3028)*/
"3028" => "/turismo-plan-2011",
/*Derecho (3029)*/
"3029" => "/derecho-plan-2011",
/*Ingeniería Mecánica y de Organización Industrial (3030)*/
"3030" => "/ingenieria-mecanica-y-de-organizacion-industrial-plan-2011",
/*Química Teórica y Modelización Computacional (3031)*/
"3031" => "/quimica-teorica-y-modelizacion-computacional-plan-2011"
);

function printHref($url,$label){
    $print ="";
    $print.="<a href=\"".$url."\">".$label."</a>";
    return $print;
}
function tablaCentroAdministrativo(){
    $print="";

    try{
       $t_programas = t_programas_plan2011();
      
       
       $print.="<table class='table table-striped'>";
       $print.="<tbody>"; 
       $print.="<tr><th style='text-align: center;'>Centros administrativos de los programas de Doctorado RD 99/2011</th></tr>";
       
      foreach ($t_programas as $r_programa){
          $array = json_decode($r_programa->contacto);
          $idprograma = $r_programa->id;
          $r_organoparticipante = t_OrganoParticipante($idprograma);
          
          
          if($array!=null){
                 $contacto_admin = $array->administrativo->contactos;
                 $contacto_acade = $array->academico->contactos;
          }
            $print.="<tr>";
            $print.="<td style='text-align: left;' width='400'>";
   
            $print.="<p><strong>".printHref(OFERTA_PLAN_2011.URL_PROGRAMA[$r_programa->codigo],$r_programa->denominacion)."</strong></p>";
            $print.="<ul>";
            $print.="<li>";
            $print.="<strong>Órganos participantes</strong>";

     foreach($r_organoparticipante as $element) {
         try{
             $universidad=$element->universidad;
             if(strcmp($universidad, "") == 0 ){
                 $universidad="Universidad de Sevilla";
             }
             $escuela=$element->escuela;
             $web=$element->web;

         } catch (Exception $e){
             echo "Exception :: " . $e;
             $universidad="";
             $escuela="";
             $web="";
        }finally{
            $print .= "<ul>";
            if(strcmp($web, "") !== 0){   
                $print .= "<li><a href='".$web."' title='".$escuela."'>".$escuela."</a> (".$universidad.")</li>";
            }else{
                $print .= "<li>".$escuela." (".$universidad.")</li>";
            }
            $print .= "</ul>";  
        }
    
     }
            
            $print.="</li>";
              
            $print.="<li>";
            $print.="<strong>Contacto administrativo</strong>";
            $print.=printListaContacto($contacto_admin);
            $print.="</li>";
            $print.="<li>";
            $print.="<strong>Contacto acad&eacute;mico:</strong>";
            $print.=printListaContacto($contacto_acade);
            $print.="</li>";
            $print.="</ul>";
            $print.="</td></tr>";
       }
       $print.="</tbody></table>"; 

    }catch(Exception $e){
        
    }finally{
        return $print;
    }
    
}

function printListaContacto($contactos){
    $print_ ="";
   
    try{
        if(count($contactos)>0){
         $print_ .="<ul>";
        foreach($contactos as $contacto){
            
        $lista = [];

        $direccion = $contacto->direccion;
        $telefono = $contacto->telefono;
        $nombre = $contacto->nombre;
        $email = $contacto->email;
        
        if(($nombre)!=""){
            array_push ( $lista, $nombre );
        }
        if(($email)!=""){
            array_push ( $lista, $email );
        }
        if(($direccion)!=""){
            array_push ( $lista, $direccion );
        }
       if(($telefono)!=""){
            array_push ( $lista, $telefono );
        }
        $count = count($lista);
        
        $i=0;
        $print_ .="<li>";
        while($i<$count){
        
        if($i=== ($count-1)){
            $print_ .= $lista[$i].".";
        }else{
            $print_ .= $lista[$i].", ";
        }
         $i++;
        }
        $print_ .="</li>";
    }
    $print_ .="</ul>";
        }
    }catch(Exception $e){
        echo $e;
    }finally{
        return $print_ ;
    }
    
}


