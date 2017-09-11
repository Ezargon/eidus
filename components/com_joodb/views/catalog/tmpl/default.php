<?php // no direct acces
defined('_JEXEC') or die('Restricted access');
$app = JFactory::getApplication();
?>
<div class="joodb database-list<?php echo $this->params->get('pageclass_sfx')?>">
	<?php if ($this->params->get('show_page_heading') || $this->params->get( 'show_description')) : ?>
		<div class="page-header">
			<?php if ($this->params->get('show_page_heading')) : ?>
				<h1>
					<?php echo $this->escape($this->params->get('page_heading')); ?>
				</h1>
			<?php endif; ?>
			<?php if ( $this->params->get( 'show_description') && $this->pagination->limitstart==0 ) : ?>
				<div class="introtext">
					<?php if ($this->params->get('image')!="-1") : ?>
						<img src="<?php echo $this->baseurl."/".$this->params->get('image');?>" style="float: <?php echo $this->params->get('image_align');?>; margin: 0 15px;" alt="*" />
					<?php endif; ?>
					<?php echo nl2br($this->params->get('description')); ?>
				</div>
				<div style="clear: both"></div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	<form name="searchForm" id="searchForm"  method="get" class="form-inline" action="<?php echo JURI::current(); ?>"  >
		<input type="hidden" name="option" value="com_joodb"/>
		<input type="hidden" name="view" value="catalog"/>
		<input type="hidden" name="format" value="html"/>
		<input type="hidden" name="reset" value="false"/>
		<input type="hidden" name="ordering" value="<?php echo $app->input->get("ordering"); ?>"/>
		<input type="hidden" name="orderby" value="<?php echo $app->input->getString("orderby"); ?>"/>
		<input type="hidden" name="Itemid" value="<?php echo $app->input->getInt("Itemid"); ?>"/>
		<input type="hidden" name="task" value=""/>
		<?php

		// replace nodata wildcard if data is empty
		if (empty($this->items) && $this->state->get('show_data')!=false) {
			$this->joobase->tpl_list = str_replace("{joodb nodata}" , '<div class="error nodata">'.JText::_('No data found')."</div>" ,$this->joobase->tpl_list);
		}

		$pageparts = preg_split("!{joodb loop}!", $this->joobase->tpl_list);
		if (count($pageparts)<3) {
			JError::raiseError(500, "Error in catalog template. Remember 2 loop declarations must be found inside catalog template!");
		}

		// get header text
		$parts = JoodbHelper::splitTemplate($pageparts[0]);
		$page = new JObject();
		$page->text = $this->_parseTemplate($parts);

		// do the loop
		if ($this->items) {
			// get the parts
			$parts = JoodbHelper::splitTemplate($pageparts[1]);
			foreach ( $this->items as $n=>$item ) {
				$this->params->set("counter",$n);
				$page->text .= JoodbHelper::parseTemplate($this->joobase,$parts,$item,$this->params);
			}
		}

		// get footer text
		$parts = JoodbHelper::splitTemplate($pageparts[2]);
		$page->text .= $this->_parseTemplate($parts);
		// render output text
		JoodbHelper::printOutput($page,$this->params,"catalog");

		?>
	</form>
	<div style="clear: both;"></div>
</div>
<script type="text/javascript" >

	// Submit search form
	function submitSearch(task) {
		var form = document.searchForm;
		form.format.value="html";
		if (task=="reset") {
			form.ordering.value="";
			form.orderby.value="";
			jQuery('#searchForm select.groupselect').val('');
			jQuery('#searchForm input.check').attr('checked', false);
			jQuery('#searchForm input[type=text]').val('');
			form.reset.value = true
		} else if (task=="xportxls") {
			form.format.value="xls";
		} else if (task=="uncheck") {
			jQuery('#searchForm input.check').attr('checked', false);
		} else if (task=="setlimit") {
		}
		if (form.search && form.search.value=="<?php echo JText::_('search...'); ?>") {
			form.search.value="";
		}
		form.submit();
	}

	// Check if touch device
	function isTouchDevice() {
		try {
			document.createEvent("TouchEvent");
			return true;
		} catch (e) {
			return false;
		}
	}

	// Jquery encapsulation
	(function ($) {

		$(document).ready(function () {
//            jQuery('#searchForm select.groupselect').change(function(){ submitSearch(""); });
		});

		$(window).load(function () {
			if (isTouchDevice()) return false;
			if ($('#limit')) {
				$('#limit').change(function(){ submitSearch('setlimit'); });
			}
		});

	})(jQuery);

</script>
