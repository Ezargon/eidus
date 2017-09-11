<?php
/**
 * Created by     Eric Fernance
 * User:          Eric Fernance
 * Website:       http://hotchillisoftware.com
 * License:       GPL v2
 */


defined('_JEXEC') or die;


JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

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
						<th width="1%" class="nowrap center"><?php echo JText::_("JPUBLISHED");?></th>
						<th><?php echo JText::_("COM_PBBOOKING_Email_NAME");?></th>
						<th><?php echo JText::_("COM_PBBOOKING_Email_IDS");?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="5">
						</td>
					</tr>
				</tfoot>
				<tbody>
					<?php foreach ($this->items as $i => $item) : ?>
						<tr class="row<?php echo $i % 2; ?>">
							<td class="center"><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
							<td class="center"><?php echo $item->published;?></td>
							<td><?php echo $item->name;?></td>
							<td><?php echo $item->services;?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

		<?php echo $this->pagination->getListFooter(); ?>

	</div>

	<?php endif;?>
</form>
