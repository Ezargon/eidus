<?php
/*
 *
 * @Version       $Id: timecreated.php 1929 2015-02-08 16:25:12Z geoffc $
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
class JFormFieldTimecreated extends JFormField
{
   /**
    * The form field type.
    *
    * @var     string
    * @since   1.6
    */
   protected $type = 'timecreated';

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

      $date  = JFactory::getDate();

      $time_created = $this->value;
      if (!$time_created) {
         $time_created = $date->toSQL();
         // $time_created = date("Y-m-d H:i:s");
         $html[] = '<input type="hidden" name="'.$this->name.'" value="'.$time_created.'" />';
      }
      $jdate = new JDate($time_created);
      $pretty_date = $jdate->format(JText::_('DATE_FORMAT_LC2'));
      $html[] = "<div>".$pretty_date."</div>";

      return implode($html);
   }
}