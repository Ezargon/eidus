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
    <form name="adminForm" action="index.php"  class="form-validate" method="post" >
		<input type="hidden" name="option" value="com_joodb" />
		<input type="hidden" name="view" value="joodbentry" />
		<input type="hidden" name="tmpl" value="component" />
		<input type="hidden" name="layout" value="step1" />
		<input type="hidden" name="task" value="addnew" />
		<table cellpadding="5">
		  <tr>
			<td><label for="jform_server"><?php echo JText :: _('Server'); ?></label></td>
			<td>
				<input type="text" value="" class="inputbox required" name="server" id="jform_server" style="width:250px;"/>
			</td>
			<td rowspan="4" style="padding-left: 20px; text-align: center;">
				<img src="<?php echo JURI::root(); ?>administrator/components/com_joodb/assets/images/remote.png" alt="*"/>
				<div id="indicator1" style="margin: 5px 0; padding: 5px; background-color: #ccc; border: 1px solid #ccc; width: 150px; text-align: center;">...</div>
				<div id="indicator2" style="display: none; margin: 5px 0; padding: 5px; background-color: #d40000; color: #fff; border: 1px solid #ccc; width: 150px; text-align: center;"><?php echo JText :: _('Error'); ?></div>
				<div id="indicator3" style="display: none; margin: 5px 0; padding: 5px; background-color: #3c9103; color: #fff; border: 1px solid #ccc; width: 150px; text-align: center;"><?php echo JText :: _('Connection established'); ?></div>
				<button type="button" class="btn btn-small" onmousedown="testConnection();"><?php echo JText :: _('Test Connection'); ?></button>
			</td>
		</tr>
	    <tr>
			<td><label for="jform_user"><?php echo JText :: _('Username'); ?></label></td>
			<td>
				<input type="text" value="" class="inputbox required" name="user" id="jform_user" style="width:250px;"/>
			</td>
		</tr>
	    <tr>
			<td><label for="jform_pass"><?php echo JText :: _('Password'); ?></label></td>
			<td>
				<input type="password" value="" class="inputbox required" name="pass" id="jform_pass" style="width:250px;"/>
			</td>
		</tr>
	    <tr>
			<td><label for="datbase"><?php echo JText :: _('Database'); ?></label></td>
			<td id="dbcontainer">
				<select class="inputbox" id="datbase" name="database"  class="required" style="width:250px;">
				  <option value="">...</option>
				</select>
			</td>
		</tr>
		</table>
	</form>
	<br/>
	<div class="clr"></div>
	</div>
<script type="text/javascript">


// test connection and list availiable databases
function testConnection() {
	var frm = document.adminForm;
	var id = new Array('none','none');
	var success = false
    jQuery('#indicator1').hide();
    jQuery('#indicator2').hide();
    jQuery('#indicator3').hide();
	if (document.formvalidator.isValid(frm)) {
		jQuery.ajaxSetup({ async: false });
		jQuery.getJSON("index.php?option=com_joodb&task=testconnection",
			{'extdb_server': frm.server.value, 'extdb_user': frm.user.value, 'extdb_pass': frm.pass.value},
		  function(response) {
				if (response.dbs) {
					success=true; id[1] = "block";
					frm.database.options.length = 0;
					response.dbs.each(function(el){
						frm.database.options[frm.database.options.length] = new Option(el,el);
					});
				} else if (response.connected) { // No Database list... manual input					
					 success=true;
					 $("dbcontainer").set('html','<input  class="inputbox" type="text" name="database" style="width:250px;"/>');
				} else {
					success=false;
                    jQuery('#indicator1').show();
				}				  
		    });	    
	} else
		alert('<?php echo JText::_( "Fillout required fields" ); ?>');
    if (success) jQuery('#indicator3').show(); else jQuery('#indicator2').show();
   	return success;
}

Joomla.submitbutton = function(task) {
    var frm = document.adminForm;

    if (task=="cancel") {
        Joomla.submitform("display",frm);
        return true;
    }

	if (document.formvalidator.isValid(frm)) {
		if (frm.database.value!="") {
               Joomla.submitform(task,frm);
			return true;
		}
	}

	alert('<?php echo JText::_( "Fillout required fields" ); ?>');
	return false;
}



</script>

