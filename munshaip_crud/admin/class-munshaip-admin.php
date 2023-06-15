<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       www.munshaipinfotech.com
 * @since      1.0.0
 *
 * @package    Munshaip
 * @subpackage Munshaip/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Munshaip
 * @subpackage Munshaip/admin
 * @author     shailesh parikh <shailesh5180@gmail.com>
 */
class Munshaip_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	private $table_activator;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-munshaip-activator.php';
		

        $this->table_activator = new Munshaip_Activator();

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function munship_admin_enqueue_styles() {

		
		 $page = isset($_REQUEST['page']) ? trim($_REQUEST['page']) : "";

        $plugin_valid_pages = array(
            
            "shipmint-manage-commission",
            "shipmint-add-commission",
        );
        if (in_array($page, $plugin_valid_pages)) {
            //stylesheet files
            wp_enqueue_style("owt-lib-bootstrap", MUNSHAIP_PLUGIN_URL . 'assets/css/bootstrap.min.css', array(), $this->version, 'all');
            wp_enqueue_style("owt-lib-font-icons", MUNSHAIP_PLUGIN_URL . 'assets/fonts/material-icons.css', array(), $this->version, 'all');
            wp_enqueue_style("owt-lib-materialsdesignicons", MUNSHAIP_PLUGIN_URL . 'assets/css/materialdesignicons.css', array(), $this->version, 'all');
            wp_enqueue_style("owt-lib-datatable", MUNSHAIP_PLUGIN_URL . 'assets/css/jquery.dataTables.min.css', array(), $this->version, 'all');
            wp_enqueue_style("owt-lib-sweetalert", MUNSHAIP_PLUGIN_URL . 'assets/css/sweetalert.css', array(), $this->version, 'all');
            wp_enqueue_style("buttons.dataTables", MUNSHAIP_PLUGIN_URL . 'assets/css/buttons.dataTables.min.css', array(), $this->version, 'all');
            wp_enqueue_style("owt-lib-toastr", MUNSHAIP_PLUGIN_URL . 'assets/css/toastr.min.css', array(), $this->version, 'all');
            wp_enqueue_style("munshaip-custom-css", MUNSHAIP_PLUGIN_URL . 'admin/css/munshaip-admin.css', array(), $this->version, 'all');
            wp_enqueue_style("owt-lib-global", MUNSHAIP_PLUGIN_URL . 'assets/css/owt-lib-global.css', array(), $this->version, 'all');
        }


	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function munship_admin_enqueue_scripts() {

		 $page = isset($_REQUEST['page']) ? trim($_REQUEST['page']) : "";

        $plugin_valid_pages = array(
           
            "shipmint-manage-commission",
            "shipmint-add-commission",
         );
        if (in_array($page, $plugin_valid_pages)) {
            // javascript files
            wp_enqueue_script("bootstrap", MUNSHAIP_PLUGIN_URL . 'assets/js/bootstrap.min.js', array('jquery'), $this->version, true);
            wp_enqueue_script("datatable", MUNSHAIP_PLUGIN_URL . 'assets/js/jquery.dataTables.min.js', array('jquery'), $this->version, true);
            wp_enqueue_script("validate", MUNSHAIP_PLUGIN_URL . 'assets/js/jquery.validate.min.js', array('jquery'), $this->version, true);
            wp_enqueue_script("sweatalert", MUNSHAIP_PLUGIN_URL . 'assets/js/sweetalert.min.js', array('jquery'), $this->version, true);
            wp_enqueue_script("buttons.html5", MUNSHAIP_PLUGIN_URL . 'assets/js/buttons.html5.min.js', array('jquery'), $this->version, true);
            wp_enqueue_script("dataTables.buttons", MUNSHAIP_PLUGIN_URL . 'assets/js/dataTables.buttons.min.js', array('jquery'), $this->version, true);
            
            wp_enqueue_script("vfs_fonts", MUNSHAIP_PLUGIN_URL . 'assets/js/vfs_fonts.js', array('jquery'), $this->version, true);
            wp_enqueue_script("toastr", MUNSHAIP_PLUGIN_URL . 'assets/js/toastr.min.js', array('jquery'), $this->version, true);
            wp_enqueue_script($this->plugin_name, MUNSHAIP_PLUGIN_URL . 'admin/js/munshaip-admin.js', array('jquery'), $this->version, true);
            wp_localize_script($this->plugin_name, "owt_lib", array(
                "ajaxurl" => admin_url("admin-ajax.php")
                
            ));
        }

		

	}

	public function munshaip_admin_menus()
    {
        add_menu_page("Munshaip Crud", "Munshaip Crud", "manage_options", "owt-lib-manage", array($this, "shipmint_management_dashbaord"), "dashicons-book-alt", 58);
        add_submenu_page("owt-lib-manage", "Dashboard", "Dashboard", "manage_options", "owt-lib-manage", array($this, "shipmint_management_dashbaord"));
        add_submenu_page("owt-lib-manage", "Pay Commission", "Pay Commission", "manage_options", "shipmint-manage-commission", array($this, "shipmint_commission_management"));
        add_submenu_page("owt-lib-manage", "", "", "manage_options", "shipmint-add-commission", array($this, "shipmint_add_pay_commission"));
    }

	public function shipmint_management_dashbaord()
	   {
	        echo "<h2>WELCOME TO DASHBOARD</h2>";
	   }	

	public function shipmint_include_template_file($template, $lib_params = array())
    {

        ob_start();
        $params = $lib_params;
        include_once MUNSHAIP_PLUGIN_DIR_PATH . 'admin/views/' . $template . ".php";
        $template = ob_get_contents();
        ob_end_clean();

        echo $template;
    }
	public function shipmint_commission_management()
	   {
        global $wpdb;

       
        $commission_list = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * from " . $this->table_activator->shipmint_tbl_commission() . " ",
                
            )
        );

        $this->shipmint_include_template_file("commission/shipmint-commission-list", array("commissions" => $commission_list));
	   }	
	public function shipmint_add_pay_commission()
	   {
	      global $wpdb;

        $action = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : "add";
        $find_commission = array();
        $valid_opt_fn = array("edit", "delete", "view");
        if (!empty($action) && in_array($action, $valid_opt_fn)) {

            $stid = isset($_REQUEST['stid']) ? intval($_REQUEST['stid']) : 0;

            if ($stid > 0) {

                $find_commission = $wpdb->get_row(
                    $wpdb->prepare(
                        "SELECT * from " . $this->table_activator->shipmint_tbl_commission() . " WHERE id = %d",
                        $stid
                    ),
                    ARRAY_A
                );
            }
        }
      
      
        $this->shipmint_include_template_file("commission/shipmint-pay-commission", array( "commission_data" => $find_commission, "action" => $action));
	   }

   
    public function json($sts, $msg, $arr = array())
    {
        $ar = array('sts' => $sts, 'msg' => $msg, 'arr' => $arr);
        print_r(json_encode($ar));
        die;
    }


    public function munshaip_ajax_request_handler()
    {
        global $wpdb;

        $param = isset($_REQUEST['param']) ? trim($_REQUEST['param']) : "";

        if (!empty($param)) {

            if ($param == "shipmint_add_commission") {

              $reg_id = isset($_REQUEST['txt_reg_id']) ? sanitize_text_field($_REQUEST['txt_reg_id']) : "";
                $txt_name = isset($_REQUEST['txt_name']) ? sanitize_text_field($_REQUEST['txt_name']) : "";
               
                
                $action = isset($_REQUEST['opt_action']) ? trim($_REQUEST['opt_action']) : "add"; // or it will be edit


                if ($action == "add") {

                    if (!empty($reg_id)) { // checking registration id existance 
                        $find_reg_id = $wpdb->get_row(
                            $wpdb->prepare(
                                "SELECT * from " . $this->table_activator->shipmint_tbl_commission() . " WHERE student_id = %s",
                                $reg_id
                            )
                        );
                        if (!empty($find_reg_id)) {
                            $this->json(0, "commission ID already registered");
                        }
                    }

                    $wpdb->insert($this->table_activator->shipmint_tbl_commission(), array(
                        "registration_type" => "admin",
                        "name" => $txt_name,
                        "student_id" => $reg_id,
                       
                    ));

                    if ($wpdb->insert_id > 0) {
                        $this->json(1, "commission pay successfully, reloading page...");
                    } else {
                        $this->json(0, "Failed to commission");
                    }
                } elseif ($action == "edit") {

                    $find_commission = $wpdb->get_row(
                        $wpdb->prepare(
                            "SELECT * from " . $this->table_activator->shipmint_tbl_commission() . " WHERE student_id = %s",
                            $reg_id
                        )
                    );

                    if (!empty($find_commission)) {

                        $wpdb->update(
                            $this->table_activator->shipmint_tbl_commission(),
                            array(
                                "name" => $txt_name,
                                
                            ),
                            array(
                                "id" => $find_commission->id
                            )
                        );

                        $this->json(1, "commission details updated, reloading page...");
                    } else {

                        $this->json(0, "Invalid commission ID");
                    }
                }
            } elseif ($param == "shipmint_delete_commission") {

                $student_id = isset($_REQUEST['st']) ? intval($_REQUEST['st']) : 0;

                if ($student_id > 0) {

                    $find_commission = $wpdb->get_row(
                        $wpdb->prepare(
                            "SELECT * from " . $this->table_activator->shipmint_tbl_commission() . " WHERE id = %d",
                            $student_id
                        )
                    );

                    if (!empty($find_commission)) {

                        $wpdb->delete($this->table_activator->shipmint_tbl_commission(), array(
                            "id" => $student_id
                        ));
                        $this->json(1, "commission deleted successfully");
                    } else {
                        $this->json(0, "Invalid ID, commission not found");
                    }
                } else {
                    $this->json(0, "Invalid ID, commission not found");
                }
            } 

        }//main end if 

        wp_die();
    }



}
