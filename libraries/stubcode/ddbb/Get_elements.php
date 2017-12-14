<?php

require_once('libraries/stubcode/model/Programa.php');
require_once('libraries/stubcode/model/Profesor.php');
require_once('libraries/stubcode/model/Comision.php');
require_once('libraries/stubcode/model/Plaza.php');
require_once('libraries/stubcode/model/Usuario.php');
require_once('libraries/stubcode/model/Actividad.php');


class Get_elements {
    /**
     ###############################################
     # Extrae Informacion para Formacion Doctoral  #
     ###############################################
     */ 
    /**
     * Devuelve un Array de Actividad
     */
    

    
    function get_Actividades(){
       
        
        ################################
        # Extrae Informacion de fabrik #
        ################################

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        
        $query
        ->select(array('u.*','a.*'))
        ->from($db->quoteName('tolteca-fabrik.usuarios', 'u'))
        ->join('RIGHT', $db->quoteName('tolteca-fabrik.actividades', 'a') . ' ON (' . $db->quoteName('u.id') . ' = ' . $db->quoteName('a.gestor') . ')')
       // ->join('INNER', $db->quoteName('tolteca-fabrik.actividades_documentacion', 'ac') . ' ON (' . $db->quoteName('ac.parent_id') . ' = ' . $db->quoteName('a.id') . ')')
        //->join('INNER', $db->quoteName('eidus-fabrik.t_programas', 'pr') . ' ON (' . $db->quoteName('pr.id') . ' = ' . $db->quoteName('l.programa') . ')')
        ->order($db->quoteName('a.fecha_ini') . 'DESC')
        ->order($db->quoteName('a.fecha_fin') . 'DESC');
        //   ->where($db->quoteName('a.id') .'='.$db->quote($codigoPrograma));
            
            
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        $array_actividades = array();
        
        foreach ($rows as $row){
            $act = new Actividad();
            $act->setTitulo($row->titulo);
            $act->setDescripcion($row->descripcion);
            $act->setFecha_fin($row->fecha_fin);
            $act->setFecha_ini($row->fecha_ini);
            $act->setHoras($row->horas);
            $act->setDocumentacion($row->documentacion);
            
            $usuario = new Usuario();
            $usuario->setBloqueado($row->bloqueado);
            $usuario->setCreador($row->creador);
            $usuario->setEmail($row->email);
            $usuario->setGrupos($row->grupos);
            $usuario->setId($row->id);
            $usuario->setNombre($row->nombre);
            $usuario->setUserid($row->userid);
            $usuario->setUsuario($row->usuario);
            
            $act->setGestor($usuario);
            $act->setHorario($row->horario);
            $act->setLugar($row->lugar);
            $act->setPlazas($row->plazas);
            $act->setPlazo_inscripcion($row->plazo_inscripcion);
            $act->setCodigo_solicitud($row->codigo_solicitud);
            $act->setCodigo_ice($row->codigo_ice);
            $act->setFormador($row->formador);
            $act->setFinanciacion($row->financiacion);
            $act->setContacto($row->contacto);
            $act->setImpartido($row->impartido);
            $act->setBloque($row->bloque);
            $act->setObservaciones($row->observaciones);
            $act->setGestion($row->gestion);
            $act->setEnlace($row->enlace);

            array_push($array_actividades, $act);
        }
        
        //print_r($rows);
        return $array_actividades;

    }
    /**
     * Devuelve el Usuario con el ID.
     * @param ID de Usuario: $id
     */
    function get_Usuario($id){
        
        /**
         ################################
         # Extrae Informacion de fabrik #
         ################################
         */
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('tolteca-fabrik.usuarios'));
        $query->where($db->quoteName('id')." = ".$db->quote($id));
        $db->setQuery($query);
        $row = $db->loadAssoc();
        
        $usuario = new Usuario();
        $usuario->setBloqueado($row['bloqueado']);
        $usuario->setCreador($row['creador']);
        $usuario->setEmail($row['email']);
        $usuario->setGrupos($row['grupos']);
        $usuario->setId($row['id']);
        $usuario->setNombre($row['nombre']);
        $usuario->setUserid($row['userid']);
        $usuario->setUsuario($row['usuario']);
            
        return $usuario;
        
    }
    
    /**
     * @param empty
     * @return Devuelve Array con todos los programas.
     */
    function getProgramas(){
        /**
         ################################
         # Extrae Informacion de fabrik #
         ################################
         */
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('tolteca-fabrik.programas'))->order($db->quoteName('denominacion') . 'ASC');
       // $query
        
        $db->setQuery($query);
        // Load the results as a list of stdClass objects (see later for more options on retrieving data).
        $rows = $db->loadObjectList();
        
        $array_programa = array();
        //Relleno array
        foreach($rows as $row) {
                
            /**
             ################################
             # Creo objeto de Programa      #
             ################################
             */
            
            $p = new Programa();
            $p->setDenominacion($row->denominacion);
            $p->setEmail($row->email);
            $p->setId($row->id);
            $p->setInteruniversitario($row->interuniversitario);
            $p->setIsced1($row->isced1);
            $p->setIsced2($row->isced2);
            $p->setPlan($row->plan);
            $p->setWeb($row->web);
            $p->setCodigo($row->codigo);
            $p->setRama($row->rama);
            
            $p->setArray_plaza( $this->get_Array_Plaza($row->id));
            
            array_push ( $array_programa , $p);
        }
        
        
        
        return $array_programa;
    }
    
    
    
    
   
    /**
     * Apartir de Codigo de Programa, devuelve Programa
     * @param $codigoPrograma
     * @return Programa
     */
    function get_Programa($codigoPrograma)
    {
      
        /**
            ################################
            # Extrae Informacion de fabrik #
            ################################
        */ 
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('tolteca-fabrik.programas'));
        //$query->from($db->quoteName('eidus-fabrik.t_programas'));
        $query->where($db->quoteName('codigo')." = ".$db->quote($codigoPrograma))->order($db->quoteName('rama') . 'DESC');
        
        $db->setQuery($query);
        $row_Programa = $db->loadAssoc();
        
        
        
        /**
         ################################
         # Obtengo Comision del Programa  #
         ################################
         */
        
        $array_comision = $this->get_Array_Comision($row_Programa["id"]);
        //Ordeno Array
        asort($array_comision);
        
        
        
        /**
         ################################
         # Obtengo coordinador del programa  #
         ################################
         */
        
        $coordinador = $this->get_Profesor($row_Programa["coordinador"]);
        
        
        /**
         ################################
         # Obtengo plaza del programa  #
         ################################
         */
        
        $array_plaza = $this->get_Array_Plaza($row_Programa["id"]);
        
        /**
         ################################
         # Obtengo los profesores de las lineas del programa  #
         ################################
         */
        $map_array_lineasProfesor = $this->get_Profesores_Lineas($row_Programa["id"]);
        
        /**
         ################################
         # Obtengo Map con el Nombre de Lineas y Codigo  #
         ################################
         */
        $map_codigoLineas_denominacion = $this->get_CodigoLinea_Nombres($row_Programa["id"]);
        
        /**
         ################################
         # Creo objeto de Programa      #
         ################################
         */ 
        
        $p = new Programa();   
        $p->setDenominacion($row_Programa["denominacion"]);
        $p->setEmail($row_Programa["email"]);
        $p->setId($row_Programa["id"]);
        $p->setInteruniversitario($row_Programa["interuniversitario"]);
        $p->setIsced1($row_Programa["isced1"]);
        $p->setIsced2($row_Programa["isced2"]);
        $p->setPlan($row_Programa["plan"]);
        $p->setWeb($row_Programa["web"]);
        $p->setCodigo($row_Programa["codigo"]);
        $p->setRama($row_Programa["rama"]);
        
        $p->setArray_plaza($array_plaza);
        $p->setCoordinador($coordinador);
        $p->setArray_comision($array_comision);
        $p->setMap_array_lineasProfesor($map_array_lineasProfesor);
        $p->setMap_codigoLineas_denominacion($map_codigoLineas_denominacion);
           
        $p->setContacto_administrativo($row_Programa["contacto_administrativo"]);
        $p->setOrganos_participantes($row_Programa["organos_participantes"]);
        $p->setContacto_academico($row_Programa["contacto_academico"]);
        
    
        return $p;

    }
    /**
     * Apartir de ID Profesor, devuelve Profesor
     * @param $id_profesor
     * @return Profesor
     */
    function get_Profesor($id_profesor){
        /**
         ################################
         # Extrae Informacion de fabrik #
         ################################
         */
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('tolteca-fabrik.profesores'));
        $query->where($db->quoteName('id')." = ".$db->quote($id_profesor));
        
        $db->setQuery($query);
        $row_Profesor = $db->loadAssoc();
        
        /**
         ################################
         # Creo objeto de Profesor      #
         ################################
         */ 
        $p = new Profesor();
        $p->setApellido1($row_Profesor['apellido1']);
        $p->setApellido2($row_Profesor['apellido2']);
        $p->setDni($row_Profesor['dni']);
        $p->setEmail($row_Profesor['email']);
        $p->setExterno($row_Profesor['externo']);
        $p->setIdPrograma($row_Profesor['programa']);
        $p->setId($row_Profesor['id']);
        $p->setNombre($row_Profesor['nombre']);
        $p->setSexo($row_Profesor['sexo']);
        $p->setSisiusid($row_Profesor["sisiusid"]);
        
        return $p;
    }

    
    /**
     * Apartir de ID Programa, devuelve Listado de Comision
     * @param $id_programa
     * @return Array<Comision>
     */
    function get_Array_Comision($id_programa)
    {
        $array_comision = [];
        // Get a db connection.
        $db = JFactory::getDbo();
        // Create a new query object.
        $query = $db->getQuery(true);
       
        
        // build the SQL query
        //'SELECT cargo, nombre, apellidos, programa FROM t_profesor INNER JOIN t_comision ON t_profesor.id=t_comision.profesor WHERE programa=2;'
        
        $query
        ->select(array('c.*', 'p.*'))
        ->from($db->quoteName('tolteca-fabrik.profesores', 'p'))
        ->join('INNER', $db->quoteName('tolteca-fabrik.programas_comisiones', 'c') . ' ON (' . $db->quoteName('p.id') . ' = ' . $db->quoteName('c.miembro') . ')')
        ->order($db->quoteName('c.cargo') . 'DESC')
        ->where($db->quoteName('c.parent_id') .'='.$id_programa);
        
        
        // Reset the query using our new query object
        $db->setQuery($query);
        
        // Load the results as a list of stdClass objects (see later for more options on retrieving data).
        $rows = $db->loadObjectList();
      
      
        foreach ($rows as $valor){
            //echo $i . "---" . print_r($valor); 
            $p = new Profesor();
            $c = new Comision();
            
            $p->setApellido1($valor->apellido1);
            $p->setApellido2($valor->apellido2);
            $p->setDni($valor->dni);
            $p->setEmail($valor->email);
            $p->setExterno($valor->externo);
            $p->setId($valor->miembro);
            $p->setIdPrograma($valor->parent_id);
            $p->setNombre($valor->nombre);
            $p->setSexo($valor->sexo);
            $p->setSisiusid($valor->sisiusid);
            $c->setCargo($valor->cargo);
            $c->setIdPrograma($valor->parent_id);
            $c->setProfesor($p);
            
            array_push($array_comision, $c);
           
        }
        
        // Retrieve each value in the ObjectList
        return $array_comision;
    }
    
    /**
     * A partir de un ID del Programa devuelve un Array de Plaza del programa
     * @param $id_programa
     * @return array<Plaza>
     */
    function get_Array_Plaza($id_programa){
        // Get a db connection.
        $db = JFactory::getDbo();
        // Create a new query object.
        $query = $db->getQuery(true);
        
        $query
        ->select(array('plaza.*'))
        ->from($db->quoteName('tolteca-fabrik.programas_plazas', 'plaza'))
        ->where($db->quoteName('plaza.parent_id') .'='.$id_programa);
        
       
        
        // Reset the query using our new query object
        $db->setQuery($query);
        
        // Load the results as a list of stdClass objects (see later for more options on retrieving data).
        $rows = $db->loadObjectList();
              
        //Array para imprimir
        $array_plaza = [];
      
        //Relleno array
        foreach($rows as $row) {
                $curso = $row->curso;
                $total = $row->numero;
                $idPrograma = $row->parent_id;
                $id = $row->id;
                
                //Creo el array
                $p = new Plaza();
                $p->setId($id);
                $p->setIdPrograma($idPrograma);
                $p->setCurso($curso);
                $p->setTotal($total);
                $array_plaza[$curso] = $p;

        }
      
        // Retrieve each value in the ObjectList
        return $array_plaza;
        
    }
    function get_CodigoLinea_Nombres($id_programa){
        // Get a db connection.
        $db = JFactory::getDbo();
        // Create a new query object.
        $query = $db->getQuery(true);
        
        $query
        ->select(array('pr.*'))
        ->from($db->quoteName('tolteca-fabrik.lineas', 'pr'))
        ->order($db->quoteName('pr.codigo') . 'ASC')
        ->where($db->quoteName('pr.programa') .'='.$id_programa);
        
        // Reset the query using our new query object
        $db->setQuery($query);
        // Load the results as a list of stdClass objects (see later for more options on retrieving data).
        $rows = $db->loadObjectList();
        
       
        foreach($rows as $row) {
            $map_codigoLineas_denominacion[$row->codigo] = $row->denominacion;
        }

        return $map_codigoLineas_denominacion;
    }
    
    
    
    /**
     * Dado un id de un Programa, te devuelve un HashMap de Key:Linea_Profesores y Value: Array de Profesor
     * @param $id_programa
     * @return Map<Codigo_linea, <Array<Profesores>>
     */
    function get_Profesores_Lineas($id_programa){
        // Get a db connection.
        $db = JFactory::getDbo();
        // Create a new query object.
        $query = $db->getQuery(true);
        
        $query
        ->select(array('pr.*', 'l.codigo', 'l.denominacion'))
        ->from($db->quoteName('tolteca-fabrik.profesores', 'pr'))
        ->join('RIGHT', $db->quoteName('tolteca-fabrik.lineas_profesores', 'pl') . ' ON (' . $db->quoteName('pr.id') . ' = ' . $db->quoteName('pl.profesores') . ')')
        ->join('LEFT', $db->quoteName('tolteca-fabrik.lineas_invitados', 'pi') . ' ON (' . $db->quoteName('pr.id') . ' = ' . $db->quoteName('pi.invitados') . ')')
        ->join('INNER', $db->quoteName('tolteca-fabrik.lineas', 'l') . ' ON (' . $db->quoteName('l.id') . ' = ' . $db->quoteName('pl.parent_id') . ')')
        //->join('INNER', $db->quoteName('eidus-fabrik.t_programas', 'pr') . ' ON (' . $db->quoteName('pr.id') . ' = ' . $db->quoteName('l.programa') . ')')
        //->order($db->quoteName('pr.id') . 'ASC')
        ->order($db->quoteName('pr.apellido1') . 'ASC')
        ->where($db->quoteName('l.programa') .'='.$id_programa);
        
        // Reset the query using our new query object
        $db->setQuery($query);
        
        // Load the results as a list of stdClass objects (see later for more options on retrieving data).
        $rows = $db->loadObjectList();
        

        $map_array = array();
        //Relleno array
        foreach($rows as $row) {
            $__codigo = $row->codigo;
            
            $profesor = new Profesor();
            $profesor->setApellido1($row->apellido1);
            $profesor->setApellido2($row->apellido2);
            $profesor->setDni($row->dni);
            $profesor->setEmail($row->email);
            $profesor->setSexo($row->sexo);
            $profesor->setExterno($row->externo);
            $profesor->setId($row->id);
            $profesor->setIdPrograma($id_programa);
            $profesor->setNombre($row->nombre);
            $profesor->setSisiusid($row->sisiusid);
            
            try{
                 //Obtengo el Array de Profesores Linea asociado a la línea
                if(isset($map_array[$__codigo])){
                    $array_profesores_linea = $map_array[$__codigo];
                   
                }else{
                    $array_profesores_linea = array();
                } 
                 //Guardo Profesor en el Array.
                 array_push($array_profesores_linea, $profesor);
                 //Guardo el Array en el Map de línea.
                 $map_array[$__codigo]= $array_profesores_linea;
            }catch(Exception $e){
                echo $e;
            }
           
            
        }
        
        // Retrieve each value in the ObjectList
        return $map_array;
    }
    
   

}
