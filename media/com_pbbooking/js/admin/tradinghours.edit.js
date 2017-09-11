var tradinghours;
var time_groupings;

jQuery(document).ready(function(){

    bindHours();




});

function HoursModel()
{
    var self = this;

    if (jQuery('input[name=tradinghours]').val() == "") {
        // No trading hours push the defaults in
        jQuery('input[name=tradinghours]').val('[{"status":"open","open_time":"1000","close_time":"2000"},{"status":"open","open_time":"1000","close_time":"2000"},{"status":"open","open_time":"1000","close_time":"2000"},{"status":"open","open_time":"1000","close_time":"2000"},{"status":"open","open_time":"1000","close_time":"2000"},{"status":"open","open_time":"0900","close_time":"1700"},{"status":"closed"}]');
        alert(Joomla.JText._("COM_PBBOOKING_INVALID_TRADING_HOUR_WARNING"));
    }
    
    self.hours = ko.observableArray(jQuery.parseJSON(jQuery('input[name="tradinghours"]').val()));

    //for BC need to convert the time groupings into something managable by knockoutjs
    self.time_groupings = ko.observableArray(
        jQuery.map(shift_times,function(obj,idx){return {"varname":idx,"shift_start":obj.shift_start,"shift_end":obj.shift_end,"display_label":obj.display_label,"checked":0}})
    );


    self.addShift = function(){
        self.time_groupings.push({"varname":'',"shift_start":'',"shift_end":'',"display_label":'','checked':0});
    }

    self.removeShift = function()
    {
        var removeArr = [];
        jQuery.each(self.time_groupings(),function(idx,el){
            if (el.checked == true) {
                removeArr.push(el);
            }
        });
        
        jQuery.each(removeArr,function(idx,el){
            self.time_groupings.remove(el);
        })
    }
}


function bindHours()
{
    tradinghours = new HoursModel();
    ko.applyBindings(tradinghours);
}

Joomla.submitbutton = function(task)
{
    jQuery('input[name="tradinghours"]').val(JSON.stringify(tradinghours.hours()));

    //for BC need to conect the time groupings back to the PBooking format.
    var shifts = {}
    jQuery.each(tradinghours.time_groupings(),function(idx,el){
        // Check to make sure the shift is not empty.
        if (el.varname != '')
        {
            shifts[el.varname] = {"shift_start":el.shift_start,"shift_end":el.shift_end,"display_label":el.display_label}
        }
    });
    jQuery('input[name="time_groupings"]').val(JSON.stringify(shifts));
    
    //now just submit form.
    Joomla.submitform(task, document.getElementById('adminForm'));

}