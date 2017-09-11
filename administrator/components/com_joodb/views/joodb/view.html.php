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

class JoodbViewJoodb extends JViewLegacy
{
    protected $items = null;
    protected $page = null;
    protected $lists = array();

	function display($tpl = null)
	{
        $app = JFactory::getApplication();

		$text = JText::_( 'Databases' );
		JToolBarHelper::title(   JText::_( "JooDatabase" ).': <small><small>['.$text.']</small></small>','joodb.png' );
		$bar = JToolBar::getInstance('toolbar');
		JoodbAdminHelper::getPopupButton('new','NEW', 'index.php?option=com_joodb&amp;tmpl=component&amp;view=joodbentry&amp;layout=step1&amp;task=addnew', 680, 350);
		JToolBarHelper::divider();
		JToolBarHelper::editList();
		JToolBarHelper::deleteList('Really delete');
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::divider();		
		JToolBarHelper::preferences('com_joodb');
		JToolBarHelper::divider();		
		JToolBarHelper::cancel( 'exitjoodb','close' );		
		$bar->appendButton('Help', 'http://joodb.feenders.de/support.html', false, 'http://joodb.feenders.de/support.html', null);

		// init toolbar
		JoodbAdminHelper::addSubmenu('joodb');

		// Initialize variables
		$db	= JFactory::getDBO();
		$filter	= null;

		// Get some variables from the request
		$context			= 'com_joodb.joodb';
		$filter_order		= $app->getUserStateFromRequest( $context.'filter_order',		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $context.'filter_order_Dir',	'filter_order_Dir',	'DESC',	'word' );
		$filter_state		= $app->getUserStateFromRequest( $context.'filter_state',		'filter_state',		'',	'word' );
		$search				= $app->getUserStateFromRequest( $context.'search',			'search',			'',	'string' );
		$search				= trim(JString::strtolower($search));

		$limit		= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart	= $app->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ( $limit != 0 ? (floor($limitstart / $limit) * $limit) : 0 );

		$order = ' ORDER BY '. $filter_order .' '. $filter_order_Dir;
		$all = 1;

		$where = "";

		// Keyword filter
		if (!empty($search)) {
			if (is_numeric($search)) {
				$where= "WHERE `id`='".(int)$search."'";
			} else {
				$where= "WHERE `name` LIKE ".$db->Quote( '%'.$db->escape( $search, true ).'%', false );
			}
		}

		// Get the total number of records
		$query = "SELECT COUNT('id') FROM `#__joodb` AS c ".$where;
		$db->setQuery($query);
		$total = $db->loadResult();

		// Create the pagination object
		jimport('joomla.html.pagination');
		$this->page = new JPagination($total, $limitstart, $limit);

		// Get the titles
		$query = 'SELECT * FROM `#__joodb` AS c '.$where .$order;
		$db->setQuery($query, $this->page->limitstart, $this->page->limit);
		$this->items = $db->loadObjectList();

		// table ordering
		$this->lists['order_Dir']	= $filter_order_Dir;
        $this->lists['order']		= $filter_order;
        $this->lists['search'] = $search;

        JHTML::_('behavior.modal');
        JHTML::_('behavior.tooltip');
        $document = JFactory::getDocument();
        $document->addScript('components/com_joodb/assets/joodb.js');

		parent::display($tpl);

	}
}
