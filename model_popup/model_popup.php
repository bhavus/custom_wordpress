<?php
/*
Plugin Name: custom model popup
Plugin URI: http://www.pradipdebnath.com
Description: This plugin enables the ability to add/display website brand/owner information on a widget.
Author: Pradip Debnath
Version: 1.0
Author URI: http://www.pradipdebnath.com/
*/




// Enqueue necessary scripts and styles
function modal_form_plugin_scripts() {
    // Enqueue Bootstrap CSS
    wp_enqueue_style('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css');

    // Enqueue Bootstrap JS and jQuery
    wp_enqueue_script('jquery');
    wp_enqueue_script('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js', array('jquery'), '4.3.1', true);

    // Enqueue custom script
    wp_enqueue_script('modal-form-script', plugin_dir_url(__FILE__) . 'js/modal-form-script.js', array('jquery'), '1.0', true);

    // Pass AJAX URL to the custom script
    wp_localize_script('modal-form-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'modal_form_plugin_scripts');

// Shortcode handler
function modal_form_shortcode() {
    ob_start(); ?>

    <!-- Button to trigger the modal -->
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#custom-modal">Open Modal</button>

    <!-- Modal -->
    <div class="modal fade" id="custom-modal" tabindex="-1" role="dialog" aria-labelledby="custom-modal-label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="custom-modal-label">Modal Form</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="custom-form" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="mobile">Mobile</label>
                            <input type="text" class="form-control" id="mobile" name="mobile" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <input type="text" class="form-control" id="description" name="description" required>
                        </div>
                        <div class="form-group">
                            <label for="featured_image">Featured Image</label>
                            <input type="file" class="form-control-file" id="featured_image" name="featured_image">
                        </div>
                        <div class="form-group">
                            <label for="category">Category</label>
                            <select class="form-control" id="category" name="category">
                                <?php
                                $categories = get_categories();
                                foreach ($categories as $category) {
                                    echo '<option value="' . $category->term_id . '">' . $category->name . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tags">Tags</label>
                            <input type="text" class="form-control" id="tags" name="tags">
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('modal_form', 'modal_form_shortcode');

// AJAX form submission handler
function modal_form_submit() {
    // Get the form data
    $name = sanitize_text_field($_POST['name']);
    $description = sanitize_text_field($_POST['description']);
    $email = sanitize_email($_POST['email']);
    $mobile = sanitize_text_field($_POST['mobile']);
    $category_id = isset($_POST['category']) ? intval($_POST['category']) : 0;  //
    $tags = sanitize_text_field($_POST['tags']);
   
    //custom meta data
    $data = array(
        'email' => $email,
        'mobile' => $mobile,
    );


    // Store the data in the database
    $post_id = wp_insert_post(array(
        'post_type' => 'post',  //custom post type add
        'post_status' => 'publish',
        'post_title' => $name,
        'post_author' => get_current_user_id(),
        'post_content' => $description,
        'post_category' => array($category_id), //
        'tags_input' => $tags,
        'meta_input' => $data
    ));

    if ($post_id) {
        // Set featured image
        if (isset($_FILES['featured_image']) && !empty($_FILES['featured_image']['name'])) {
            $attachment_id = media_handle_upload('featured_image', $post_id);
            set_post_thumbnail($post_id, $attachment_id);
        }

        // Set category
        // $category_id = isset($_POST['category']) ? intval($_POST['category']) : 0;
        // wp_set_post_categories($post_id, array($category_id));

        // Store additional meta data
        //update_post_meta($post_id, 'email', $email);

        // Prepare the response
        $response = array(
            'success' => true,
            'message' => 'Form submitted successfully!'
        );
    } else {
        // Prepare the response
        $response = array(
            'success' => false,
            'message' => 'Error submitting form. Please try again.'
        );
    }

    // Send the response
    wp_send_json($response);
}
add_action('wp_ajax_modal_form_submit', 'modal_form_submit');
add_action('wp_ajax_nopriv_modal_form_submit', 'modal_form_submit');







