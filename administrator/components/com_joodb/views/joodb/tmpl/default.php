<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-inline">
	<div id="editcell">
		<fieldset id="filter-bar">
			<div class="filter-search btn-group pull-left fltlft">
				<label class="inline"><?php echo JText::_( 'FILTER' ); ?>:&nbsp;</label>
				<input type="text" class="inputbox inline" name="search" id="search" style="width:200px;" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" placeholder="<?php echo JText::_( 'FILTER_BY_TITLE_OR_ENTER_ARTICLE_ID' );?>" title="<?php echo JText::_( 'FILTER_BY_TITLE_OR_ENTER_ARTICLE_ID' );?>"/>
			</div>
			<div class="btn-group pull-left fltlft hidden-phone">
				<button class="btn" onclick="this.form.submit();"><?php echo JText::_( 'FILTER' ); ?></button>
				<button class="btn" onclick="document.getElementById('search').value='';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_( 'RESET' ); ?></button>
			</div>
		</fieldset>
		<table class="adminlist table table-striped" style="position: relative;">
			<thead>
			<tr>
				<th style="width: 3%" class="center"><?php echo JHTML::_('grid.sort',   'ID', 'c.id', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
				<th style="width: 3%" class="center"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" /></th>
				<th style="width: 30%"><?php echo JHTML::_('grid.sort',   'Name', 'c.name', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
				<th style="width: 20%"><?php echo JText::_('Data'); ?></th>
				<th style="width: 20%"><?php echo JHTML::_('grid.sort',   'TABLE IN DATABASE', 'c.table', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
				<th style="width: 3%" nowrap="nowrap"><?php  echo JText::_('Menu item'); ?></th>
				<th style="width: 3%" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort',   'Published', 'c.published', $this->lists['order_Dir'], $this->lists['order'] ); ?>
				</th>
				<th style="width: 10%" ><?php echo JHTML::_('grid.sort',   'Created', 'c.published', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<td colspan="8">
					<?php echo $this->page->getListFooter(); ?>
				</td>
			</tfoot>
			<tbody>	<?php
			$dbimage = "components/com_joodb/assets/images/database.png";
			$menuimage = "components/com_joodb/assets/images/add-menu.png";
			$editimage = "components/com_joodb/assets/images/edit.png";
			$k = 0;
			for ($i=0, $n=count( $this->items ); $i < $n; $i++)
			{
				$row = &$this->items[$i];

				$published 	= JHTML::_('grid.published', $row,  $i);
				$checked 	= JHTML::_('grid.id',   $i, $row->id );
				$termLink	= JRoute::_("index.php?option=com_joodb&task=edit&view=joodbentry&cid[]=$row->id");
				$editLink	= JRoute::_("index.php?option=com_joodb&task=listdata&view=listdata&joodbid=$row->id");
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td class="center"><?php echo $row->id; ?></td>
					<td class="center"><?php echo $checked; ?></td>
					<td>
						<a href='<?php echo $termLink; ?>' class="hasTip" title="<?php echo addslashes(JText::_("EDIT")."::".$row->name);?>" >
							<img src="<?php echo $editimage ?>" alt="*" border="0" />
							<?php  echo $row->name; ?>
						</a>
					</td>
					<td>
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'Edit data of' )." <b>".$row->table."</b>";?>">
					<a href="<?php echo $editLink; ?>">
						<img src="<?php echo $dbimage ?>" " alt="*" border="0" />
						<?php echo JText::_( 'Edit data' ); ?>
					</a>
				</span>
					</td>
					<td><?php echo $row->table; ?></td>
					<td class="center">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'Create menu item' )." <b>".$row->name."</b>";?>">
					<a class="modal" href="index.php?option=com_joodb&tmpl=component&view=addmenuitem&cid[]=<?php echo $row->id ?>" rel="{handler: 'iframe', size: {x: 480, y: 120}}">
						<img src="<?php echo $menuimage ?>" alt="*" border="0" />
					</a>
				</span>
					</td>
					<td class="center">
						<?php echo $published;?>
					</td>
					<td><?php echo JHTML::_('date', $row->created, JText::_('DATE_FORMAT_LC3')); ?></td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
		</table>
	</div>
	<input type="hidden" name="option" value="com_joodb" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="joodb" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHTML::_( 'form.token' );?>
</form>
