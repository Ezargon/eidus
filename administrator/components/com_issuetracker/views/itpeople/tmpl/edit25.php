<?php
/*
 *
 * @Version       $Id: edit25.php 1929 2015-02-08 16:25:12Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.5
 * @Copyright     Copyright (C) 2011-2015 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2015-02-08 16:25:12 +0000 (Sun, 08 Feb 2015) $
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access' );

require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'issuetracker.php';

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<script type="text/javascript">
   Joomla.submitbutton = function(task)
   {
      if (task == 'itpeople.cancel' || document.formvalidator.isValid(document.id('type-form'))) {
         Joomla.submitform(task, document.getElementById('type-form'));
      }
      else {
         alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
      }
   }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="type-form" class="form-validate">
   <div class="width-60 fltlft">
      <fieldset class="adminform">
         <legend><?php echo JText::_('JDETAILS'); ?></legend>
         <ul class="adminformlist">

         <li><?php echo $this->form->getLabel('user_id'); ?>
         <?php echo $this->form->getInput('user_id'); ?></li>

         <li><?php echo $this->form->getLabel('person_name'); ?>
         <?php echo $this->form->getInput('person_name'); ?></li>

         <li><?php echo $this->form->getLabel('username'); ?>
         <?php echo $this->form->getInput('username'); ?></li>

         <li><?php echo $this->form->getLabel('person_email'); ?>
         <?php echo $this->form->getInput('person_email'); ?></li>

         <li><?php echo $this->form->getLabel('phone_number'); ?>
         <?php echo $this->form->getInput('phone_number'); ?></li>

            <li><?php echo $this->form->getLabel('person_role'); ?>
         <?php echo $this->form->getInput('person_role'); ?></li>

         <li><?php echo $this->form->getLabel('assigned_project'); ?>
         <?php echo $this->form->getInput('assigned_project'); ?></li>

         <li><?php echo $this->form->getLabel('issues_admin'); ?>
         <?php echo $this->form->getInput('issues_admin'); ?></li>

         <li><?php echo $this->form->getLabel('staff'); ?>
         <?php echo $this->form->getInput('staff'); ?></li>

         <li><?php echo $this->form->getLabel('email_notifications'); ?>
         <?php echo $this->form->getInput('email_notifications'); ?></li>

         <?php if ( IssuetrackerHelper::comp_installed('com_acysms')) { ?>
            <li><?php echo $this->form->getLabel('sms_notify'); ?>
            <?php echo $this->form->getInput('sms_notify'); ?></li>
         <?php }?>

         <li><?php echo $this->form->getLabel('registered'); ?>
         <?php echo $this->form->getInput('registered'); ?></li>

         <li><?php echo $this->form->getLabel('published'); ?>
         <?php echo $this->form->getInput('published'); ?></li>

         <li><?php echo $this->form->getLabel('checked_out'); ?>
         <?php echo $this->form->getInput('checked_out'); ?></li>

         <li><?php echo $this->form->getLabel('checked_out_time'); ?>
         <?php echo $this->form->getInput('checked_out_time'); ?></li>

         </ul>
      </fieldset>
   </div>

   <div class="width-40 fltlft">
      <?php echo $this->loadTemplate('audit_details');?>
   </div>

   <input type="hidden" name="task" value="" />
   <?php echo JHtml::_('form.token'); ?>
   <div class="clr"></div>
</form>
