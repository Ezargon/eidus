<?php

// no direct access
defined('_JEXEC') or die('Restricted access');
require_once(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/form.php' );

$item = $this->item;
$jb = & $this->jb;

?>
<form action="index.php" method="post" name="adminForm" id="adminForm"  class="form-validate form-inline" enctype="multipart/form-data">
    <input type="hidden" name="option" value="com_joodb" />
    <input type="hidden" name="joodbid" value="<?php echo $jb->id; ?>" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="<?php echo $jb->fid; ?>" value="<?php echo $this->id?>" />
    <div class="row-fluid">
        <div class="width-60 fltlft span8">
            <!--Stammdaten -->
            <fieldset class="adminform  block">
                <legend><?php echo JText::_( "Editable fields" ); ?></legend>
                <table class="paramlist">
                    <?php
                    foreach ($jb->fields as $fname=>$fcell) {
                        echo '<tr><td class="paramlist_key"><label for="jform_'.preg_replace("/[^A-Z0-9]/i","",$fname).'">'.JText::_(ucfirst(str_replace("_"," ",$fname))).'</label></td><td class="paramlist_value">';
                        echo JoodbFormHelper::getFormField($jb,$item,$fcell,$fname);
                        echo '</td></tr>';
                    }
                    ?>
                </table>
            </fieldset>
        </div>
        <div class="width-40 fltlft span4">
            <fieldset class="adminform block">
                <legend><?php echo JText::_( "Upload image" ); ?></legend>
                <?php
                $imgpath = JPATH_ROOT."/images/joodb/db".$jb->id;
                // attach image to dataset
                if (!file_exists($imgpath)) {
                    if (!@mkdir($imgpath,0777, true)) {
                        echo '<p style="color:red; font-weight: bold;">Can not create JooDB image directory. Make shure that /images is writable</p>';
                    }
                }
                ?>
                <table class="paramlist">
                    <tr>
                        <td colspan="2">
                            <input class="inputbox input-medium" name="dataset_image" type="file" size="30" style="width:200px" maxlength="1000000" accept="*.jpg, *.jpeg, *.png" /><br/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <label><input type="checkbox" name="delete_image" value="1" />&nbsp;<?php echo JText::_('DELETE_IMAGE'); ?></label>
                        </td>
                    </tr>
                    <tr>
                        <td class="paramlist_key"><?php echo JText::_( "Existing image" ); ?></td>
                        <td>
                            <?php
                            if (($item->{$jb->fid}) && (file_exists($imgpath."/img".$item->{$jb->fid}.".jpg"))) {
                                echo '<img  style="border: 1px solid #444; background-color: #ccc; padding: 10px;" src="'.JURI::root(true).'/images/joodb/db'.$jb->id.'/img'.$item->{$jb->fid}.'-thumb.jpg" alt="*" />';
                            } else {
                                echo JText::_( "None" );
                            }
                            ?>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <?php
            // create a set of fields to replace
            $subitems = $jb->getSubitems();
            foreach ($subitems AS $subitem) {
                    if ($subitem->type=="2") {
                        echo '<fieldset class="adminform block"><legend>'.ucfirst($subitem->label).'</legend>';
                        echo JoodbFormHelper::getSubitemSelectMulti($jb,$subitem,$this->id);
                        echo "</fieldset>";
                    }
                }
            ?>
        </div>
    </div>
    <?php echo JHTML::_( 'form.token' );?>
</form>
<script type="text/javascript">

    Joomla.submitbutton = function(task) {
        var frm = document.adminForm;
        if (task == 'listdata') {
            Joomla.submitform(task, frm);
            return true;
        }
        // do field validation
        var valid = document.formvalidator.isValid(frm);
        if (frm.<?php echo $jb->ftitle; ?>.value == ""){
            alert('<?php echo addslashes(JText::_( "Must have title" )); ?>');
            frm.<?php echo $jb->ftitle; ?>.focus();
            return false;
        } else  {
            if (valid == false) {
                alert("<?php echo addslashes(JText::_( "Fillout required fields" )); ?>");
                return false;
            } else {
                if (typeof tinyMCE != "undefined") window.onbeforeunload = function() {};
            }
        }
        Joomla.submitform(task, frm);
    }

</script>
