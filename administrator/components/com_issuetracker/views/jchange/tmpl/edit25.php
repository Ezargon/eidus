<?php
/*
 *
 * @Version       $Id: edit25.php 1929 2015-02-08 16:25:12Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.2
 * @Copyright     Copyright (C) 2011-2015 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2015-02-08 16:25:12 +0000 (Sun, 08 Feb 2015) $
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted Access');

// Load date helper
if (! class_exists('IssueTrackerHelperDate')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'dates.php');
}

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

// Import CSS
// $document = JFactory::getDocument();
// $document->addStyleSheet('components/com_issuetracker/assets/css/issuetracker.css');

// Get the dates in local timezone. Need to add the input tags manually else JHtml complians with the message
// Notice: Object of class JDate could not be converted to int in /share/MD0_DATA/Web/DEV/libraries/joomla/html/html.php on line 901.
if ( $this->item->change_date == '0000-00-00 00:00:00' || empty($this->item->change_date) || is_null($this->item->change_date) ) {
   $d0 = "";
} else {
   $d0 = IssueTrackerHelperDate::getDate($this->item->change_date);
}


?>
<script type="text/javascript">
    js = jQuery.noConflict();
    js(document).ready(function(){

    });

    Joomla.submitbutton = function(task)
    {
        if(task == 'jchange.cancel'){
            Joomla.submitform(task, document.getElementById('jchange-form'));
        }
        else {
            if (task != 'jchange.cancel' && document.formvalidator.isValid(document.id('jchange-form'))) {
                Joomla.submitform(task, document.getElementById('jchange-form'));
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="jchange-form" class="form-validate">
   <div class="width-100 fltlft">
      <fieldset class="adminform">
         <legend><?php echo JText::_('JDETAILS'); ?></legend>
         <ul class="adminformlist">

            <li><?php echo $this->form->getLabel('table_name'); ?>
            <?php echo $this->form->getInput('table_name'); ?></li>

            <li><?php echo $this->form->getLabel('component'); ?>
            <?php echo $this->form->getInput('component'); ?></li>

            <li><?php echo $this->form->getLabel('column_name'); ?>
            <?php echo $this->form->getInput('column_name'); ?></li>

            <li><?php echo $this->form->getLabel('column_type'); ?>
            <?php echo $this->form->getInput('column_type'); ?></li>

            <li><?php echo $this->form->getLabel('row_key'); ?>
            <?php echo $this->form->getInput('row_key'); ?></li>

            <li><?php echo $this->form->getLabel('row_key_link'); ?>
            <?php echo $this->form->getInput('row_key_link'); ?></li>

            <li><?php echo $this->form->getLabel('action'); ?>
            <?php echo $this->form->getInput('action'); ?></li>

            <li><?php echo $this->form->getLabel('id'); ?>
            <?php echo $this->form->getInput('id'); ?></li>

            <li><?php echo $this->form->getLabel('old_value'); ?>
            <?php echo $this->form->getInput('old_value'); ?></li>

            <li><?php echo $this->form->getLabel('new_value'); ?>
            <?php echo $this->form->getInput('new_value'); ?></li>

         </ul>
            <div class="clr"></div>
         <ul>
            <li><?php echo $this->form->getLabel('change_by'); ?>
            <?php echo $this->form->getInput('change_by'); ?></li>

            <li><?php echo $this->form->getLabel('change_date'); ?>
            <input type="text" name="jform[change_date]" id="jform_change_date" value="<?php echo $d0; ?>" size="40" disabled="disabled" readonly="readonly"/>
            <!-- ?php echo $this->form->getInput('change_date'); ? -->
            </li>
         </ul>
      </fieldset>
      <input type="hidden" name="task" value="" />
      <?php echo JHtml::_('form.token'); ?>
   </div>
</form>