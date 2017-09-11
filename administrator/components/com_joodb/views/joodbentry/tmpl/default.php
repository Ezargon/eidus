<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

$params = $this->params;
$item = $this->item;
$fields = $this->fields;
$document = JFactory::getDocument();

// 	Load the JEditor object
if ($this->config->get('internal_editor', 1) == 0) {
    $editor = JFactory::getEditor();
} else {
    $document->addScript(JURI::root(true) . '/media/joodb/js/codemirror.js');
    $document->addScript(JURI::root(true) . '/media/joodb/js/addon/mode/overlay.js');
    $document->addScript(JURI::root(true) . '/media/joodb/js/mode/xml/xml.js');
    $document->addScript(JURI::root(true) . '/media/joodb/js/editor.js');
    $document->addStyleSheet(JURI::root(true) . '/media/joodb/css/codemirror.css');
    include_once(JPATH_ROOT . '/media/joodb/editor.php');
    $editor = new JDBEditor();
}

$document->addStyleSheet('components/com_joodb/assets/panes.css');

?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-validate">
    <input type="hidden" name="option" value="com_joodb"/>
    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="view" value="joodb"/>
    <input type="hidden" name="id" value="<?php echo $item->id; ?>"/>
    <input type="hidden" name="published" value="<?php echo $item->published; ?>"/>
    <?php echo JHtml::_('form.token'); ?>
    <div class="row-fluid">
        <div id="config-document" class="width-60 fltlft span8">
            <fieldset class="adminform">
                <legend><?php echo JText::_('Database'); ?></legend>
                <?php
                echo JHtml::_('tabs.start', 'config-pane', array('useCookie' => 1));
                echo JHtml::_('tabs.panel', JText :: _('General options'), "param-options");
                ?>
                <table class="paramlist admintable">
                    <tr>
                        <td width="250" class="paramlist_key"><?php echo JText::_('Database Name'); ?>:</td>
                        <td class="paramlist_value">
                            <input class="inputbox required" type="text" name="name"
                                   value='<?php echo str_replace("\'", "\"", $item->name); ?>' maxlength="50" size="50"
                                   style="width: 250px"/>
                        </td>
                    </tr>
                    <tr>
                        <td width="250" class="paramlist_key"><?php echo JText::_('Table'); ?>:</td>
                        <td class="paramlist_value">
                            <select name="table" class="inputbox required" onchange="submitbutton('apply');"
                                    style="width: 250px"><?php
                                foreach ($this->tables as $table) {
                                    echo "<option" . (($table == $item->table) ? " selected" : "") . ">" . $table . "</option>";
                                }
                                ?></select>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td class="paramlist_value"><br/><b><?php echo JText::_("Special fields"); ?></b></td>
                    </tr>
                    <tr>
                        <td width="250" class="paramlist_key"><?php echo JText::_("Primary Index"); ?>:</td>
                        <td class="paramlist_value">
                            <?php
                            $fselect = JoodbAdminHelper::selectFieldTypes("primary", $fields);
                            echo '<select name="fid"  class="inputbox"  style="width: 250px" >';
                            foreach ($fselect as $fname) {
                                echo "<option" . (($fname == $item->fid) ? " selected" : "") . ">" . $fname . "</option>";
                            }
                            echo "</select>";
                            if (count($fselect) < 1)
                                echo '<p style="color: #d40000; font-weight: bold; clear:both;">' . JText::_("No Primary Index") . '</p>';
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="250" class="paramlist_key"><?php echo JText::_("Title or Headline") ?>:</td>
                        <td class="paramlist_value">
                            <select name="ftitle" class="inputbox" style="width: 250px"><?php
                                $fselect = JoodbAdminHelper::selectFieldTypes("shorttext", $fields);
                                foreach ($fselect as $fname) {
                                    echo "<option" . (($fname == $item->ftitle) ? " selected" : "") . ">" . $fname . "</option>";
                                }
                                ?>    </select>
                        </td>
                    </tr>
                    <tr>
                        <td width="250" class="paramlist_key"><?php echo JText::_("Main Content"); ?>:</td>
                        <td class="paramlist_value">
                            <select name="fcontent" class="inputbox" style="width: 250px"><?php
                                foreach ($fselect as $fname) {
                                    echo "<option" . (($fname == $item->fcontent) ? " selected" : "") . ">" . $fname . "</option>";
                                }
                                ?>    </select>
                        </td>
                    </tr>
                    <tr>
                        <td width="250" class="paramlist_key"><?php echo JText::_("Abstract"); ?>:</td>
                        <td class="paramlist_value">
                            <select name="fabstract" class="inputbox" style="width: 250px">
                                <option value="">...</option><?php
                                foreach ($fselect as $fname) {
                                    echo "<option" . (($fname == $item->fabstract) ? " selected" : "") . ">" . $fname . "</option>";
                                }
                                ?>    </select>
                        </td>
                    </tr>
                    <tr>
                        <td width="250" class="paramlist_key"><?php echo JText::_("Main Date"); ?>:</td>
                        <td class="paramlist_value">
                            <select name="fdate" class="inputbox" style="width: 250px">
                                <option value="">...</option><?php
                                $fselect = JoodbAdminHelper::selectFieldTypes("date", $fields);
                                foreach ($fselect as $fname) {
                                    echo "<option" . (($fname == $item->fdate) ? " selected" : "") . ">" . $fname . "</option>";
                                }
                                ?>    </select>
                        </td>
                    </tr>
                    <tr>
                        <td width="250" class="paramlist_key"><?php echo JText::_("Status Field"); ?>:</td>
                        <td class="paramlist_value">
                            <select name="fstate" class="inputbox" style="width: 250px">
                                <option value="">...</option><?php
                                $fselect = JoodbAdminHelper::selectFieldTypes("number", $fields);
                                foreach ($fselect as $fname) {
                                    echo "<option" . (($fname == $item->fstate) ? " selected" : "") . ">" . $fname . "</option>";
                                }
                                ?>    </select>
                        </td>
                    </tr>
                    <tr>
                        <td width="250" class="paramlist_key"><?php echo JText::_("User ID Field"); ?>:</td>
                        <td class="paramlist_value">
                            <select name="fuser" class="inputbox" style="width: 250px">
                                <option value="">...</option><?php
                                $fselect = JoodbAdminHelper::selectFieldTypes("number", $fields);
                                foreach ($fselect as $fname) {
                                    echo "<option" . (($fname == $item->getSubdata('fuser')) ? " selected" : "") . ">" . $fname . "</option>";
                                }
                                ?>    </select>
                        </td>
                    </tr>
                </table>
                <?php
                echo JHtml::_('tabs.panel', JText :: _('Catalog template'), "config-cattmpl");
                ?>
                <table class="paramlist admintable">
                    <tr>
                        <td class="paramlist_value">
                            <?php
                            echo $editor->display('tpl_list', stripslashes($item->tpl_list), '95%', '500', '40', '6', false);
                            JoodbAdminHelper::printTemplateFooter('tpl_list', $fields, 'catalog');
                            ?>
                        </td>
                    </tr>
                </table>
                <?php
                echo JHtml::_('tabs.panel', JText :: _('Singleview template'), "config-sngltmpl");
                ?>
                <table class="paramlist admintable">
                    <tr>
                        <td class="paramlist_value">
                            <?php
                            echo $editor->display('tpl_single', stripslashes($item->tpl_single), '95%', '500', '40', '6', false);
                            JoodbAdminHelper::printTemplateFooter('tpl_single', $fields, 'single');
                            ?>
                        </td>
                    </tr>
                </table>
                <?php
                echo JHtml::_('tabs.panel', JText :: _('Print template'), "config-prnttmpl");
                ?>
                <table class="paramlist admintable">
                    <tr>
                        <td class="paramlist_value">
                            <?php // 	Load the JEditor object
                            echo $editor->display('tpl_print', stripslashes($item->tpl_print), '95%', '500', '40', '6', false);
                            JoodbAdminHelper::printTemplateFooter('tpl_print', $fields, 'print');
                            ?>
                        </td>
                    </tr>
                </table>
                <?php
                echo JHtml::_('tabs.panel', JText :: _('Form template'), "config-frmtmpl");
                ?>
                <table class="paramlist admintable">
                    <tr>
                        <td class="paramlist_value">
                            <?php // 	Load the JEditor object
                            echo $editor->display('tpl_form', stripslashes($item->tpl_form), '95%', '500', '40', '6', false);
                            JoodbAdminHelper::printTemplateFooter('tpl_form', $fields, 'form');
                            ?>
                        </td>
                    </tr>
                </table>
                <?php
                echo JHtml::_('tabs.end');
                ?>
            </fieldset>
        </div>
        <div class="width-40 fltlft span4">
            <fieldset class="adminform">
                <legend><?php echo JText::_('Parameters'); ?></legend>
                <div class="clr"></div>
                <div class="row-fluid">
                    <?php
                    echo JHtml::_('sliders.start', 'menu-pane', array('useCookie' => 1));
                    $fieldSets = $params->getFieldsets();
                    foreach ($fieldSets as $name => $fieldSet) :
                        echo JHtml::_('sliders.panel', JText :: _($fieldSet->description), "param-page");
                        echo '<div class="block">';
                        foreach ($params->getFieldset($name) as $field):
                            echo '<div class="control-group">';
                            echo '<div class="control-label">' . $field->label . '</div>';
                            echo '<div>' . $field->input . '</div>';
                            echo '</div>';
                        endforeach;
                        echo '</div>';
                    endforeach;
                    echo JHtml::_('sliders.end');

                    ?></div>
            </fieldset>
        </div>
    </div>
</form>
<script type="text/javascript">

    var itemid = '<?php echo $item->id ?>'

    /* Send the Form */
    Joomla.submitbutton = function (task) {
        var frm = document.adminForm;
        frm.task.value = task;
        // tinymce is buggy!!!

        if (task == 'cancel') {
            Joomla.submitform(task, frm);
            return true;
        }

        // do field validation
        if (frm.name.value == "") {
            alert('<?php echo JText::_( "Name Your DB" ); ?>');
            frm.title.focus();
            return false;
        } else {
            if ((frm.table.value == "") && (!document.formvalidator.isValid(frm))) return false;
            // Tinymce wont because the Joomla Developers disabled autosave.
            if (typeof tinyMCE != "undefined") window.onbeforeunload = function() {};
            if (window.Joomla) {
                Joomla.submitform(task, frm);
            } else {
                frm.submit();
            }
        }
        return false;
    }

    /**
     * Calculate center for PopUp Window
     */
    function centerPopup(width, height) {
        var padding = (navigator.appName == "Microsoft Internet Explorer") ? (padding = 10) : (padding = 0);
        var screenw = screen.availWidth;
        var screenh = screen.availHeight;
        var winw = (width + 15 + padding);
        var winh = (height + 15 + padding);
        var posx = (screenw / 2) - (winw / 2);
        var posy = (screenh / 2) - (winh / 2);
        return ",top=" + posy + ",left=" + posx + ",width=" + winw + ",height=" + winh;
    }

    window.addEvent('load', function () {
        jQuery.ajaxSetup({ async: true });
        refreshSubitems('getlistxhr', false);
    });

</script>
