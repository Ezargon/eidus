<?php
/*
 *
 * @Version       $Id: edit_attachment.php 1929 2015-02-08 16:25:12Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.4.0
 * @Copyright     Copyright (C) 2011-2015 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2015-02-08 16:25:12 +0000 (Sun, 08 Feb 2015) $
 *
 */
defined('_JEXEC') or die('Restricted Access');

// Create shortcut to parameters.
$parameters = $this->state->get('params');

$uploadLimit = $parameters->get('max_file_size', 1);

?>

<fieldset>
   <legend>
      <?php echo JText::_('COM_ISSUETRACKER_ATTACHMENTS_LEGEND'); ?>
   </legend>
   <label for="attachedfile" class="control-label"><?php echo JText::_('COM_ISSUETRACKER_LBL_ATTACHMENT'); ?></label>
   <div class="controls">
      <input type="file" id="attachedfile" name="attachedfile" size="40" />
      <span class="help-block">
         <?php echo JText::sprintf('COM_ISSUETRACKER_LBL_ATTACHMENT_MAXSIZE', $uploadLimit); ?>
      </span>
   </div>
</fieldset>

