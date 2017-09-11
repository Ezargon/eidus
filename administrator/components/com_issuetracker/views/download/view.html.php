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

/**
 * View class for download a list of issues.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_issuetracker
 * @since       1.6
 */
class IssuetrackerViewDownload extends JViewLegacy
{
   protected $form;

   /**
    * Display the view
    *
    * @param   string $tpl The name of the template file to parse; automatically searches through the template paths.
    *
    * @return bool|mixed
    */
   public function display($tpl = null)
   {
      $this->form = $this->get('Form');

      // Check for errors.
      if (count($errors = $this->get('Errors')))
      {
         JError::raiseError(500, implode("\n", $errors));

         return false;
      }

      return parent::display($tpl);

   }
}