<?php
/**
* @package		JooDatabase - http://joodb.feenders.de
* @copyright	Copyright (C) Computer - Daten - Netze : Feenders. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @author		Dirk Hoeschen (hoeschen@feenders.de)
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');

/**
 * JooDatabase Component single item Model
 */
class JoodbModelForm extends JModelLegacy
{
	/**
	 * Entry Item Object
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * Database Object
	 *
	 * @var object
	 */
	var $_joobase = null;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$app = JFactory::getApplication();
		$params	= $app->getParams();
		$joobase = $params->get("joobase",0);
		// Load the Database parameters
		if ($joobase==0) $joobase = JRequest::getInt('joobase', 1);
		$this->_joobase = JTable::getInstance( 'joodb', 'Table' );
		if (!$this->_joobase->load( $joobase)) JError::raiseError( 500, $this->_joobase->getError());
		if ($this->_joobase->published==0) JError::raiseError( 404, JText::sprintf( 'Database is unpublished or not availiable'));
		$this->_db = $this->_joobase->getTableDBO();

		// access allowed... redirect to login if not
		JoodbHelper::checkAuthorization($this->_joobase,"accessf");

		// get the table field list with fieldinfo
		$this->_joobase->fields = $this->_db->getTableColumns($this->_joobase->table,false);

	}

	/**
	 * Get Object from JooDB table
	 *
	 * @access public
	 * @return single object
	 */
	public function getJoobase()
	{
		return	$this->_joobase;
	}

	/**
	 * Method to get Data from table in Database
	 *
     * @param id integer
	 * @access public
	 * @return array
	 */
	public function getData($id=null)
	{
        // Lets load the content if it doesn't already exist
        if (empty($this->_data))
        {
            /* Query single object. */
            if (empty($id)) $id = JRequest::getInt("id");
            if (!empty($id)) {
                $this->_db->setQuery('SELECT * FROM `'.$this->_joobase->table
                    . '` WHERE `'.$this->_joobase->fid.'`='.$id.' LIMIT 1;');
                $this->_data = $this->_db->loadObject();
            }
        }

        // empty object if not found
        if (empty($this->_data)) {
            $this->_data = new JObject();
            foreach ($this->_joobase->fields AS &$field)
                $this->_data->{$field->Field} = null;
        }

        return $this->_data;
	}

}
?>
