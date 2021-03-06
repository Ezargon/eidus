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
defined('_JEXEC') or die('Restricted access' );

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

// Create shortcut to parameters.
$parameters = $this->state->get('params');
$allow_private    = $parameters->get('allow_private_issues');

// Get the calling page address.
$return_page = $_SERVER['HTTP_REFERER'];
$encoded_page = base64_encode($return_page);

// Import CSS
// $document = JFactory::getDocument();
// $document->addStyleSheet('components/com_issuetracker/assets/css/issuetracker.css');
?>
<script type="text/javascript">
   Joomla.submitbutton = function(task)
   {
      if (task == 'paction.cancel' || document.formvalidator.isValid(document.id('paction-form'))) {
         Joomla.submitform(task, document.getElementById('paction-form'));
      } else {
         alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
      }
   }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="paction-form" class="form-validate" enctype="multipart/form-data">
   <div class="width-100 fltlft">
      <fieldset class="adminform">
         <legend><?php echo JText::_('COM_ISSUETRACKER_LEGEND_PROGRESS'); ?></legend>
         <ul class="adminformlist">

         <li><?php echo $this->form->getLabel('id'); ?>
         <?php echo $this->form->getInput('id'); ?></li>

         <li><?php echo $this->form->getLabel('issue_id'); ?>
         <?php echo $this->form->getInput('issue_id'); ?></li>

         <li><?php echo $this->form->getLabel('alias'); ?>
         <?php echo $this->form->getInput('alias'); ?></li>

         <li><?php echo $this->form->getLabel('lineno'); ?>
         <?php echo $this->form->getInput('lineno'); ?></li>

         <li><?php echo $this->form->getLabel('access'); ?>
         <?php echo $this->form->getInput('access'); ?></li>

         <li><?php echo $this->form->getLabel('public'); ?>
         <?php echo $this->form->getInput('public'); ?></li>

         <li><?php echo $this->form->getLabel('progress'); ?>
         <div class="clr"></div>
         <?php echo $this->form->getInput('progress'); ?></li>

         <li><?php echo $this->form->getLabel('state'); ?>
         <?php echo $this->form->getInput('state'); ?></li>

         <li><?php echo $this->form->getLabel('checked_out'); ?>
         <?php echo $this->form->getInput('checked_out'); ?></li>

         <li><?php echo $this->form->getLabel('checked_out_time'); ?>
         <?php echo $this->form->getInput('checked_out_time'); ?></li>

         </ul>
      </fieldset>

      <?php echo $this->loadTemplate('audit_details');?>
   </div>


   <input type="hidden" name="return" value="<?php echo $encoded_page;?>" />
   <input type="hidden" name="task" value="" />
   <?php echo JHtml::_('form.token'); ?>
   <div class="clr"></div>

    <style type="text/css">
        /* Temporary fix for drifting editor fields */
        .adminformlist li {
            clear: both;
        }
    </style>
</form>