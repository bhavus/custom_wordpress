<?php 
/**
 * Plugin Name: Custom Gallery Slider
 * Plugin URI: http://xyz.com
 * Author: Shailesh Parmar
 * Author URI: http://xyz.me
 * Version: 1.0.0
 * Text Domain: my-plugin
 * Description: A smaple plugin to learn the plugin development. [custom_gallery_slider  type="slider"] or
 * [custom_gallery_slider  type="gallery"]
 */
if( !defined('ABSPATH') ) : exit(); endif;

/**
 * Define plugin constants
 */
define( 'MUNSHAIP_PLUGIN_PATH', trailingslashit( plugin_dir_path(__FILE__) ) );
define( 'MUNSHAIP_PLUGIN_URL', trailingslashit( plugins_url('/', __FILE__) ) );


function custom_gallery_slider_shortcode($atts) {
    
    // Extract shortcode attributes
    $atts = shortcode_atts(array(
        'type' => 'slider',
        'images' => '',
    ), $atts);

    // Conditionally generate the HTML output based on attributes
    $output = '';
    // Process the attributes and generate the HTML output based on conditions
    if ($atts['type'] === 'gallery') {
        // Generate image gallery HTML output
        $output = include $atts['type'].'.php';
    } elseif ($atts['type'] === 'slider') {
        // Generate slider HTML output
        $output = include $atts['type'].'.php';
    } else {
        // Invalid type specified
        $output = 'Invalid type specified for the custom shortcode.';
    }


    // Return the generated output
    return $output;
}

add_action('init', 'custom_gallery_slider_init');

function custom_gallery_slider_init() {
    add_shortcode('custom_gallery_slider', 'custom_gallery_slider_shortcode');
}
