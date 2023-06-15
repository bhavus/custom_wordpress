<?php
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Junk Removal Load Size Calculator
 * @subpackage Junk Removal Load Size Calculator/public
 */
class Jrlsc_Calculator_Public {

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

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}
	
	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( "jrlsc-bootstrap", JRLS_CALCULATOR_URL . 'assets/css/bootstrap.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/jrls-calculator-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( "jrlsc-bootstrap", JRLS_CALCULATOR_URL . 'assets/js/bootstrap.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/jrls-calculator-public.js', array( 'jquery' ), $this->version, false );

		wp_localize_script($this->plugin_name, "public_jrlsco",array(
			"ajaxurl" => admin_url("admin-ajax.php")
		));

	}
	/**
	 * The public template functionality of the plugin.
	 *
	 * @since      1.0.0
	 */
	public function load_jrlsc_tool_content(){

		ob_start();

		include_once JRLS_CALCULATOR_PATH.'public/partials/tmpl-list-tool-content.php';

		$template = ob_get_contents();

		ob_end_clean();

		return $template;
	}
	/**
	 * The public ajax request handle functionality of the plugin.
	 *
	 * @since      1.0.0
	 **/

	public function handle_ajax_request_public(){

		global $wpdb;

		$param = isset($_REQUEST['param']) ? $_REQUEST['param'] : "";

        $status = 0;

		if( !empty( $param ) && $param == "first_ajax_request" ) {

            $fname = isset($_REQUEST['fname']) ? $_REQUEST['fname'] : "";
            $lname = isset($_REQUEST['lname']) ? $_REQUEST['lname'] : "";
            $calcxl = isset($_REQUEST['calcxl']) ? intval($_REQUEST['calcxl']) : 0;
            $calcl = isset($_REQUEST['calcl']) ? intval($_REQUEST['calcl']) : 0;
            $calcm = isset($_REQUEST['calcm']) ? intval($_REQUEST['calcm']) : 0;
            $calcrbb = isset($_REQUEST['calcrbb']) ? intval($_REQUEST['calcrbb']) : 0;
            $truck = isset($_REQUEST['truck']) ? $_REQUEST['truck'] : "";
            $txtTotalAmount = isset($_REQUEST['txtTotalAmount']) ? intval($_REQUEST['txtTotalAmount']) : 0;

            if( !empty($fname) && ( !empty( $calcxl ) || !empty( $calcl ) || !empty( $calcm ) || !empty( $calcrbb ) ) ){

                $wpdb->insert(JRLS_PREFIX_TBL, array(
                    "fname"   => $fname,
                    "lname"   => $lname,
                    "calcxl"  => $calcxl,
                    "calcl"   => $calcl,
                    "calcm"   => $calcm,
                    "calcrbb" => $calcrbb,
                    "truck"   => $truck,
                    "txtTotalAmount" => $txtTotalAmount,
                ));
            }

            if( $wpdb->insert_id > 0 ) {
                $status = 1;
            }
		}

        $response = array(
            'status' => $status
        );

        wp_send_json( $response , 200 );
        exit();
	}
}
