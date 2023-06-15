<?php

/**
 * Fired during plugin activation
 *
 * @link       www.munshaipinfotech.com
 * @since      1.0.0
 *
 * @package    Munshaip
 * @subpackage Munshaip/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Munshaip
 * @subpackage Munshaip/includes
 * @author     shailesh parikh <shailesh5180@gmail.com>
 */

class Munshaip_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
    public function activate() {

        global $wpdb;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        // table to store commission
        if ($wpdb->get_var("show tables like '" . $this->shipmint_tbl_commission() . "'") != $this->shipmint_tbl_commission()) {

            $sql_student = 'CREATE TABLE `' . $this->shipmint_tbl_commission() . '` (
                                  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                                  driver_id bigint(20) DEFAULT 0,
                                  name varchar(250) DEFAULT NULL,
                                  total_commission decimal(19,4) DEFAULT 0.0000,
                                  user_id bigint(20) DEFAULT 0,
                                  pay_commission decimal(19,4) DEFAULT 0.0000,
                                  avil_commission decimal(19,4) DEFAULT 0.0000,
                                  pay_by varchar(250) DEFAULT NULL,
                                  referance_no varchar(100) DEFAULT NULL,
                                  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                  PRIMARY KEY (id),
                                  KEY driver_id (driver_id)
                              ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;';

            dbDelta($sql_student);
        }

      

    }

public function shipmint_tbl_commission() {

        global $wpdb;
        return $wpdb->prefix . "shipmint_commission";
    }

    
}
