<?php
/*
 *
 * @Version       $Id: default.php 1929 2015-02-08 16:25:12Z geoffc $
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
?>
<form
   action="<?php echo JRoute::_('index.php?option=com_issuetracker&task=itissueslist.display&format=raw'); ?>"
   method="post"
   name="adminForm"
   id="download-form"
   class="form-validate">
   <fieldset class="adminform">
      <legend><?php echo JText::_('COM_ISSUETRACKER_ISSUES_DOWNLOAD'); ?></legend>

      <?php foreach ($this->form->getFieldset() as $field) : ?>
         <?php if (!$field->hidden) : ?>
            <?php echo $field->label; ?>
         <?php endif; ?>
         <?php echo $field->input; ?>
      <?php endforeach; ?>
      <div class="clr"></div>
      <button type="button" class="btn" onclick="this.form.submit();window.top.setTimeout('window.parent.SqueezeBox.close()', 700);"><?php echo JText::_('COM_ISSUETRACKER_ISSUES_EXPORT'); ?></button>
      <!-- button type="button" class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('JCANCEL'); ?></button -->
      <!-- button type="button" class="btn" onclick="window.parent.SqueezeBox.close();"><?php echo JText::_('JCANCEL'); ?></button -->

   </fieldset>
</form>
