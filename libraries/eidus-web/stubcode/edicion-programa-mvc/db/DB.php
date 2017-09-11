<?php
//Base de Datos Fabrik - metodos SELECT
class EidusFabrik {
    public static $BBDD = "eidus-fabrik";
    
    public static function t_programas_plan2011(){
        $t_programa = EidusFabrik::t_programas_by_plan('2011');
        return $t_programa;

    }
    //Devuelve tabla t_programas plan 2011
    public static function t_programas_by_plan($plan){
    // Get a db connection.
	$db = JFactory::getDbo();
	// Create a new query object.
	$query = $db->getQuery(true);
	 $query->select(array('*'))
        ->from($db->quoteName(EidusFabrik::$BBDD.'.t_programas', 't'))
         ->where($db->quoteName('t.plan') .'='."'".$plan."'")
        ->order($db->quoteName('t.denominacion') . 'ASC');
         
         // Reset the query using our new query object
	$db->setQuery($query);
	
	// Load the results as a list of stdClass objects (see later for more options on retrieving data).
	$table = $db->loadObjectList();
	
	// Retrieve each value in the ObjectList
	return $table;
    }
    
     //Devuelve tabla t_programas
    public static function t_programas(){
    // Get a db connection.
	$db = JFactory::getDbo();
	// Create a new query object.
	$query = $db->getQuery(true);
	 $query->select(array('*'))
        ->from($db->quoteName(EidusFabrik::$BBDD.'.t_programas', 't'))
        ->order($db->quoteName('t.denominacion') . 'ASC');
         
         // Reset the query using our new query object
	$db->setQuery($query);
	
	// Load the results as a list of stdClass objects (see later for more options on retrieving data).
	$table = $db->loadObjectList();
	
	// Retrieve each value in the ObjectList
	return $table;
    }
    /**
 * Apartir de ID de Programa, devuelve t_Programa
 * @param t_Programa->$id_programa
 * @return t_Programa
 */
public static function row_Programa($id_programa)
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
    
}

