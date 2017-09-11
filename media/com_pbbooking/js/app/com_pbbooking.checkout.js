jQuery(document).ready(function(){

	jQuery("form#payment").on("submit",function(){
		var provider = jQuery("input[name=provider]:checked").val();
		if (!provider || provider == '') {
			jQuery("#system-message-container").html('<div class="alert alert-danger">'+Joomla.JText._('COM_PBBOOKING_PAYMENT_ERROR_NO_PROVIDER_CHOSEN')+'</div>');
			return false;
		}
	});

});