<?php

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Junk Removal Load Size Calculator
 * @subpackage Junk Removal Load Size Calculator/includes
 */
class Jrlsc_Calculator_Deactivator {

	public function deactivate() {

		global $wpdb;

		// dropping tables on plugin uninstall
       
        //$wpdb->query("DROP TABLE IF EXISTS ". $this->table_activator->jrlsco_tbl_list());
	}
}
