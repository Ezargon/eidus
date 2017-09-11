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
	<form name="adminForm" action="index.php"  class="form-validate"  method="post" >
		<input type="hidden" name="option" value="com_joodb" />
		<input type="hidden" name="server" value="<?php echo JREquest:: getVar("server");?>" />
		<input type="hidden" name="user" value="<?php echo JREquest:: getVar("user");?>" />
		<input type="hidden" name="pass" value="<?php echo JREquest:: getVar("pass");?>" />
		<input type="hidden" name="database" value="<?php echo JREquest:: getVar("database");?>" />
		<input type="hidden" name="view" value="joodbentry" />
		<input type="hidden" name="tmpl" value="component" />
		<input type="hidden" name="layout" value="step2" />
		<input type="hidden" name="task" value="addnew" />
		<table cellpadding="5">
		  <tr>
			<td><label for="jform_dbname"><?php echo JText::_( "Name Your DB" ); ?></label></td>
			<td>
			<input type="text" value="" class="inputbox required" name="dbname" id="jform_dbname" style="width:250px;"/>
			</td>
		  </tr><tr><td><label for="jform_dbtable"><?php echo JText::_( "Please choose table" ); ?></label></td>
		  <td>
			<select name="dbtable"  id="jform_dbtable" class="inputbox required" style="width:250px;" >
		 		<option value="">...</option>
				<?php
					foreach ($this->tables as $table) {
						echo "<option>".$table."</option>";
					}
				?>
			</select>
		   </td>
		</tr>
		<tr>
             <td>&nbsp;</td>
		    <td>
                <hr/>
                <button class="btn btn-small" onmousedown="Joomla.submitbutton('extern');"><?php echo JText :: _('Use External Database'); ?></button>
		    </td>
		</tr>
		</table>
	</form>
	<br/>
	<div class="clr"></div>
	</div>
</div>
<script type="text/javascript">
//Send Form

Joomla.submitbutton = function(task) {
		var frm = document.adminForm;
		if (task=="extern") {
            frm.layout.value="extern";
			Joomla.submitform(task,frm);
			return false;
		}
		if (!document.formvalidator.isValid(frm)) {
			if(frm.dbname.value==""){
				alert('<?php echo JText::_( "Name Your DB" ); ?>');
				return false;
			}
			if(frm.dbtable.selectedIndex<=0){
				alert('<?php echo JText::_( "Please choose table" ); ?>');
				return false;
			}
		}
		Joomla.submitform(task,frm);
	}
</script>
