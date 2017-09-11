<?php /**
 * @title		Shape 5 Open Table
 * @version		1.0
 * @package		Joomla
 * @website		http://www.shape5.com
 * @copyright	Copyright (C) 2015 Shape 5 LLC. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
$document = JFactory::getDocument();
$document->addCustomTag('<link rel="stylesheet" href="'.$url.'css/style.css" type="text/css" />');

?>

<?php if ($s5_ot_pretext != "") { ?>		
	<div id="s5_ot_pretext">			
		<?php echo $s5_ot_pretext ?>		
	</div>	
<?php } ?>

<div id="OT_searchWrapperAll">
	<script type="text/javascript" src="https://secure.opentable.com/frontdoor/default.aspx?rid=<?php echo $s5_ot_idcode ?>&restref=<?php echo $s5_ot_idcode ?>&mode=wide&hover=1"></script>
</div>
<div style="clear:both;"></div>




	