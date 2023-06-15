<?php

/**
 * Plugin Name:       CRUD Plugin
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       Custom Plugin to exceute crud opreation in Wordpress.
 * Version:           1.00
 * Requires at least: 5.0
 * Requires PHP:      7.0
 * Author:            Sahil Gulati
 * Author URI:        https://facebook.com/sahilgulati007
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       crud-plugin
 */

//creating database on plugin activation
global $jal_db_version;
$jal_db_version = '1.0';

function jal_install() {
    global $wpdb;
    global $jal_db_version;
    $table_name = $wpdb->prefix . 'employee_list';
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  name tinytext NOT NULL,
  address text NOT NULL,
  role text NOT NULL,
  contact bigint(12), 
  PRIMARY KEY  (id)
 ) $charset_collate;";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
    add_option( 'jal_db_version', $jal_db_version );
}
register_activation_hook( __FILE__, 'jal_install' );

//adding in menu
add_action('admin_menu', 'at_try_menu');
function at_try_menu() {
    //adding plugin in menu
    add_menu_page('employee_list', //page title
        'Employee Listing', //menu title
        'manage_options', //capabilities
        'Employee_Listing', //menu slug
        'employee_list' //function
    );
    //adding submenu to a menu
    add_submenu_page('Employee_Listing',//parent page slug
        'employee_insert',//page title
        'Employee Insert',//menu titel
        'manage_options',//manage optios
        'Employee_Insert',//slug
        'employee_insert'//function
    );
    add_submenu_page( null,//parent page slug
        'employee_update',//$page_title
        'Employee Update',// $menu_title
        'manage_options',// $capability
        'Employee_Update',// $menu_slug,
        'employee_update'// $function
    );
    add_submenu_page( null,//parent page slug
        'employee_delete',//$page_title
        'Employee Delete',// $menu_title
        'manage_options',// $capability
        'Employee_Delete',// $menu_slug,
        'employee_delete'// $function
    );

}

// returns the root directory path of particular plugin
define('ROOTDIR', plugin_dir_path(__FILE__));
require_once(ROOTDIR . 'list.php');
require_once (ROOTDIR.'insert.php');
require_once (ROOTDIR.'update.php');
require_once (ROOTDIR.'delete.php');

?>