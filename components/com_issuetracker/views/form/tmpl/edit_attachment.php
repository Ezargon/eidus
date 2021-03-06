<?php
/*
 *
 * @Version       $Id: edit_attachment.php 1929 2015-02-08 16:25:12Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.2
 * @Copyright     Copyright (C) 2011-2015 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2015-02-08 16:25:12 +0000 (Sun, 08 Feb 2015) $
 *
 */
defined('_JEXEC') or die('Restricted Access');

// Load date helper
if (! class_exists('IssueTrackerHelperDate')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'dates.php');
}

// Create shortcut to parameters.
$parameters = $this->state->get('params');

$maxfiles = $parameters->get('max_files', 1);
$uploadLimit = $parameters->get('max_file_size', 1);

?>

<fieldset>
   <legend><?php echo JText::_('COM_ISSUETRACKER_ATTACHMENTS_LEGEND'); ?></legend>

   <label for="attachedfile" class="control-label"><?php echo JText::_('COM_ISSUETRACKER_LBL_ATTACHMENT'); ?></label>
   <div class="controls">
      <?php for ($i = 1; $i <= $maxfiles; $i++) { ?>
         <input type="file" name="attachedfile[]" size="40" />
         <?php echo $this->form->getLabel('Filedesc'); ?>
         <input type="text" name="Filedesc[]" size="40" maxlength="250" />
         <div class="clr"></div>
      <?php } ?>

      <div class="clr"></div>
      <span class="help-block">
         <?php echo JText::sprintf('COM_ISSUETRACKER_LBL_ATTACHMENT_MAXSIZE', $uploadLimit); ?>
      </span>
   </div>
</fieldset>