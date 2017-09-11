<?php
/**
 * Created by     Eric Fernance
 * User:          Eric Fernance
 * Website:       http://hotchillisoftware.com
 * License:       GPL v2
 */

defined("_JEXEC") or die("Invalid Direct Access");


JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_("jquery.framework");

?>

<form action="<?php echo JRoute::_('index.php?option=com_pbbooking');?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	<div class="span12">
		<div class="well well-small">
			<h3><?php echo JText::_("COM_PBBOOKING_EMAIL_HEADING_GENERAL");?></h3>
			<?php if ($this->item->subscriber == 1):?>
				<div class="alert alert-info">
					<?php echo JText::_("COM_PBBOOKING_GENERAL_TEST_SUBSCRIBER_ONLY");?>
				</div>
			<?php endif;?>
			<div class="form-horizontal">
				<?php foreach($this->form->getFieldset("") as $field) :?>
					<?php echo $field->getControlGroup();?>
				<?php endforeach;?>
			</div>
		</div>
	</div>
	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="option" value="com_pbbooking"/>
	<input type="hidden" name="task" value="email.edit"/>
	<input type="hidden" name="id" value="<?php echo (isset($this->item->id)) ? $this->item->id : null;?>"/>
</form>
