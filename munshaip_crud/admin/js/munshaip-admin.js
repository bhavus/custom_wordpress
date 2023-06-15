jQuery(function() {

    //var owt_lib_prefix = owt_lib.owt_lib_prefix;

    if (jQuery('#owt-tbl-book-list').length > 0) {
        jQuery('#owt-tbl-book-list').DataTable();
    }

    
    // create student from here...
    jQuery("#wpowt-lib-frm-create-new-student").validate({
        submitHandler: function() {
            jQuery("#wpowt-lib-frm-create-new-student").find("button[type='submit']").text('Processing...').css("cursor", "progress");
            var formdata = jQuery("#wpowt-lib-frm-create-new-student").serialize();
            console.log(formdata);
            var postdata = formdata + "&action=munshaip_ajax_handler&param=shipmint_add_commission";

            jQuery("body").addClass("wpowt-pl-processing");
            jQuery.post(owt_lib.ajaxurl, postdata, function(response) {
                jQuery("body").removeClass("wpowt-pl-processing");
                var data = jQuery.parseJSON(response);

                if (data.sts == 1) {
                    jQuery("#wpowt-lib-frm-create-new-student").find("button[type='submit']").text('Submitted, please wait...').css("cursor", "progress");
                    wpowt_lib_toastr(data.msg, "success");
                    setTimeout(function() {
                        location.reload();
                    }, 1200);
                } else {
                    jQuery("#wpowt-lib-frm-create-new-student").find("button[type='submit']").html('<i class="mdi mdi-check-outline"></i> Submit').css("cursor", "pointer");
                    wpowt_lib_toastr(data.msg, 'error')
                }
            });
        }
    });
    // delete student from here... 
    jQuery(document).on("click", ".wpowt-lib-del-student", function() {

        var conf = confirm("Are you sure want to delete, It will delete all data of book Issues as well ?");
        if (conf) {
            var student_id = jQuery(this).attr("data-id");
            var postdata = "st=" + student_id + "&action=munshaip_ajax_handler&param=shipmint_delete_commission";
            jQuery("body").addClass("wpowt-pl-processing");
            jQuery.post(owt_lib.ajaxurl, postdata, function(response) {
                jQuery("body").removeClass("wpowt-pl-processing");
                var data = jQuery.parseJSON(response);
                if (data.sts == 1) {
                    wpowt_lib_toastr(data.msg, "success");
                    setTimeout(function() {
                        location.reload();
                    }, 1200);
                } else {
                    wpowt_lib_toastr(data.msg, "error");
                }
            });
        }
    });
    

  
    
   
});

function wpowt_lib_toastr(message, type) {
    if (type == "success") {
        toastr.success(message, 'Success')
    } else if (type == "error") {
        toastr.error(message, 'Error')
    }
}