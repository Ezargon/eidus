<?php

// No direct access
 
defined('_JEXEC') or die('Restricted access'); 

JHtml::_('jquery.framework');
JHtml::_('script','com_pbbooking/external/back.js',true,true);
JHtml::_('script','com_pbbooking/admin/calendar.create.js',true,true);
JHtml::_('stylesheet','com_pbbooking/pbbooking-admin.css',array(),true);


$bow = date_create("last Sunday",new DateTimeZone(PBBOOKING_TIMEZONE));
$eow = date_create("this Sunday",new DateTimeZone(PBBOOKING_TIMEZONE));

if (PBBOOKING_VERSION == "free") {
    JHtml::_("stylesheet", "com_pbbooking/pbbooking-admin-pro.css", array(), true);
}
?>

<script>
    jQuery(document).ready(function () {

        <?php if (PBBOOKING_VERSION=="free"):?>
            jQuery("input.pro").attr("disabled","disable");
            jQuery("select.pro").attr("disabled","disable");
            jQuery("button.pro").attr("disabled","disable");
        <?php endif;?>

    });
</script>


<form id="adminForm" name="adminForm" method="POST" action="?option=com_pbbooking&layout=edit&id=<?php echo $this->item->id;?>" class="form form-horizontal">
    <div class="row-fluid">
        <div class="span3">
            <?php echo JText::_('COM_PBBOOKING_CREATE_EDIT_RESOURCE_INSTRUCTIONS');?>

            <?php if (PBBOOKING_VERSION == "free") :?>
                <div class="alert alert-info">
                    <?php echo JText::_("COM_PBBOOKING_CALENDAR_GOOGLE_CAL_ONLY_IN_SUB");?>
                </div>
            <?php endif;?>
        </div>
        <div class="span9">
            <fieldset>
                <legend><?php echo JText::_('COM_PBBOOKING_CALENDAR_CONFIG_DETAILS');?></legend>
                <?php foreach ($this->form->getFieldset('details') as $field) :?>
                    <?php echo $field->getControlGroup();?>
                <?php endforeach;?>
            </fieldset>




            <fieldset>
                <legend class="pro"><?php echo JText::_('COM_PBBOOKING_CONFIG_SUBSCRIBE_DETAILS');?> <span class="pro-feature"><?php echo JText::_("COM_PBBOOKING_SUBSCRIBER_ONLY_ICON");?></span></legend>



                <?php if (PBBOOKING_VERSION=="subscriber" && (JComponentHelper::getParams('com_pbbooking')->get('clientsecret') == "" || JComponentHelper::getParams('com_pbbooking')->get('clientid') == "") )  :?>
                    <div class="alert alert-warning">
                        <?php echo JText::_("COM_PBBOOKING_GOOGLE_CALENDAR_CLIENT_DETAILS_HOW_TO");?>
                    </div>
                <?php endif;?>


                <?php foreach ($this->form->getFieldset('integration') as $field) :?>
                    <?php echo $field->getControlGroup();?>
                <?php endforeach;?>
            </fieldset>


            <fieldset>
                <div id="ko-tradinghours">
                    <legend class="pro"><?php echo JText::_('COM_PBBOOKING_CALENDAR_OVERRIDE_TRADING_TIMES');?> </legend>
                    <!-- this needs to be done manually rather than using JForm to get the layout I want -->
                    <table width="100%">
                        <tr>
                            <th></th>
                            <th align="center"><?php echo JText::_('COM_PBBOOKING_DAY_IS_OPEN');?></th>
                            <th align="center"><?php echo JText::_('COM_PBBOOKING_DAY_OPENING_TIME');?></th>
                            <th align="center"><?php echo JText::_('COM_PBBOOKING_DAY_CLOSING_TIME');?></th>
                            <th align="center"><?php echo JText::_('COM_PBBOOKING_CAL_MAX_BOOKINGS');?></th>
                        </tr>
                        <?php while ($bow < $eow) :?>
                            <tr>
                                <td><?php echo JHtml::_('date',$bow->format(DATE_ATOM),'l');?></td>
                                <td align="center">
                                    <select class="input-mini" name="status[<?php echo $bow->format('w');?>]" data-bind="value: hours()[<?php echo $bow->format('w');?>]['status']">
                                        <option value="open"><?php echo JText::_('JYES');?></option>
                                        <option value="closed"><?php echo JText::_('JNO');?></option>
                                    </select>
                                </td>
                                <td align="center">
                                    <input type="text" class="input-medium" name="open_time[<?php echo $bow->format('w');?>]" data-bind="value: hours()[<?php echo $bow->format('w');?>]['open_time']">
                                </td>
                                <td align="center">
                                    <input type="text" class="input-medium" name="close_time[<?php echo $bow->format('w');?>]" data-bind="value: hours()[<?php echo $bow->format('w');?>]['close_time']">                             
                                </td>
                                <td align="center">
                                    <input type="text" class="input-medium" name="max_bookings[<?php echo $bow->format('w');?>]" data-bind="value: hours()[<?php echo $bow->format('w');?>]['max_bookings']">                             
                                </td>   
                            </tr>
                            <?php $bow->modify('+1 day');?>
                        <?php endwhile;?>
                    </table>
                </div>
            </fieldset>


            <fieldset>
                <div id="ko-classschedule">
                    <legend><?php echo JText::_('COM_PBBOOKING_SERVICE_SCHEDULE');?>  <span class="pro-feature"><?php echo JText::_("COM_PBBOOKING_SUBSCRIBER_ONLY_ICON");?></span> </legend>
                    <?php foreach ($this->form->getFieldset('group_bookings') as $field) :?>
                        <?php echo $field->getControlGroup();?>
                    <?php endforeach;?>

                    <table class="table table-striped" style="width:100%;">
                        <thead>
                            <tr>
                                <th><?php echo JText::_('COM_PBBOOKING_DAYS_HEADING');?></th>
                                <th><?php echo JText::_('COM_PBBOOKING_CLASS_SCHEDULE_START_TIME');?></th>
                                <th><?php echo JText::_('COM_PBBOOKING_CLASS_SCHEDULE_END_TIME');?></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody data-bind="foreach: classes">
                            <tr>
                                <td>
                                    <select data-bind="value: dayofweek" class="pro">
                                        <option value="0"><?php echo JText::_('SUNDAY');?></option>
                                        <option value="1"><?php echo JText::_('MONDAY');?></option>
                                        <option value="2"><?php echo JText::_('TUESDAY');?></option>
                                        <option value="3"><?php echo JText::_('WEDNESDAY');?></option>
                                        <option value="4"><?php echo JText::_('THURSDAY');?></option>
                                        <option value="5"><?php echo JText::_('FRIDAY');?></option>
                                        <option value="6"><?php echo JText::_('SATURDAY');?></option>
                                    </select>
                                </td>
                                <td><input type="text" class="input-mini pro" placeholder="hh:mm" data-bind="value:time"/></td>
                                <td><input type="text" class="input-mini pro" placeholder="hh:mm" data-bind="value:endtime"/></td>
                                <td align="left"><a href="#" data-bind="click: $parent.removeClass" class="fa fa-trash-o" style="color:red;"> </a></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4"><button class="btn btn-primary pro" data-bind="click: addClassTime"><?php echo JText::_('COM_PBBOOKING_ADD');?></button></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
          </fieldset>


        </div>
    </div>

    <input type="hidden" name="task" value="field.edit"/>
    <?php echo JHtml::_('form.token'); ?>
</form>
