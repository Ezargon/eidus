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

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

JLoader::import( 'joomla.application.component.view' );

/**
 * Issuetracker View
 *
 * @package       Joomla.Components
 * @subpackage    Issuetracker
 */
class IssuetrackerViewIttypeslist extends JViewLegacy
{
   protected $items;
   protected $pagination;
   protected $state;

   /**
    * Display the view
    * @param null $tpl
    * @return mixed
    */
   public function display($tpl = null)
   {
      $this->state      = $this->get('State');
      $this->items      = $this->get('Items');
      $this->pagination = $this->get('Pagination');

      // Check for errors.
      if (count($errors = $this->get('Errors'))) {
         JError::raiseError(500, implode("\n", $errors));
         return false;
      }

      JHtml::stylesheet(JPATH_COMPONENT_ADMINISTRATOR.DS.'css', array(), true, false, false);

      $this->addToolbar();

      $jversion = new JVersion();
      if( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
         $this->sidebar = JHtmlSidebar::render();
      } else {
         $this->setLayout("default25");
      }

      return parent::display($tpl);
   }

   /**
    * Add the page title and toolbar.
    *
    * @since   1.6
    */
   protected function addToolbar()
   {
      require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'issuetracker.php';

      $state   = $this->get('State');
      $canDo   = IssueTrackerHelper::getActions($state->get('filter.category_id'));

      JHtml::stylesheet('com_issuetracker/administrator.css', array(), true, false, false);

      JToolBarHelper::title(JText::_('COM_ISSUETRACKER_MANAGER_TYPES'), 'generic.png');

        //Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'ittypes';
        if (file_exists($formPath)) {

            if ($canDo->get('core.create')) {
             JToolBarHelper::addNew('ittypes.add','JTOOLBAR_NEW');
          }

          if ($canDo->get('core.edit')) {
             JToolBarHelper::editList('ittypes.edit','JTOOLBAR_EDIT');
          }

        }

      if ($canDo->get('core.edit.state')) {

            if (isset($this->items[0]->state)) {
             JToolBarHelper::divider();
             JToolBarHelper::custom('ittypeslist.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
             JToolBarHelper::custom('ittypeslist.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
            } else {
                //If this component does not use state then show a direct delete button as we can not trash
                JToolBarHelper::deleteList('', 'ittypeslist.delete','JTOOLBAR_DELETE');
            }

            if (isset($this->items[0]->state)) {
             JToolBarHelper::divider();
             JToolBarHelper::archiveList('ittypeslist.archive','JTOOLBAR_ARCHIVE');
            }
            if (isset($this->items[0]->checked_out)) {
               JToolBarHelper::custom('ittypeslist.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
            }
      }

        //Show trash and delete for components that uses the state field
        if (isset($this->items[0]->state)) {
          if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
             JToolBarHelper::deleteList('', 'ittypeslist.delete','JTOOLBAR_EMPTY_TRASH');
             JToolBarHelper::divider();
          } else if ($canDo->get('core.edit.state')) {
             JToolBarHelper::trash('ittypeslist.trash','JTOOLBAR_TRASH');
             JToolBarHelper::divider();
          }
        }

      if ($canDo->get('core.admin')) {
         JToolBarHelper::preferences('com_issuetracker', '600','800');
      }

      JToolBarHelper::divider();
      JToolBarHelper::help( 'screen.issuetracker', true );

      $jversion = new JVersion();
      if( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
         JHtmlSidebar::setAction('index.php?option=com_issuetracker&view=ittypeslist');

         JHtmlSidebar::addFilter(
            JText::_('JOPTION_SELECT_PUBLISHED'),
            'filter_state',
            JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.state'), true)
         );
      }

   }

   /**
    * Returns an array of fields the table can be sorted by
    *
    * @return  array  Array containing the field name to sort by as the key and display text as value
    *
    * @since   3.0
    */
   protected function getSortFields()
   {
      return array(
        'a.type_name'   => JText::_('COM_ISSUETRACKER_TYPE_NAME'),
        'a.description' => JText::_('COM_ISSUETRACKER_DESCRIPTION'),
        'a.state'       => JText::_('JPUBLISHED'),
        'a.ordering'    => JText::_('JGRID_HEADING_ORDERING'),
//        'a.language'  => JText::_('JGRID_HEADING_LANGUAGE'),
        'a.id'          => JText::_('JGRID_HEADING_ID')
       );

   }
}