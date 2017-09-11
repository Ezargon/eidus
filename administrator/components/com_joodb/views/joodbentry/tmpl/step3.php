<?php

// no direct access
defined('_JEXEC') or die('Restricted access');
$app = JFactory::getApplication();

?>
<div id="header-box" class="single">
    <div class="header">
        <h1 class="page-title"><?php echo JHtml::_('string.truncate', $app->JComponentTitle, 0, false, false); ?></h1>
    </div>
    <div class="subhead" id="toolbar-box">
        <?php echo $this->bar->render(); ?>
    </div>
</div>
<div class="content-box" id="element-box">
	<h4><?php echo JText::_( "Step3 Help" );   ?>
    </h4>
	<br/><br/><br/><br/>
	<div class="clr"></div>
</div>
<script type="text/javascript">

Joomla.submitbutton = function(task) {
	window.parent.location.reload();
}

</script>