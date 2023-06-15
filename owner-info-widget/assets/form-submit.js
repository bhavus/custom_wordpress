
jQuery(document).ready(function($){
    
    jQuery('#hire_me_submit').click(function(){
        
        //-----------------------------------------------------
    // Enquiry Form Validation
    //-----------------------------------------------------
     // add the rule here
     $.validator.addMethod('selectcheck', function (value) {
        return (value != 'none');
    }, "Please select requirement");
    
    $('#hire_me_form').validate({
        rules: {
            name: "required",
            email: "required",
            mobile: "required",
            requirement: { selectcheck: true },
        },
        messages: {
            name: "Please provide your full name",
            email: "Please provide an email address",
            mobile: "Mobile number is required",
            //enquiry_category: { valueNotEquals: "Required" }
        },
        submitHandler: function(form) {

            jQuery.ajax({
                type: 'POST',
                url: $('#hire_me_form').attr('action'),
                action : 'hire_me_submit',
                data: $(form).serialize(),

                beforeSend : function() {
                    $('#hire_me_form').hide();
                    $('.hire-loader').show();
                },
                success : function( response ) {
                    //console.log($('#hire_me_form').attr('action'));
                    console.log($(form).serialize());
                    //alert(response);
                    $('.hire-loader').hide();
                    $('.hire-success-msg').show();
                },
                error: function() { alert("Error posting data."); }
            });
        }
        
    });
        
    });
    
});