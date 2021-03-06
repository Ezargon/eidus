<?php
/*
 *
 * @Version       $Id: edit.php 1929 2015-02-08 16:25:12Z geoffc $
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

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

?>
<script type="text/javascript">
   Joomla.submitbutton = function(task) {
      if (task == 'itroles.cancel' || document.formvalidator.isValid(document.id('type-form'))) {
         Joomla.submitform(task, document.getElementById('type-form'));
      } else {
         alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
      }
   }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="type-form" class="form-validate">
   <div class="span12 row-fluid form-horizontal">
      <?php echo JHtml::_('bootstrap.startTabSet', 'roleTab', array('active' => 'general')); ?>

         <?php echo JHtml::_('bootstrap.addTab', 'roleTab', 'general', JText::_('COM_ISSUETRACKER_ROLE_DETAILS', true)); ?>
            <div class="span9 row-fluid form-horizontal">
               <?php echo $this->form->renderField('role_name'); ?>
               <?php echo $this->form->renderField('description'); ?>
            </div>
            <div class="span3 form-vertical">
               <?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
            </div>
         <?php echo JHtml::_('bootstrap.endTab'); ?>

         <?php echo JHtml::_('bootstrap.addTab', 'roleTab', 'audit', JText::_('COM_ISSUETRACKER_AUDIT_INFORMATION', true)); ?>
            <div class="row-fluid form-horizontal">
               <?php echo $this->loadTemplate('audit_details'); ?>
            </div>
         <?php echo JHtml::_('bootstrap.endTab'); ?>
      <?php echo JHtml::_('bootstrap.endTabSet'); ?>

      <input type="hidden" name="task" value="" />
      <?php echo JHtml::_('form.token'); ?>
   </div>
</form>