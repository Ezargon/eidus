<?php
/*
 *
 * @Version       $Id: createdby.php 1929 2015-02-08 16:25:12Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.4.0
 * @Copyright     Copyright (C) 2011-2015 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2015-02-08 16:25:12 +0000 (Sun, 08 Feb 2015) $
 *
 */
defined('_JEXEC') or die('Restricted access');

JLoader::import('joomla.form.formfield');

/**
 * Supports an HTML select list of categories
 */
class JFormFieldCreatedby extends JFormField
{
   /**
    * The form field type.
    *
    * @var     string
    * @since   1.6
    */
   protected $type = 'createdby';

   /**
    * Method to get the field input markup.
    *
    * @return  string   The field input markup.
    * @since   1.6
    */
   protected function getInput()
   {
      // Initialize variables.
      $html = array();


      //Load user
      $user_id = $this->value;
      if ($user_id) {
         $user = JFactory::getUser($user_id);
      } else {
         $user = JFactory::getUser();
         $html[] = '<input type="hidden" name="'.$this->name.'" value="'.$user->id.'" />';
      }
      $html[] = "<div>".$user->name." (".$user->username.")</div>";

      return implode($html);
   }
}