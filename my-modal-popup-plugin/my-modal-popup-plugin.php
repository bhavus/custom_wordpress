<?php
/**
 * Plugin Name: My Modal Popup Plugin
 * Plugin URI: https://example.com/
 * Description: Display modal popup flash message after login.
 * Version: 1.0
 * Author: Your Name
 * Author URI: https://example.com/
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Enqueue scripts and styles
function my_modal_popup_plugin_enqueue_scripts() {
    if (is_user_logged_in()) {
        wp_enqueue_script('my-modal-popup-plugin-script', plugins_url('modal-popup.js', __FILE__), array('jquery'), '1.0', true);
        wp_enqueue_style('my-modal-popup-plugin-style', plugins_url('modal-popup.css', __FILE__));
    }
}
add_action('wp_enqueue_scripts', 'my_modal_popup_plugin_enqueue_scripts');

// Add the modal popup HTML
function my_modal_popup_plugin_display_popup() {
    if (is_user_logged_in()) {
        ?>
        <div id="my-modal-popup" class="my-modal-popup">
            <div class="my-modal-content">
                <h3>Welcome back!</h3>
                <p>This is your custom flash message after login.</p>
                <button id="my-modal-close">Close</button>
            </div>
        </div>
        <?php
    }
}
add_action('wp_footer', 'my_modal_popup_plugin_display_popup');
