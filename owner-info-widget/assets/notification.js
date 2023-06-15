jQuery(document).ready(function($) {
    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {"action": "hire_me_update_notification", },
        success: function (response) {
            //run stuff on success here.  You can use `data` var in the 
           //return so you could post a message.  
            //alert(response);
            
        }
    });
});