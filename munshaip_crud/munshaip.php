<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              www.munshaipinfotech.com
 * @since             1.0.0
 * @package           Munshaip
 *
 * @wordpress-plugin
 * Plugin Name:       Munshaip
 * Plugin URI:        www.munshapinfotech.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            shailesh parikh
 * Author URI:        www.munshaipinfotech.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       munshaip
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
if (!defined('FS_METHOD')) {
    define("FS_METHOD","direct");
}


/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'MUNSHAIP_PLUGIN_VERSION', '1.0.0' );


define('MUNSHAIP_PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));
define('MUNSHAIP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('MUNSHAIP_PLUGIN_PREFIX', "mun_");
define('MUNSHAIP_PLUGIN_BASEPATH', plugin_basename(__FILE__));


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-munshaip-activator.php
 */
function activate_munshaip() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-munshaip-activator.php';
	$activator = new Munshaip_Activator();
    $activator->activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-munshaip-deactivator.php
 */
function deactivate_munshaip() {
	
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-munshaip-activator.php';
    $activator = new Munshaip_Activator();

    require_once plugin_dir_path( __FILE__ ) . 'includes/class-munshaip-deactivator.php';
    $deactivator = new Munshaip_Deactivator($activator);
    $deactivator->deactivate();
}

register_activation_hook( __FILE__, 'activate_munshaip' );
register_deactivation_hook( __FILE__, 'deactivate_munshaip' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-munshaip.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_munshaip() {

	$plugin = new Munshaip();
	$plugin->run();

}
run_munshaip();
