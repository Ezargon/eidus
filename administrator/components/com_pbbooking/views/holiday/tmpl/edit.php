<?php

// No direct access
 
defined('_JEXEC') or die('Restricted access'); 

JHtml::_('jquery.framework');
JHtml::_("script","com_pbbooking/external/back.js",true,true);
JHtml::stylesheet("com_pbbooking/pbbooking-admin.css",array(),true);

$t_bod = date_create("tomorrow")->setTime(0,0,0);
$t_eod = date_create("tomorrow")->setTime(23,59);


?> 


<form id="adminForm" name="adminForm" method="POST" action="?option=com_pbbooking&layout=edit&id=<?php echo $this->item->id;?>" class="form form-horizontal">
    <div class="row-fluid">
        <div class="span3">
            <?php echo JText::_('COM_PBBOOKING_CREATE_EDIT_HOLIDAY_INSTRUCTIONS');?>
        </div>
        <div class="span9">
            <fieldset>
                <legend><?php echo JText::_('COM_PBBOOKING_HOLIDAYS');?></legend>
                <?php foreach ($this->form->getGroup('') as $field) :?>
                    <?php echo $field->getControlGroup();?>
                <?php endforeach;?>
            </fieldset>

        </div>
    </div>

    <input type="hidden" name="task" value="holiday.edit"/>
    <?php echo JHtml::_('form.token'); ?>
</form>
<script>
    jQuery("#jform_block_start_date").val("<?php echo $t_bod->format("Y-m-d H:i:00");?>");
    jQuery("#jform_block_end_date").val("<?php echo $t_eod->format("Y-m-d H:i:00");?>");
</script>