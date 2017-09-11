<?php
/**
 * Created by     Eric Fernance
 * User:          Eric Fernance
 * Website:       http://hotchillisoftware.com
 * License:       GPL v2
 */


defined('_JEXEC') or die;

JHtml::_("jquery.framework");
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
JHtml::stylesheet("com_pbbooking/pbbooking-admin.css",array(),true);

$app       = JFactory::getApplication();
$user      = JFactory::getUser();
$userId    = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));


?>

<form action="<?php echo JRoute::_('index.php?option=com_pbbooking'); ?>" method="post" name="adminForm" id="adminForm">

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token'); ?>
	<div id="j-main-container">

		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items">
				<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
			<table class="table table-striped" id="articleList">
				<thead>
					<tr>
						<th width="1%" class="nowrap center">
							<?php echo JHtml::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
						</th>
						<th><?php echo JText::_("COM_PBBOOKING_SMS_SMS_TAG");?></th>
						<th><?php echo JText::_("COM_PBBOOKING_SMS_SMS_TAG_DESC");?></th>
						<th><?php echo JText::_("COM_PBBOOKING_SMS_SMS");?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="6">
						</td>
					</tr>
				</tfoot>
				<tbody>
					<?php foreach ($this->items as $i => $item) : ?>
						<tr class="row<?php echo $i % 2; ?>">
							<td class="center"><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
							<td><?php echo htmlentities($item->sms_tag);?></td>
							<td><?php echo JText::_("COM_PBBOOKING_SMS_SMS_TAG_".strtoupper($item->sms_tag)."_DESC");?></td>
							<td><?php echo htmlentities($item->sms);?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

		<?php echo $this->pagination->getListFooter(); ?>

	</div>

	<?php endif;?>
</form>
