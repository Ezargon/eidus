<?php
/*
 *
 * @Version       $Id: view.html.php 1929 2015-02-08 16:25:12Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.4
 * @Copyright     Copyright (C) 2011-2015 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2015-02-08 16:25:12 +0000 (Sun, 08 Feb 2015) $
 *
 */

defined('_JEXEC') or die('Restricted access');

// import Joomla view library
JLoader::import('joomla.application.component.view');

/**
 * Issuetracker view
 *
 * @package       Joomla.Components
 * @subpackage    Issuetracker
 */
class IssueTrackerViewEmail extends JViewLegacy
{
   protected $state;
   protected $item;
   protected $form;

   /**
    * Display the view
    * @param null $tpl
    * @return mixed
    */
   public function display($tpl = null)
   {

      // get the Data
      $this->state   = $this->get('State');
      $this->form    = $this->get('Form');
      $this->item    = $this->get('Item');
      // $this->script = $this->get('Script');

      // Check for errors
      if (count($errors = $this->get('Errors'))) {
         JError::raiseError(500, implode('<br />', $errors));
         return false;
      }

      JHtml::stylesheet('com_issuetracker/administrator.css', array(), true, false, false);

      $this->addToolbar();
      $jversion = new JVersion();
      if( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
         $this->setLayout("edit");
      } else {
         $this->setLayout("edit25");
      }

      // Add in path to common audit templates
      $this->_addPath( 'template', JPATH_COMPONENT_ADMINISTRATOR . DS . 'views' . DS . 'common' . DS . 'tmpl' );

      return parent::display($tpl);
   }

   protected function addToolBar()
   {
      require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'issuetracker.php';

      //JRequest::setVar('hidemainmenu', true);
      JFactory::getApplication()->input->set('hidemainmenu', true);

      $user    = JFactory::getUser();
      $isNew   = ($this->item->id == 0);
      if (isset($this->item->checked_out)) {
         $checkedOut   = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
      } else {
         $checkedOut = false;
      }
      $canDo   = IssueTrackerHelper::getActions();

      $text = $isNew ? JText::_( 'NEW' ) : JText::_( 'EDIT' );
      JToolBarHelper::title(   JText::_( 'COM_ISSUETRACKER' ).': <small>[ ' . $text.' ]</small>', 'mail' );

      // If not checked out, can save the item.
      if (!$checkedOut && ($canDo->get('core.edit')||($canDo->get('core.create')))) {
         JToolBarHelper::apply('email.apply', 'JTOOLBAR_APPLY');
         JToolBarHelper::save('email.save', 'JTOOLBAR_SAVE');
      }
      if (!$checkedOut && ($canDo->get('core.create'))) {
         JToolBarHelper::custom('email.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
      }
      // If an existing item, can save to a copy.
      if (!$isNew && $canDo->get('core.create')) {
         JToolBarHelper::custom('email.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
      }
      if (empty($this->item->id)) {
         JToolBarHelper::cancel('email.cancel', 'JTOOLBAR_CANCEL');
      } else {
         JToolBarHelper::cancel('email.cancel', 'JTOOLBAR_CLOSE');
      }
   }
}