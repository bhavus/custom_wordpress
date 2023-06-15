<?php
/**
 * Plugin Name:       Junk Removal Load Size Calculator
 * Plugin URI:        https://xyz.com/
 * Description:       Manage Junk Removal Load Size Calculator form. Short code name: [estimation-calculator]
 * Version:           1.0.0
 * Author:            Shailesh Parmar
 * Author URI:        https://creedally.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       jrlsc-domain
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Define plugin version.
define( 'JRLS_CALCULATOR_VERSION', '1.0.0' );

// Define plugin dir url.
define( 'JRLS_CALCULATOR_URL', plugin_dir_url( __FILE__ ) );

// Define plugin dir path.
define('JRLS_CALCULATOR_PATH', plugin_dir_path(__FILE__));

global $wpdb;
// Define custom table name.
define('JRLS_PREFIX_TBL', $wpdb->prefix."jrlsco_table_list");

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-jrls-calculator-activator.php
 * 
 * @since 1.0.0
 */
function activate_jrlsc_calculator() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-jrls-calculator-activator.php';
	$activator = new Jrlsc_Calculator_Activator();
	$activator->activate();
}

register_activation_hook( __FILE__, 'activate_jrlsc_calculator' );

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-jrls-calculator-deactivator.php
 * 
 * @since 1.0.0
 */
function deactivate_jrlsc_calculator() {

	require_once plugin_dir_path( __FILE__ ) . 'includes/class-jrls-calculator-deactivator.php';
	$deactivator  = new Jrlsc_Calculator_Deactivator();
	$deactivator->deactivate();
}

register_deactivation_hook( __FILE__, 'deactivate_jrlsc_calculator' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 * 
 * @since 1.0.0
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-jrls-calculator.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function init_join_cryptopedia() {

	$plugin = new Jrlsc_Calculator();
	$plugin->run();
}

add_action( 'plugins_loaded', 'init_join_cryptopedia', 10 );
