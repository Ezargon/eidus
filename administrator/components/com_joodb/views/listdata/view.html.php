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

class JoodbViewListdata extends JViewLegacy
{
	function display($tpl = null)
	{

		$app = JFactory::getApplication();
		$joodbid	= JRequest::getCmd( 'joodbid');

		$jb = JTable::getInstance( 'joodb', 'Table' );
		$jb->load( $joodbid );

		JToolBarHelper::title(   $jb->name.': <small><small>['.JText::_( 'Edit Data' ).']</small></small>','joodb.png' );
		JToolBarHelper::addNew('editdata');
		JToolBarHelper::editList('editdata');
		JToolBarHelper::deleteList('Realy Delete','removedata');
		JToolBarHelper::cancel('cancel' ,'close');

		// Initialize variables
		$db	= $jb->getTableDBO();
		$filter		= null;

		// Get some variables from the request
		$context			= 'com_joodb.joodblistdata'.$joodbid;
		$filter_order		= $app->getUserStateFromRequest( $context.'filter_order','filter_order',$jb->fid,'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $context.'filter_order_Dir','filter_order_Dir','DESC',	'word' );
//		$filter_state		= $app->getUserStateFromRequest( $context.'filter_state','filter_state','',	'word' );
		$search				= $app->getUserStateFromRequest( $context.'search','search','',	'string' );
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
				$where= "WHERE `".$jb->fid."`='".(int)$search."'";
			} else {
				$where= "WHERE `".$jb->ftitle."` LIKE ".$db->Quote( '%'.$db->escape( $search, true ).'%', false )
					." OR `".$jb->fid."`=".$db->quote($search);
			}
		}
		// Get the total number of records
		$query = 'SELECT COUNT(*) FROM `'.$jb->table.'` AS c '.$where;
		$db->setQuery($query);
		$total = $db->loadResult();

		// Create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);

		// Get the titles
		$query = 'SELECT * FROM `'.$jb->table.'` AS c '.$where .$order;
		$db->setQuery($query, $pagination->limitstart, $pagination->limit);
		$rows = $db->loadObjectList();

		// If there is a database query error, throw a HTTP 500 and exit
		if ($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr() );
			return false;
		}

		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;
		$lists['search'] = $search;
		$lists['fid'] = $jb->fid;
		$lists['ftitle'] = $jb->ftitle;
		$lists['fstate'] = $jb->fstate;
		$lists['fcontent'] = $jb->fcontent;
		$lists['fdate'] = $jb->fdate;
		$lists['joodbid'] = $joodbid;

		$this->assignRef('lists',$lists);
		$this->assignRef('items',$rows);
		$this->assignRef('page',$pagination);
		parent::display($tpl);

	}
}
