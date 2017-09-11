var selectedprimaries;

function loadPrimaryValues()
{
    var getprimaryvalues = jQuery.ajax({
        url: '?option=com_pbbooking&task=multilang.getprimaryvalues&format=raw',
        dataType: 'json',
        type:'POST',
        data: {type:jQuery('#jform_type').val()}});

    getprimaryvalues.done(function(data) {
        selectedprimaries = data.data;
        jQuery('#jform_primarylangvalue').empty().append('<option value="">'+Joomla.JText._('JGLOBAL_SELECT_AN_OPTION')+'</option>');
        jQuery.each(data.data,function(idx,value){
            jQuery('#jform_primarylangvalue').append('<option value='+value.id+'>'+value.varname+'</option>');
        });
    });
}

function updatePrimaryValue()
{
    var type = jQuery("#jform_type").val();
    if (type=="message" || type=="subject") {
        var selected = jQuery('#jform_primarylangvalue').val();
        jQuery.each(selectedprimaries,function(idx,value) {
            if (value.id == selected) {
                jQuery('#jform_original_id').val(value.id);
                jQuery('#jform_primaryvalue').val(value.primaryvalue);
            }
        });
    } else {
        var selected = jQuery('#jform_primarylangvalue').val();
        jQuery.each(selectedprimaries,function(idx,value) {
            if (parseInt(value.id) == parseInt(selected)) {
                jQuery('#jform_original_id').val(value.id);
                jQuery('#jform_primaryvalue').val(value.primaryvalue);
            }
        });

    }
}