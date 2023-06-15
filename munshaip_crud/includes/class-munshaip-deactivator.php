<?php

/**
 * Fired during plugin deactivation
 *
 * @link       www.munshaipinfotech.com
 * @since      1.0.0
 *
 * @package    Munshaip
 * @subpackage Munshaip/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Munshaip
 * @subpackage Munshaip/includes
 * @author     shailesh parikh <shailesh5180@gmail.com>
 */
class Munshaip_Deactivator {

	private $table_activator;
    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since 1.0.0
     */

     public function __construct($activator)
    {
        $this->table_activator = $activator;
    }
    
    public function deactivate() {
        global $wpdb;

        $wpdb->query("DROP TABLE IF EXISTS " . $this->table_activator->shipmint_tbl_commission());
       
        
    }

}
