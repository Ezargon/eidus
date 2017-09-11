<?php
/**
* @package		JooDatabase - http://joodb.feenders.de
* @copyright	Copyright (C) Computer - Daten - Netze : Feenders. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @author		Dirk Hoeschen (hoeschen@feenders.de)
*/

// no direct access
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

class JoodbViewJoodbentry extends JViewLegacy
{
	var $bar = null;
	var $version = null;
	var $fields = array();
	var $tables = array();
	var $config = array();	

	function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();
		$this->config = JComponentHelper::getParams('com_joodb');		
		
		$this->version = new JVersion();
		$this->bar = JToolBar::getInstance('toolbar');

		$layout	= JRequest::getCmd('layout');
		if ($layout=="step1") {
	 		JToolBarHelper::title(JText::_( "Step1 choose Table" ), 'cpanel.png' );
			$this->bar->appendButton('Standard', 'forward arrow-right-2', JText::_( "Continue" ), 'addnew',false);
			$db = $this->getDbo();
			$this->tables = $db->getTableList();
		} else if ($layout=="extern") {
	 		JToolBarHelper::title(JText::_( "External Database" ), 'cpanel.png' );
            $this->bar->appendButton('Standard', 'cancel', JText::_( "Back" ), 'cancel',false);
			$this->bar->appendButton('Standard', 'forward', JText::_( "Continue" ), 'addnew',false);
		} else if ($layout=="step2") {
	 		JToolBarHelper::title(JText::_( "Step2 define Fields" ), 'cpanel.png' );
			$this->bar->appendButton('Standard', 'forward', JText::_( "Continue" ), 'addnew',false);
			$this->dbtable = JRequest::getVar('dbtable');
			$this->dbname = JRequest::getVar('dbname');
			$db = $this->getDbo();
            $this->fields = $db->getTableColumns($this->dbtable,false);
		} else if ($layout=="step3") {
			// Add new entry
			$post = JRequest::get( 'post' );
			$item = JTable::getInstance('joodb', 'Table');
			if (!$item->save( $post )) JError::raiseWarning( 500, $item->getError() );
	 		JToolBarHelper::title(JText::_( "Step3 no step" ), 'cpanel.png' );
			$this->bar->appendButton('Standard', 'cancel', JText::_( "close" ), 'close',false);
		} else {
			$cid	= JRequest::getVar( 'cid', array(), '', 'array' );
			JArrayHelper::toInteger( $cid );
			$id = $cid[0];
			JToolBarHelper::apply();
			JToolBarHelper::save();
			JToolBarHelper::cancel();
			$bar = JToolBar::getInstance('toolbar');
			$bar->appendButton('Help', 'http://joodb.feenders.de/support.html', false, 'http://joodb.feenders.de/support.html', null);

			$item = JTable::getInstance( 'joodb', 'Table' );
			if (!$item->load( $id )) {
				$app->enqueueMessage( JText::_($item->getError()), 'error' );
			} else {
				$tdb = $item->getTableDBO();
				$tdb->setQuery("SHOW COLUMNS FROM `".$item->table."`");
				$this->fields = $tdb->loadObjectList();
				$this->tables = $tdb->getTableList();
			}
			$item->subitems = $item->getSubitems();
							
			JHtml::_('behavior.tooltip');		
			$params = JForm::getInstance('config_items',JPATH_COMPONENT_ADMINISTRATOR.'/config_items.xml',array('control' => 'params', 'load_data' => true),  false,'/config');
			$params->bind($item->getParameters());
			$this->assignRef('params',$params);			
			$this->assignRef('item',$item);

			JToolBarHelper::title( (!empty($item->name) ? $item->name : JText::_( "JooDatabase" )).': <small><small>['.JText::_( 'Edit' ).']</small></small>','joodb.png' );				
			JRequest::setVar( 'hidemainmenu', 1 );
		}

        $jquery = is_file(JPATH_LIBRARIES . '/cms/html/jquery.php');
        if (!$jquery) {
            $document->addScript(JURI::root(true) . '/media/joodb/js/jquery.min.js');
            $document->addScriptDeclaration('jQuery.noConflict();');
        } else {
            JHtml::_('jquery.framework');
        }
        JHTML::_('behavior.formvalidation');
        JHtml::_('behavior.keepalive');
        JHTML::_('behavior.tooltip');

		parent::display($tpl);
	}

	/**
	 * Get external DB if external server ...
	 * @return JDatabase
	 */
	function getDbo() {
		if (!empty($_POST['server'])) {
			$options = array ('host' => JRequest::getVar('server'), 'user' => JRequest::getVar('user'), 'password' => JRequest::getVar('pass'), 'database' => JRequest::getVar('database'), 'prefix' => '');
			$db = JDatabase::getInstance($options);
			if (JError::isError($db)) {
				$this->setError(JText::_('Database Error: ' . (string) $db));
				return false;
			}
			if ($db->getErrorNum() > 0) {
				$this->setError('Database Error: ' .$db->getErrorMsg());
				return false;
			}
			return $db;
		}
		return JFactory::getDbo();
	}

}