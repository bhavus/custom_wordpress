<?php
 
/**
 * Fired during plugin activation
 *
 * @since      1.0.0
 *
 * @package    Junk Removal Load Size Calculator
 * @subpackage Junk Removal Load Size Calculator/includes
 */

 class Jrlsc_Calculator_Activator {

	public function activate() {

        global $wpdb;

        $shelf_table = "CREATE TABLE `".JRLS_PREFIX_TBL."` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `fname` varchar(150) NOT NULL,
        `lname` varchar(150) NOT NULL,
        `calcxl` int(11) NOT NULL,
        `calcl` int(11) NOT NULL,
        `calcm` int(11) NOT NULL,
        `calcrbb` int(11) NOT NULL,
        `truck` varchar(150) NOT NULL,
        `txtTotalAmount` int(11) NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";

        require_once (ABSPATH.'wp-admin/includes/upgrade.php');
        dbDelta($shelf_table);

	}
}
