<?php
require_once('libraries/stubcode/model/Programa.php');
const HREF_IMPRESO_ADMISION = "impresos/admision/"; 
class Articulos_Programa_View {

    
    private $programa;
    
    
    public function __construct($programa){
        $this->programa = $programa;
    }

    public function __print(){
        // print --> PANTALLA PRINCIPAL :: TAB
        echo $this->printMainTab();
      
    }
    
    /**
     *  ###############################################################
     *  #                   PANTALLA PRINCIPAL :: TAB                 #
     *  ###############################################################
     *  Tab #descripcion - Tab #acceso - Tab #lineas - Tab #contacto - Tab #reglamento
     */
    private function printMainTab(){
        $print_ = "";
        $print_ .= "<ul class='nav nav-tabs'>";
        $print_ .= "<li class='active'><a href='#descripcion' data-toggle='tab'>Descripci&oacute;n</a></li>";
        $print_ .= "<li><a href='#acceso' data-toggle='tab'>Acceso al programa</a></li>";
        $print_ .= "<li><a href='#lineas' data-toggle='tab'>L&iacute;neas de investigaci&oacute;n y profesores</a></li>";
        $print_ .= "<li><a href='#contacto' data-toggle='tab'>Contacto</a></li>";
        //$print_ .= "<li><a href='#reglamento' data-toggle='tab'>Reglamento relativo a la Tesis Doctoral</a></li>";
        $print_ .= "</ul>";
        $print_ .= "<div class='tab-content pill-content'>";
        /**
         * Tab #descripcion
         */
        $print_ .= "<div id='descripcion' class='tab-pane fade in active pill-pane'><!--DESCRIPCION :: #descripcion-->";
        $print_ .= $this->print_tab_descripcion();
        $print_ .= "</div>";
        /**
         * Tab #acceso
         */
        $print_ .="<div id='acceso' class='tab-pane fade'>";
        $print_ .= $this->print_tab_acceso();
        $print_ .="</div>";
        /**
         * Tab #lineas
         */
        $print_ .="<div id='lineas' class='tab-pane fade'>";
        $print_ .=$this->printProfesorLinea();
        $print_ .="</div>";
        /**
         * Tab #contacto
         */
        $print_ .="<div id='contacto' class='tab-pane fade'>";
        $print_ .=$this->print_tab_contacto();
        $print_ .="</div>";
        /**
         * Tab #reglamento
         
        $print_ .="<div id='reglamento' class='tab-pane fade'>";
        $print_ .=$this->print_tab_reglamento();
        $print_ .="</div>";
        */
        $print_ .="</div>";
        
        return $print_;
    }
    
    /**
     *  ###############################################################
     *  #                  Tab #descripcion                           #
     *  ###############################################################
     *
     */
    
    private function print_tab_descripcion(){
        $print_ = "";
        try {
            $print_ .=$this->printTablaDescripcionMain();
            $print_ .=$this->printTablaComision();
            $print_ .=$this->printTablaOrganosParticipantes();
            // $print_ .=printCentroAdministrativo($id_programa);
            $print_ .=$this->printRegimenPermanencia();
        //    $print_ .=$this->printPlazas();
        }catch(Exception $e){
            echo 'Excepci贸n capturada articulos-programa路php print_tab_descripcion:: ',  $e->getMessage(), "\n";
        }finally{
            return $print_ ;
        }
        
    }
    private function printTablaOrganosParticipantes(){
        $print_ = "";
        $print_ .= "<div>";
        $print_ .= "<h3>&Oacute;rganos participantes</h3>";
        $print_ .= $this->programa->getOrganos_participantes();
        $print_ .="</div>";
        $print_ .= "<br/>";
        return $print_;
    }
    
    /**
     * Imprime Tabla de Descripci髇 principal.
     * @return string
     */
    private function printTablaDescripcionMain(){
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
            $rama = $this->programa->getRama();
            $isced1=$this->programa->getIsced1();
            $isced2=$this->programa->getIsced2();
            $codigo=$this->programa->getCodigo();
            $web=$this->programa->getWeb();
            $denominacion=$this->programa->getDenominacion();
            $interuniversitario= $this->programa->getInteruniversitario();
            $email=$this->programa->getEmail();
            
            
            
        }catch(Exception $e){
            echo "Exception ::".$e;
        }
        
        $print_ .= "<div class='table-responsive'>";
        $print_ .= "<h3>Descripci&oacute;n del programa</h3>";
        $print_ .= "<table class='table table-striped tabla-programa'>";
        $print_ .= "<tbody>";
        
        $print_ .= "<tr>";
        $print_ .= "<td>";
        $print_ .= "<b>Nivel</b>";
        $print_ .= "</td>";
        $print_ .= "<td>Doctorado</td>";
        $print_ .= "</tr>";
        
        $print_ .= "<tr>";
        $print_ .= "<td>";
        $print_ .= "<b>C&oacute;digo ISCED1</b>";
        $print_ .= "</td>";
        $print_ .= "<td>".$isced1."</td>";
        $print_ .= "</tr>";
        
        
        $print_ .= "<tr>";
        $print_ .= "<td>";
        $print_ .= "<b>C&oacute;digo ISCED2</b>";
        $print_ .= "</td>";
        $print_ .= "<td>".$isced2."</td>";
        $print_ .= "</tr>";
        
        $print_ .= "<tr>";
        $print_ .= "<td>";
        $print_ .= "<b>Rama</b>";
        $print_ .= "</td>";
        $print_ .= "<td>".$rama."</td>";
        $print_ .= "</tr>";
        
        
        $print_ .= "<tr>";
        $print_ .= "<td>";
        $print_ .= "<b>C&oacute;digo UXXI</b>";
        $print_ .= "</td>";
        $print_ .= "<td>".$codigo."</td>";
        $print_ .= "</tr>";
        
        $print_ .= "<tr>";
        $print_ .= "<td>";
        $print_ .= "<b>Web oficial</b>";
        $print_ .= "</td>";
        $print_ .= "<td><a href='".$web."' title='".$web."'>".$web."</a></td>";
        $print_ .= "</tr>";
        
        $print_ .= "<tr>";
        $print_ .= "<td>";
        $print_ .= "<b>Email</b>";
        $print_ .= "</td>";
        $print_ .= "<td>".$this->fix_email($email)."</td>";
        $print_ .= "</tr>";
        
        
        $print_ .="</tbody>";
        $print_ .="</table>";
        $print_ .="</div>";
        
        $print_ .="<br />";
        return $print_;
    }
    
    /**
     * Organo responsable del Programa: Comisi髇 Academica
     * printComision
     * @param type $id_programa: id del programa
     */
    private function printTablaComision()
    {
        $print_ = "";
        $array_comision = $this->programa->getArray_comision();
        
       
    //    $array_comision = ordenaPrioridadComite($array_comision);
        
        //@TODO - Cargar plantilla.
        
        
        $print_ .= "<div class='table-responsive tabla-programa'>";
        $print_ .= "<h3>&Oacute;rgano responsable del Programa: Comisi&oacute;n Acad&eacute;mica</h3>";
        $print_ .= "<table class='table table-striped'>";
        $print_ .= "<tbody>";
        foreach($array_comision as $c) {
           
            
            $cargo = mb_convert_encoding(mb_convert_case($c->getCargo(), MB_CASE_TITLE), "UTF-8");
            $nombre = mb_convert_encoding(mb_convert_case($c->getProfesor()->getNombre(), MB_CASE_TITLE), "UTF-8");
            $apellido1 = mb_convert_encoding(mb_convert_case($c->getProfesor()->getApellido1(), MB_CASE_TITLE), "UTF-8");
            $apellido2 = mb_convert_encoding(mb_convert_case($c->getProfesor()->getApellido2(), MB_CASE_TITLE), "UTF-8");
            $email = mb_convert_encoding(mb_convert_case($c->getProfesor()->getEmail(), MB_CASE_TITLE), "UTF-8");
            $sisius_id = $c->getProfesor()->getSisiusid();
          
            //$sisius_id = "3885";
            /**
            $sexo = $c->getProfesor()->getSexo();
            $titulo = "";
            if(strcmp($sexo, "H")){
                $titulo = mb_convert_encoding(mb_convert_case('Do&ntilde;a', MB_CASE_TITLE), "UTF-8");;
            }else{
                $titulo = mb_convert_encoding(mb_convert_case('Don', MB_CASE_TITLE), "UTF-8");;
            }
            */
            
            $print_ .= "<tr>";
            $print_ .= "<td>";
            $print_ .= "<b>".$cargo."</b>";
            $print_ .= "</td>";
            
            
            
            if (strcmp($sisius_id, "") !== 0) {
            $print_ .= "<td><a rel=\"nofollow\" target= \"blank\" href=\"https://investigacion.us.es/sisius/sis_showpub.php?idpers=".$sisius_id."\">".$nombre." ".$apellido1." ".$apellido2."</a></td>";
            $print_ .= "<td><a rel=\"nofollow\" target= \"blank\" href=\"https://investigacion.us.es/sisius/sis_solmail.php?idpers=".$sisius_id."\">Solicitar correo</a></td>";
            }else{
            $print_ .= "<td>". " ". $nombre." ".$apellido1." ". $apellido2 . "</td>";
            }
          /*  if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $e =  JHtml::_('email.cloak',   strtolower($email));
      
                $print_ .= " (".$e.")"; ;
            }else{
                $print_ .= "";
            }*/
         
            
            $print_ .= "</tr>";
        }
        $print_ .= "</tbody>";
        $print_ .= "</table>";
        $print_ .= "</div>";
        $print_ .= "<!--tabla participantes-->";
        
        
        
        
        return $print_;
    }

        
    
    private function fix_email($email){
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
           // $e =  JHtml::_('email.cloak',   strtolower($email));
            $pos = strpos($email, '@');
            $email_ = substr($email, 0, $pos);
            $dominio = substr($email, $pos+1);
            
            $pos = strpos($dominio, '.');
            $dominio = str_replace('.', " <i>'punto'</i> ",  strtolower($dominio));
            
            $ret = "".strtolower($email_) . " <i>'arroba'</i> " . $dominio;
            
            return $ret;
            
        }else{
            return "";
        }
    }
    

    
    /**
     * Imprime tabla de Plazas del programa
     * @return string
     */
    private function printPlazas(){
     //$rows = t_plaza($id_programa);
        $array_plaza = $this->programa->getArray_plaza();
       //Array para imprimir
        $array = [];
        
        //Relleno array
        foreach($array_plaza as $p) {
       
                $curso = $p->getCurso();
                $total = $p->getTotal();
                //Creo el array
                $array[$curso]= $total;
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
    
    
    /**
     * Constante.
     * 
     */
    private function printRegimenPermanencia(){
        $print_ = "";
        $print_.="<h3>R&eacute;gimen de permanencia</h3>";
        $print_.="<p>Tiempo completo y tiempo parcial</p>";
        $print_.="<br/>";
        return $print_;
    }
    /**
     *  ###############################################################
     *  #                  Tab #acceso                                #
     *  ###############################################################
     *
     */
    
    //<div id="acceso" class="tab-pane fade">
    private function print_tab_acceso(){
        $print_ = "";
        try {
            $print_ .=$this->printPlazas();
            //$print_ .=$this->printCentroAdministrativo();
            $print_ .=$this->printEnlacePDF();
         //   $print_ .=$this->printPerfilIngreso();
            
        }catch(Exception $e){
            echo 'Excepci贸n capturada articulos-programa路php print_tab_acceso:: ',  $e->getMessage(), "\n";
        }finally{
            return $print_;
        }
        
    }
    /**
     * Imprime el enlace donde se encuentra el documento de Acceso.
     */
    private function printEnlacePDF(){
        $print_ = "";
        $print_ .= "<h3>Ficha formato PDF</h3>";
        $uxxi = $this->programa->getCodigo();
        $print_ .= "<p><span><a href=\"".HREF_IMPRESO_ADMISION.$uxxi.".pdf\" target='_blank' class='pdf'>Descargar</a></span></p>";
        $print_ .= "<br/>";
        return $print_;
    }
    
    /**
     * El campo acceso tiene guardado en bruto HTML
     * @TODO: Pasar a XML o JSON la informaci贸n que se guarda.
     * @param type $id_programa
     */
    private function printPerfilIngreso(){
        $print_ = "";
        try{
            $print_ = "";
            $print_ .= "<h3>Perfil de Ingreso Recomendado</h3>";
            $print_ .= "<div>".$this->programa->getPerifl_ingreso()."</div>";
            $print_ .= "<br/>";
            return $print_;

        }catch(Exception $e){
            $acceso ="";
        }finally{
            
        }
        return $print_;
    }
    
    /**
     * Imprime Centro Administrativo Responsable a partir de la ID de programa
     * @param t_Programa->$id_programa
     */
    private function printCentroAdministrativo(){
        $print_ = "";
        try{
            $print_ = "";
            $print_ .= "<h3>Contacto administrativo responsable</h3>";
            $print_ .= "<div>".$this->programa->getContacto_administrativo()."</div>";
            $print_ .= "<br/>";
            return $print_;
        }catch(Exception $e){
            $print_ .=$e;
        }finally{
            return $print_;
        }
        
        
    }
    
    /**
     *  ###############################################################
     *  #                  Tab #lineas                                #
     *  ###############################################################
     *
     */
    /**
     * Lineas de Investigaci贸n y Profesores
     * printProfesorLinea
     * @param type $id_programa
     */
    private function printProfesorLinea()
    {
        try{
            $print_ = "";
            $map_codigoLineas_denominacion =  $this->programa-> getMap_codigoLineas_denominacion();
            $elements =  $this->programa->getMap_array_lineasProfesor();
            //Obtengo las Key y las ordeno.
            $codigosLinea = array_keys($elements);
            sort($codigosLinea);
                        
            $print_ .= "<h3>Lineas de Investigaci&oacute;n y Profesores</h3>";
            $print_ .= "<div class='panel-group accordion' id='accordion'>";
            
            foreach($codigosLinea as $codigoLinea) {
                $print_ .= "<div class='panel panel-default accordion-group'>";
                $print_ .= "<div class='panel-heading accordion-heading'>";
                $print_ .= "<h4 class='panel-title'><a href='#".$codigoLinea."' data-toggle='collapse' data-parent='#accordion'>".$codigoLinea." - ".($map_codigoLineas_denominacion[$codigoLinea])."</a></h4>";
                $print_ .= "</div>";
                $print_ .= "<div id='".$codigoLinea."' class='panel-collapse collapse'>";
                $print_ .= "<div class='panel-body accordion-inner'>";
                
                $print_ .= "<ol>";
                $array_profesores = $elements[$codigoLinea];
                
    
                foreach ($array_profesores as $profesor){
                    $sexo = $profesor->getSexo();
                  /*  $titulo = "";
                    if(strcmp($sexo, "H")){
                        $titulo = mb_convert_encoding(mb_convert_case('Do&ntilde;a', MB_CASE_TITLE), "UTF-8");;
                    }else{
                        $titulo = mb_convert_encoding(mb_convert_case('Don', MB_CASE_TITLE), "UTF-8");;
                    }*/
                    $nombre = mb_convert_encoding(mb_convert_case($profesor->getNombre(), MB_CASE_TITLE), "UTF-8");
                    $apellido1 = mb_convert_encoding(mb_convert_case($profesor->getApellido1(), MB_CASE_TITLE), "UTF-8");
                    $apellido2 = mb_convert_encoding(mb_convert_case($profesor->getApellido2(), MB_CASE_TITLE), "UTF-8");
                    $apellidos = $apellido1. " ".$apellido2;
                    //$id = $element->id;
                    $email = $this->fix_email($profesor->getEmail());
                    $sisius_id = $profesor->getSisiusid();
                    
                    $email = "<a rel=\"nofollow\" target= \"blank\" href=\"https://investigacion.us.es/sisius/sis_solmail.php?idpers=".$sisius_id."\">Solicitar correo</a>";
                    
                    if (strcmp($sisius_id, 0) !== 0) {
                        $print_ .= "<li><a rel=\"nofollow\" target= \"blank\" href=\"https://investigacion.us.es/sisius/sis_showpub.php?idpers=".$sisius_id."\">".$apellidos.", ".$nombre." (".$email .")</a></li>";
                    //    $print_ .= "<li value=\"".$li_value."\">".$apellidos.", ".$nombre." (".$email .")</li>";
                    }else{
                        $print_ .= "<li><a>".$apellidos.", ".$nombre."</a></li>";
                 //       $print_ .= "<li value=\"".$li_value."\">".$apellidos.", ".$nombre."</li>";
                    }
                  
                    
             
                }
                $print_ .= "</ol>";
                $print_ .= "</div></div></div>";
                
            }
            $print_ .= "</div>";
            
            
        }catch(Exception $e){
            
        }finally{
            return $print_;
        }
        
        
    }
    /**
     *  ###############################################################
     *  #                  Tab #contacto                              #
     *  ###############################################################
     *
     */
    private function print_tab_contacto(){
        $print_ = "";
        $print_ .= $this->print_Contacto_Administrativo(); 
        $print_ .= $this->print_Contacto_Academico();
        return $print_;
    }
    private function print_Contacto_Administrativo(){
        $print_ = "";
        $print_ .= "<h3>Contacto Administrativo</h3>";
        $print_ .= "<div>".$this->programa->getContacto_administrativo()."</div>";
        $print_ .= "<br/>";
        return $print_;
    }
    function print_Contacto_Academico(){
        $print_ = "";
        $print_ .= "<h3>Contacto Acad&eacute;mico</h3>";
        $print_ .= "<div>".$this->programa->getContacto_academico()."</div>";
        $print_ .= "<br/>";
        return $print_;
    }
    
    /**
     *  ###############################################################
     *  #                  Tab #reglamento                                #
     *  ###############################################################
     *
     */
    
    /**
     * Imprime una constante HTML al Reglamento Relativo.
     * @param type $id_programa
     */
    private function print_tab_reglamento(){
        return $this->printReglamentoRelativo();
    }
    private function printReglamentoRelativo(){
        $print_ = "";
        $print_ .= "<h3>Reglamento Relativo a la Tesis Doctoral</h3>";
        $print_ .= "<p><span>Visitar la <a href='index.php?option=com_content&amp;view=article&amp;id=62&amp;Itemid=185' title='Normativa de R&eacute;gimen de Tesis Doctoral de la Universidad de Sevilla'>Normativa de R&eacute;gimen de Tesis Doctoral de la Universidad de Sevilla</a></span></p>";
        $print_ .= "<p><span><a href='http://bous.us.es/2011/numero-4/pdf/archivo-12.pdf' target='_blank' class='pdf'>Acuerdo 7.2/CG 17-6-11</a> por el que se aprueba la Normativa de Estudios de Doctorado conforme a lo establecido en el R.D. 99/2011.</span></p>";
        //    $print_ .= "<p>&nbsp;</p>";
        return $print_;
    }
    
    /****
     * #############################################
     * #            FUNCIONES AUXILIARES           #
     * #############################################
     * */
    
    //Ordena Array de Rows, devuelve Array de Clase Row
    //
    private function ordenaPrioridadComite($array_comision){
        $i = 1;
        foreach( $rows as $row ) {
            $tabla[$i] = new PrioridadComite($row);
            $i +=1;
        }
        //Nombre de la clase y el nombre de la funcion comparacion
        usort($tabla, array("PrioridadComite", "cmp_obj"));
        return $tabla;
    }
    
    

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
        'COORDINADOR COMISI脫N ACAD脡MICA' => 2,
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
    
    /* 脡sta es la funci贸n de comparaci贸n est谩tica: */
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