   jQuery(document).ready(function($) {
    // Handle form submission
    $('#custom-form').on('submit', function(e) {
        e.preventDefault(); // Prevent form from submitting normally

        // Get form data
        var formData = new FormData(this);
        formData.append('action', 'modal_form_submit');

        // Perform AJAX request
        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                // Handle the response from the server
                if (response.success) {
                    // Display a success message
                    console.log(response.message);
                } else {
                    // Display an error message
                    console.error(response.message);
                }

                // Close the modal
                $('#custom-modal').modal('hide');
            },
            error: function(xhr, textStatus, errorThrown) {
                // Handle any errors that occurred during the AJAX request
                console.error('Form submission failed: ' + errorThrown);
            }
        });
    });
});
