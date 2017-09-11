<?php

include JPATH_BASE.'/libraries/eidus-web/mysql/eidus-fabrik/extract.php';



/**
 * ###############################
 * #          FUNCTIONS          #
 * ###############################
 *
 * */

/**
 * El campo acceso tiene guardado en bruto HTML
 * @TODO: Pasar a XML o JSON la información que se guarda.
 * @param type $id_programa
 */
function printPerfilIngreso($id_programa){
      //$print_ = "";
      try{
        $row_programa = row_Programa($id_programa);
        $acceso =  $row_programa["acceso"];
      
      }catch(Exception $e){
           $acceso ="";
      }finally{
          
      }
      return $acceso;  
}

function printContacto($id_programa){
      $print_ = "";
      try{
        $row_programa = row_Programa($id_programa);
        $contacto =  $row_programa["contacto"];
        
        $array = json_decode($contacto);
        if($array!=null){
        $contacto_admin = $array->administrativo->contactos;
        $contacto_acade = $array->academico->contactos;

        $print_.="<h3>Contacto Administrativo</h3>"; 
        $print_.= printListaContacto($contacto_admin);
        $print_.="<h3>Contacto Academico</h3>"; 
        $print_.= printListaContacto($contacto_acade);
        }
        
      }catch(Exception $e){
           $print_ =$e;
      }finally{
          
           return $print_;  
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
        
    }finally{
        return $print_ ;
    }
    
}




/**
 * A partir de una id de programa imprime tabla de organos participantes
 * @param type $id_programa
 */
function printTablaOrganosParticipantes($id_programa){
     $print_ = "";
     $t_OrganoParticipante = t_OrganoParticipante($id_programa);
             
    $print_ .= "<div class='table-responsive'>";
    $print_ .= "<h3>&Oacute;rganos participantes</h3>";
    $print_ .= "<table class='table table-striped'>";
    $print_ .= "<tbody>";
    
     foreach($t_OrganoParticipante as $element) {
         try{
             $universidad=$element->universidad;
             if(strcmp($universidad, "") == 0 ){
                 $universidad="Universidad de Sevilla";
             }
             //$escuela=$element->escuela;
             $web=$element->web;

         } catch (Exception $e){
             echo "Exception :: " . $e;
             $universidad="";
             $escuela="";
             $web="";
        }finally{
            $print_ .= "<tr>";
            $print_ .= "<td>";
            $print_ .= "<h5>".$universidad."</h5>";
            $print_ .= "</td>";
            if(strcmp($web, "") !== 0){   
              //  $print_ .= "<td><a href='".$web."' title='".$escuela."'>".$escuela."</a></td>";
            }else{
                $print_ .= "<td>".$escuela."</td>";
            }
            $print_ .= "</tr>";  
        }
    
     }
$print_ .= "</tbody>";
$print_ .= "</table>";      
$print_ .= "</div>";         
$print_ .= "<br />";  

     return $print_;
    
}

/**
 * Imprime Centro Administrativo Responsable a partir de la ID de programa
 * @param t_Programa->$id_programa
 */
function printCentroAdministrativo($id_programa){
     $print_ = "";
     try{
        $row_programa = row_Programa($id_programa);
        $contacto =  $row_programa["contacto"];
         $array = json_decode($contacto);     
             $contacto_admin = $array->administrativo->contactos;
             $print_ .= "<h3>Centro administrativo responsable</h3>";
             $print_.= printListaContacto($contacto_admin);
              $print_ .= "<br />";
             
         
         
     }catch(Exception $e){
      $print_ .=$e;
     }finally{
         return $print_;
     }
    
    
}
/**
 * Constante.
 * @param type $id_programa
 */
function printRegimenPermanencia($id_programa){
    $print_ = "";
    $print_.="<h3>R&eacute;gimen de permanencia</h3>";
    $print_.="<p>Tiempo completo y tiempo parcial</p>";
     return $print_;
}


/**
 * Imprime Tabla Resumen de Descripción del Programa a partir de la ID de Programa
 * @param t_Programa->$id_programa
 */
function printTablaDescripcion($id_programa){
    $print_ = "";
    /**
     * Valores
     */
    $isced1="";
    $isced2="";
    $codigo="";
    $web="";
    $denominacion="";
    $interuniversitario="";
    $email="";
    $nivel="Doctorado";
   
    try{
       $row_Programa = row_Programa($id_programa);

        $isced1=$row_Programa["isced1"];
        $isced2=$row_Programa["isced2"];
        $codigo=$row_Programa["codigo"];
        $web=$row_Programa["web"];
        $denominacion=$row_Programa["denominacion"];
        $interuniversitario= mb_convert_encoding(mb_convert_case($row_Programa["interuniversitario"], MB_CASE_TITLE), "UTF-8");
        $email=$row_Programa["email"];
              
     

    }catch(Exception $e){
        echo "Exception ::".$e;
    }
    
    $print_ .= "<div class='table-responsive'>";
    $print_ .= "<h3>Descripci&oacute;n del programa</h3>";
    $print_ .= "<table class='table table-striped tabla-programa'>";
    $print_ .= "<tbody>";
    
    $print_ .= "<tr>";
    $print_ .= "<td>";
    $print_ .= "<h5>Nivel</h5>";
    $print_ .= "</td>";
    $print_ .= "<td>Doctorado</td>";
    $print_ .= "</tr>";
    
    $print_ .= "<tr>";
    $print_ .= "<td>";
    $print_ .= "<h5>C&oacute;digo ISCED1</h5>";
    $print_ .= "</td>";
    $print_ .= "<td>".$isced1."</td>";
    $print_ .= "</tr>";
    
    
    $print_ .= "<tr>";
    $print_ .= "<td>";
    $print_ .= "<h5>C&oacute;digo ISCED2</h5>";
    $print_ .= "</td>";
$print_ .= "<td>".$isced2."</td>";
$print_ .= "</tr>";

$print_ .= "<tr>";
$print_ .= "<td>";
$print_ .= "<h5>C&oacute;digo UXXI</h5>";
$print_ .= "</td>";
$print_ .= "<td>".$codigo."</td>";
$print_ .= "</tr>";

$print_ .= "<tr>";
$print_ .= "<td>";
$print_ .= "<h5>Web oficial</h5>";
$print_ .= "</td>";
$print_ .= "<td><a href='".$web."' title='".$web."'>".$web."</a></td>";
$print_ .= "</tr>";

    $print_ .= "<tr>";
    $print_ .= "<td>";
    $print_ .= "<h5>Email</h5>";
    $print_ .= "</td>";
$print_ .= "<td>".$email."</td>";
$print_ .= "</tr>";


$print_ .="</tbody>";
$print_ .="</table>";
$print_ .="</div>";

  $print_ .="<br />";
    return $print_;
}

/**
 * Órgano responsable del Programa: Comisión Académica
 * printComision
 * @param type $id_programa: id del programa
 */
function printTablaComision($id_programa)
{
    $print_ = "";
	$rows = t_ProfesorComision($id_programa);
	$tabla = ordenaPrioridadComite($rows);
	
	//@TODO - Cargar plantilla.
	

	$print_ .= "<div class='table-responsive tabla-programa'>";
	$print_ .= "<h3>&Oacute;rgano responsable del Programa: Comisi&oacute;n Acad&eacute;mica</h3>";
	$print_ .= "<table class='table table-striped'>";
	$print_ .= "<tbody>";
		foreach($tabla as $element) {
			$row = $element->getRow();
			
		$cargo = mb_convert_encoding(mb_convert_case($row->cargo, MB_CASE_TITLE), "UTF-8");
		$nombre = mb_convert_encoding(mb_convert_case($row->nombre, MB_CASE_TITLE), "UTF-8");
		$apellidos = mb_convert_encoding(mb_convert_case($row->apellidos, MB_CASE_TITLE), "UTF-8");
		$otra = $row->otra ;
		
		
		$print_ .= "<tr>";
		$print_ .= "<td>";
		$print_ .= "<h5>".$cargo."</h5>";
		$print_ .= "</td>";
		$print_ .= "<td>".$nombre." ".$apellidos." ";
		if($otra != ""){
			$print_ .= "(".$otra.")"; ;
		}else{
			$print_ .= ""; 
		}
		$print_ .= "</td></tr>";
	}
	$print_ .= "</tbody>";
	$print_ .= "</table>";
	$print_ .= "</div>";
        $print_ .= "<br /> <!--tabla participantes-->";
	return $print_;
}

/**
 * Número de Plazas
 * printPlazas
 * @param type $id_programa
 */
function printPlazas($id_programa){
    $rows = t_plaza($id_programa);
    //Array para imprimir
    $array = [];
    
    //Relleno array
    foreach($rows as $row) {
        if($row->plazo == "PRIMERO"){
             $curso = $row->curso;
             $total = $row->total;
             //Creo el array
             $array[$curso]= $total;       
        }      
    }
            
    //@TODO - Cargar plantilla.
    $print_ = "";
    $print_ .= "<div class='table table-condensed'>";
    $print_ .= "<h3>N&uacute;mero de Plazas</h3>";
    $print_ .= "<table class='table table-striped'>";
    $print_ .= "<thead>";
    $print_ .= "<tr>";
    $print_array="";
      foreach($array as $curso=>$total)  {
           $print_array.="<th>Curso ".$curso."</th>";  
     }
      $print_ .=$print_array;
      $print_ .= "</tr>";
    $print_ .= "</thead>";
    $print_ .= "<tbody>";
    $print_ .= "<tr>";
     $print_array="";
    foreach($array as $curso=>$total){
         $print_array.= "<td>".$total."</td>";
    }
    $print_ .=$print_array;
    $print_ .= "</tr>";
    $print_ .= "</tbody>";
    $print_ .= "</table>";
    $print_ .= "</div>";
    $print_ .= "<br />";
    return $print_;
}

function printLineasProfesor($id_programa){
    $print_ = "";
    try{
        $elements = t_ProfesoresPrograma($id_programa);
        $print_ .= "<div class='container'>";
        $print_ .= "<div class='panel-group' id='accordion'>";
        
        //CREAR PRIMERO TABLA DE TABLA DE PROFESORES Y RECORRER A CONTINUACION
        for($i=1; $i<4; $i++) {
            
             $print_ .="<div class='panel panel-default'>";
             $print_ .="<div class='panel-heading'>";
             $print_ .="<h4 class='panel-title'>";
             $print_ .="<a data-toggle='collapse' data-parent='#accordion' href='#collapse".$i."'>Collapsible Group 1</a>";
             $print_ .="</h4>>";
             $print_ .="</div>";
             $print_ .="<div id='collapse".$i."' class='panel-collapse collapse in'>";
             $print_ .="<div class='panel-body'>Lorem ipsum dolor sit amet, consectetur adipisicing elit</div>";
             $print_ .="</div>";
             $print_ .="</div>";
            
        }
          $print_ .="</div>";
          $print_ .="</div>";
           $print_ .="</div>";
    
    }catch(Exception $e){
        
    }finally{
        return $print_;

}
}


/**
 * Lineas de Investigación y Profesores
 * printProfesorLinea
 * @param type $id_programa
 */
function printProfesorLinea($id_programa)
{
    try{
        $print_ = "";
        $elements = t_ProfesoresPrograma($id_programa);
        
        //Map a partir de código, tabla de elementos de profesores
        $tablas = array();
        //Map (código/nombre de la linea).
        $codigos= array();

        //Recorro todos los elementos
         foreach($elements as $element) {
             $codigoLinea = $element->codigo;
             
        //Existe codigo lineas en Map Tablas 
             if(array_key_exists($codigoLinea, $codigos)){
                $tablas[$codigoLinea][$element->dni]=$element;
               
             }else{
                $codigos[$codigoLinea]=$element->denominacion;
                $tabla = array();
                $tablas[$codigoLinea] = $tabla;
                $tablas[$codigoLinea][$element->dni]=$element;

             }
             
         }
 
    $print_ .= "<h3>Lineas de Investigaci&oacute;n y Profesores</h3>";
    $print_ .= "<div class='panel-group accordion' id='accordion'>";  

         foreach($tablas as $codigoLinea => $tabla) {
             $print_ .= "<div class='panel panel-default accordion-group'>";
                    $print_ .= "<div class='panel-heading accordion-heading'>";
                    $print_ .= "<h4 class='panel-title'><a href='#".$codigoLinea."' data-toggle='collapse' data-parent='#accordion'>".$codigoLinea." - ".$codigos[$codigoLinea]."</a></h4>";
                    $print_ .= "</div>"; 
             $print_ .= "<div id='".$codigoLinea."' class='panel-collapse collapse'>"; 
             $print_ .= "<div class='panel-body accordion-inner'>";
             
             $print_ .= "<ul>";
             foreach ($tabla as $element){
                 $nombre = mb_convert_encoding(mb_convert_case($element->nombre, MB_CASE_TITLE), "UTF-8");
              $apellidos = mb_convert_encoding(mb_convert_case($element->apellidos, MB_CASE_TITLE), "UTF-8");
              //$id = $element->id;
              $email =  $element->email;   
                if(strcmp($email, "") !== 0){
                   $e =  JHtml::_('email.cloak',   $email);
                   $print_ .= "<li>".$apellidos.", ".$nombre." (".$e .")</li>";
              
                    }else{
                     $print_ .= "<li>".$apellidos.", ".$nombre."</li>";
                }
             }
             $print_ .= "</ul>";
             $print_ .= "</div></div></div>";
             
         }
   $print_ .= "</div>";
        
        
    }catch(Exception $e){
        
    }finally{
        return $print_;
    }

  
}
function tabArticulos_(){
 $print_ = "";
$print_.="<ul class='nav nav-tabs'>";
$print_.="<li class='active'><a href='#descripcion' data-toggle='tab'>Descripci&oacute;n</a></li>";
$print_.="<li><a href='#acceso' data-toggle='tab'>Acceso al programa</a></li>";
$print_.="<li><a href='#lineas' data-toggle='tab'>L&iacute;neas de investigaci&oacute;n y profesores</a></li>";
$print_.="<li><a href='#contacto' data-toggle='tab'>Contacto</a></li>";
$print_.="<li><a href='#reglamento' data-toggle='tab'>Reglamento relativo a la Tesis Doctoral</a></li>";
$print_.="</ul>";
return $print_;
}
//Ordena Array de Rows, devuelve Array de Clase Row
//
function ordenaPrioridadComite($rows){
	$i = 1;
	foreach( $rows as $row ) {
	$tabla[$i] = new PrioridadComite($row);
	$i +=1;
	}
        //Nombre de la clase y el nombre de la funcion comparacion
	usort($tabla, array("PrioridadComite", "cmp_obj"));
	return $tabla;
}

/**
 * ###############################
 * #          CLASS              #
 * ###############################
 *
 * */
//El atributo $row_ es una linea de la base de datos.
//Se utiliza para ordenar
/**
 * Wrapper para realizar ordenamiento segun el orden en el comite
 */
class PrioridadComite
{
	//http://php.net/manual/es/function.usort.php
	private $row_;
	public $prioridad = array(
            'COMISION'=> 0,   
	    'PRESIDENTE' => 1,
            'PRESIDENTE Y COORDINADOR' => 2,
            'COORDINADOR' => 2,
            'COORDINADORA' => 2,
            'COORDINADOR CA' => 2,
            'COORDINADOR ADJUNTO' => 2,
            'COORDINADORA ADJUNTA' => 2,
            'COORDINADOR COMISIÓN ACADÉMICA' => 2,
            'COORDINADORA UNIVERSIDAD DE SEVILLA' => 2,
            'COORDINADORA INTERUNIVERSITARIO U. SEVILLA' => 2,
            'COORDINADORA UNIVERSIDAD DE VALENCIA' => 2,     
            'SECRETARIO'=> 3,
            'SECRETARIA'=> 3,
            'VICECOORDINADOR'=> 4,
            'VOCAL'=> 5,
            'VOCAL SUPLENTE'=> 6,
            'AVALISTA'=> 6,
            'SUPLENTE'=> 7
	);
	
	function __construct($row) {
		$this->row_ = $row;
	}
	
	/**
	 * Obtener la prioriridad de la etiqueta, y devolver 
	 */
	
	/* Ésta es la función de comparación estática: */
	static function cmp_obj($a, $b) 
	{
		$al = $a->prioridad[$a->row_->cargo];
		$bl = $b->prioridad[$b->row_->cargo];
		if ($al == $bl) {
			return 0;
		}
		return ($al > $bl) ? +1 : -1;
	}

	public function getRow()
	{
		return $this->row_;
	}
}