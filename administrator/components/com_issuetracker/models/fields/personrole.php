<?php
/*
 *
 * @Version       $Id: personrole.php 2050 2015-08-09 09:48:51Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.8
 * @Copyright     Copyright (C) 2011-2015 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2015-08-09 10:48:51 +0100 (Sun, 09 Aug 2015) $
 *
 */
defined('_JEXEC') or die('Restricted access');

if(!defined('DS')){
   define('DS',DIRECTORY_SEPARATOR);
}

if (! class_exists('IssueTrackerHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php');
}

JLoader::import('joomla.form.formfield');

/**
 * Supports an HTML select list of categories
 */
class JFormFieldPersonrole extends JFormField
{
   /**
    * The form field type.
    *
    * @var     string
    * @since   1.6
    */
   protected $type = 'personrole';

   /**
    * Method to get the field input markup.
    *
    * @return  string   The field input markup.
    * @since   1.6
    */
   protected function getInput()
   {
    // Initialize variables.
      // $text = '';

      $db      = JFactory::getDbo();
      $query   = $db->getQuery(true);

      $query->select('role_name AS text');
      $query->from('#__it_roles');
      $query->where('id = '.$this->value);

      // Get the options.
      $db->setQuery($query);

      $text = $db->loadResult();

      return $text;

   }
}