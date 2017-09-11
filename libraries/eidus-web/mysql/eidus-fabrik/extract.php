<?php

/**
 * Devuelve un email a partir de una id de un t_profesor
 * @param t_profesor $id
 * @return t_profesor->email
 */
function email_byID($id){
  

$db = JFactory::getDbo();
$query = $db->getQuery(true);
$query->select('email');
$query->from($db->quoteName('eidus-fabrik.t_profesor'));
$query->where($db->quoteName('id')." = ".$db->quote($id));
 
$db->setQuery($query);
$result = $db->loadResult();

return $result;
}

/**
 * Dado un id de un Programa, te devuelve un join de los profesores que pertenecen a un Programa
 * @param t_profesor_linea->$id_programa
 * @return join (t_profesor_linea.t_lineas_inves.t_profesor)
 */
function t_ProfesoresPrograma($id_programa){
    // Get a db connection.
	$db = JFactory::getDbo();
	// Create a new query object.
	$query = $db->getQuery(true);
        
        $query
	->select(array('pr.*', 'l.codigo', 'l.denominacion'))
	->from($db->quoteName('eidus-fabrik.t_profesor', 'pr'))
	->join('RIGHT', $db->quoteName('eidus-fabrik.t_profesor_linea', 'pl') . ' ON (' . $db->quoteName('pr.id') . ' = ' . $db->quoteName('pl.idprofesor') . ')')
        ->join('INNER', $db->quoteName('eidus-fabrik.t_lineas_inves', 'l') . ' ON (' . $db->quoteName('l.id') . ' = ' . $db->quoteName('pl.idlineainvestigacion') . ')')
        //->join('INNER', $db->quoteName('eidus-fabrik.t_programas', 'pr') . ' ON (' . $db->quoteName('pr.id') . ' = ' . $db->quoteName('l.programa') . ')')
        ->order($db->quoteName('pl.idlineainvestigacion') . 'ASC')
        ->order($db->quoteName('pr.apellidos') . 'ASC')
	->where($db->quoteName('l.programa') .'='.$id_programa);
        
        // Reset the query using our new query object
	$db->setQuery($query);
	
	// Load the results as a list of stdClass objects (see later for more options on retrieving data).
	$rows = $db->loadObjectList();
	
	// Retrieve each value in the ObjectList
	return $rows;
}





function t_lineas_inves($id_programa){
    $db = JFactory::getDbo();
$query = $db->getQuery(true);
$query->select('*');
$query->from($db->quoteName('eidus-fabrik.t_lineas_inves'));
$query->where($db->quoteName('programa')." = ".$db->quote($id_programa));
 
$db->setQuery($query);
// Load the results as a list of stdClass objects (see later for more options on retrieving data).
	$rows = $db->loadObjectList();

return $rows;
}

function t_plaza($id_programa){
     // Get a db connection.
	$db = JFactory::getDbo();
	// Create a new query object.
	$query = $db->getQuery(true);
        
        $query
	->select(array('plaza.*'))
        ->from($db->quoteName('eidus-fabrik.t_plaza', 'plaza'))
        ->where($db->quoteName('plaza.id_programa') .'='.$id_programa);

        // Reset the query using our new query object
	$db->setQuery($query);
	
	// Load the results as a list of stdClass objects (see later for more options on retrieving data).
	$rows = $db->loadObjectList();
	
	// Retrieve each value in the ObjectList
	return $rows;

}

function t_programas_plan2011(){
    // Get a db connection.
	$db = JFactory::getDbo();
	// Create a new query object.
	$query = $db->getQuery(true);
	 $query->select(array('*'))
        ->from($db->quoteName('eidus-fabrik.t_programas', 't'))
         ->where($db->quoteName('t.plan') .'='.'2011')
        ->order($db->quoteName('t.denominacion') . 'ASC');
         
         // Reset the query using our new query object
	$db->setQuery($query);
	
	// Load the results as a list of stdClass objects (see later for more options on retrieving data).
	$table = $db->loadObjectList();
	
	// Retrieve each value in the ObjectList
	return $table;
}

//Apartir de ID de Programa, devuelve 
function t_ProfesorComision($id_programa)
{
	// Get a db connection.
	$db = JFactory::getDbo();
	// Create a new query object.
	$query = $db->getQuery(true);
	
	// build the SQL query
	//'SELECT cargo, nombre, apellidos, programa FROM t_profesor INNER JOIN t_comision ON t_profesor.id=t_comision.profesor WHERE programa=2;'
	
	$query
	->select(array('c.*', 'p.*'))
	->from($db->quoteName('eidus-fabrik.t_profesor', 'p'))
	->join('INNER', $db->quoteName('eidus-fabrik.t_comision', 'c') . ' ON (' . $db->quoteName('p.id') . ' = ' . $db->quoteName('c.profesor') . ')')
	->order($db->quoteName('c.cargo') . 'DESC')
	->where($db->quoteName('c.programa') .'='.$id_programa);
	
	
	// Reset the query using our new query object
	$db->setQuery($query);
	
	// Load the results as a list of stdClass objects (see later for more options on retrieving data).
	$rows = $db->loadObjectList();
	
	// Retrieve each value in the ObjectList
	return $rows;
}


/**
 * Apartir de ID de Programa, devuelve t_Programa
 * @param t_Programa->$id_programa
 * @return t_Programa
 */
function row_Programa($id_programa)
{
 
$db = JFactory::getDbo();
$query = $db->getQuery(true);
$query->select('*');
$query->from($db->quoteName('eidus-fabrik.t_programas'));
$query->where($db->quoteName('id')." = ".$db->quote($id_programa));
 
$db->setQuery($query);
$row = $db->loadAssoc();

return $row;
    
}


function t_OrganoParticipante($id_programa)
{
 
// Get a db connection.
	$db = JFactory::getDbo();
	// Create a new query object.
	$query = $db->getQuery(true);
        
        $query
	->select(array('organo.*'))
        ->from($db->quoteName('eidus-fabrik.t_organos', 'organo'))
        ->where($db->quoteName('organo.idprograma') .'='.$id_programa);

        // Reset the query using our new query object
	$db->setQuery($query);
	
	// Load the results as a list of stdClass objects (see later for more options on retrieving data).
	$rows = $db->loadObjectList();
	
	// Retrieve each value in the ObjectList
	return $rows;
    
}

/**
 * Apartir de ID de Programa, devuelve row de Centro Administrativo
 * @param t_Programa->$id_programa
 * @return t_Programa
 */
function row_CentroAdministrativo($id_programa)
{
 
$db = JFactory::getDbo();
$query = $db->getQuery(true);
$query->select('*');
$query->from($db->quoteName('eidus-fabrik.t_centro_administrativo'));
$query->where($db->quoteName('idprograma')." = ".$db->quote($id_programa));
 
$db->setQuery($query);
$row = $db->loadAssoc();

return $row;
    
}