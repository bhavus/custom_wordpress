<?php
/**
 * Plugin Name: Import Export Post Using WP-CLI
 * Plugin URI: https://xyz.com/
 * Description: Create a custom cli commands for import export post via WP-CLI.
 * Version: 1.0.0
 * Author: Shailesh Parmar
 * Author URI: https://creedally.com/
 * Text Domain: creedally-wp-cli
 * Domain Path: languages
 */

defined( 'ABSPATH' ) or die( 'No direct access!' );

if( ! defined( 'WP_CLI' ) || ! WP_CLI ) { return; }
/**
 * validate wp_cli class activate
 * Activate Plugin only if command
 */
function is_wp_cli_active() {

	return WP_CLI && class_exists( 'WP_CLI_Command' );
}

require_once( plugin_dir_path( __FILE__ ) . 'inc\ca_cache.php' );
require_once( plugin_dir_path( __FILE__ ) . 'inc\ca_exports.php' );

/**
 * get a start_data and end _date between XML file export from a given post
 */
class Load_Export_Data {

    public function get_file( $args, $assoc_args ) {

        $aft_date = !empty( $assoc_args['start_date'] ) ? $assoc_args['start_date'] : '';
        $bef_date = !empty( $assoc_args['end_date'] ) ? $assoc_args['end_date'] : '';
        $posttype = !empty( $assoc_args['post_type'] ) ? explode(',', $assoc_args['post_type'] ) : 'any';

        $query_args = array(
            'post_type' => $posttype,
            'post_status' => 'publish',
        );

        if( !empty( $aft_date ) && !empty( $bef_date ) ) {

            $query_args['date_query'] = array(
                array(
                    'after'     => $aft_date,
                    'before'    => $bef_date,
                    'inclusive' => true,
                ),
            );
        }

        $query = new WP_Query($query_args);

        $assoc_args['posts'] = !empty( $query->posts ) ? $query->posts : '';

        $Wpcli_Exports = new CA_Exports( $args, $assoc_args );
        $Wpcli_Exports->get_ca();
    }
}
/**
 * @wp-hook plugins_loaded
 *
 * @since   1.0.0
 */
add_action(
	'wp_loaded',
	function () {

		if ( ! is_wp_cli_active() ) {
			return;
		}

        // uses: wp ca_export --post_type=post --start_date=2022-12-01 --end_date=2022-12-08
		WP_CLI::add_command( 'ca_export', 'Load_Export_Data' );
	}
);
