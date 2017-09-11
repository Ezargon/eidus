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

// edit or add a custom entry from a table
class JoodbViewEditdata extends JViewLegacy
{
	var $id = 0;
	var $jb = null;
	var $item = null;

	function display($tpl = null)
	{
		JRequest::setVar( 'hidemainmenu', 1 );

		// load the jooDb object with table field infos
		$this->jb = JTable::getInstance( 'joodb', 'Table' );
		$this->jb->load( JRequest::getInt( 'joodbid') );
		$db	= $this->jb->getTableDBO();

		$this->jb->fields = $db->getTableColumns($this->jb->table,false);

		// get the item to edit
		if ($cid = JRequest::getVar( 'cid', array(), '', 'array' )) {
			JArrayHelper::toInteger( $cid );
			$this->id = $cid[0];
			$db->setQuery("SELECT * FROM `".$this->jb->table."` WHERE `".$this->jb->fid."`=".$db->quote($this->id),0,1);
			$this->item = $db->loadObject();
		} else {
            $this->item = new JObject();
        }
		
		$text = ( $this->item ? JText::_( 'Edit' ) : JText::_( 'New' ) );
		JToolBarHelper::title(   $this->jb->name.': <small><small>['.$text.']</small></small>','joodb.png' );
		JToolBarHelper::apply('applydata');
		JToolBarHelper::save('savedata');
		JToolBarHelper::cancel('listdata');

		// Load the form validation behavior
		JHTML::_('behavior.formvalidation');
        if ((int)JVERSION>=3) JHtml::_('formbehavior.chosen', 'select');
        JHtml::_('behavior.keepalive');
		JHtml::_('behavior.modal');
		
		parent::display($tpl);
	}

}