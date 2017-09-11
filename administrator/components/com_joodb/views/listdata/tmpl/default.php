<?php

// no direct access
defined('_JEXEC') or die('Restricted access');
$version = new JVersion();

?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
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
			<th width="1%" class="center"><?php echo JHTML::_('grid.sort',   'ID', 'c.'.$this->lists['fid'], $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="1%" class="center"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" /></th>
			<th class="title"><?php echo JHTML::_('grid.sort',   'Title', 'c.'.$this->lists['ftitle'], $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th ><?php echo JText::_('Main Content'); ?></th>
			<th width="5%" nowrap="nowrap"><?php
				if ($this->lists['fstate']) {
				 	echo JHTML::_('grid.sort',   'Published', 'c.'.$this->lists['fstate'], $this->lists['order_Dir'], $this->lists['order'] );
				} else echo JText::_( 'Published' );
			 ?>
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="5">
				<?php echo $this->page->getListFooter(); ?>
			</td>
	</tfoot>
	<tbody>	<?php
	$k = 0;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)
	{
		$row = &$this->items[$i];
		$checked 	= JHTML::_('grid.id', $i, $row->{$this->lists['fid']} );
		$editLink	= JRoute::_("index.php?option=com_joodb&task=editdata&view=editdata&joodbid=".$this->lists['joodbid']."&cid[]=".$row->{$this->lists['fid']});
		if ($this->lists['fstate']) {
			$row->published = $row->{$this->lists['fstate']};
			$published 	= JHTML::_('grid.published', $row,  $i,'tick.png','publish_x.png','data_');
		} else {
			$published	= JHtml::_('image','admin/disabled.png', JText::_('Not availiable'), NULL, true);
		}
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td class="center"><?php echo $row->{$this->lists['fid']}; ?></td>
			<td class="center"><?php echo $checked; ?></td>
			<td><a href='<?php echo $editLink; ?>' class="hasTip" title="<?php echo addslashes(JText::_("EDIT")."::".$row->{$this->lists['ftitle']}); ?>" ><?php  echo $row->{$this->lists['ftitle']}; ?></a></td>
			<td><?php echo substr(strip_tags($row->{$this->lists['fcontent']}),0,120); ?> &hellip;</td>
			<td class="center">
				<?php echo $published;?>
			</td>
		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
	</tbody>
</table>

<input type="hidden" name="option" value="com_joodb" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="joodbid" value="<?php echo $this->lists['joodbid']; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="listdata" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
<?php echo JHTML::_( 'form.token' );?>
</div>
</form>

