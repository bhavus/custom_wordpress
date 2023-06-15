<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Junk Removal Load Size Calculator
 * @subpackage Junk Removal Load Size Calculator/admin
 */
class Jrlsc_Calculator_Admin {

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
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		$valid_pages = array( "jrls-calculator", "know-your-estimation-form-page", "estimation-list" );

		if( !empty( $_REQUEST['page'] ) &&  in_array( $_REQUEST['page'], $valid_pages )){

			wp_enqueue_style( "jrlsc-bootstrap", JRLS_CALCULATOR_URL . 'assets/css/bootstrap.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( "jrlsc-datatable", JRLS_CALCULATOR_URL . 'assets/css/jquery.dataTables.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( "jrlsc-sweetalert", JRLS_CALCULATOR_URL . 'assets/css/sweetalert.css', array(), $this->version, 'all' );
		}

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/jrls-calculator-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		$valid_pages = array( "jrls-calculator", "estimation-list" );

        if( !empty( $_REQUEST['page'] ) &&  in_array( $_REQUEST['page'], $valid_pages )){

            wp_enqueue_script( "jrlsc-bootstrap-js", JRLS_CALCULATOR_URL . 'assets/js/bootstrap.min.js', array( 'jquery' ), $this->version, false );
            wp_enqueue_script( "jrlsc-datatable-js", JRLS_CALCULATOR_URL . 'assets/js/jquery.dataTables.min.js', array( 'jquery' ), $this->version, false );
            wp_enqueue_script( "jrlsc-validate-js", JRLS_CALCULATOR_URL . 'assets/js/jquery.validate.min.js', array( 'jquery' ), $this->version, false );
            wp_enqueue_script( "jrlsc-sweetalert-js", JRLS_CALCULATOR_URL . 'assets/js/sweetalert.min.js', array( 'jquery' ), $this->version, false );
            wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/jrls-calculator-admin.js', array( 'jquery' ), $this->version, false );

            wp_localize_script($this->plugin_name, "admin_jrlsco", array(
                "ajaxurl" => admin_url("admin-ajax.php")
            ));
		}
	}
	
	public function jrlsc_management_menu(){
	
		add_menu_page("JRLSC", "JRLSC", "manage_options", "jrls-calculator", array($this, "jrlsc_calculator_plugin"), "dashicons-admin-site-alt3", 22);
    
		add_submenu_page("JRLSC","Dashboard", "Dashboard", "manage_options", "jrls-calculator", array($this, "jrlsc_calculator_plugin"));

	}

	public function default_input_options() {

		$defaults = array(
			'input_example'		=>	_e('Enter Valid Url', 'jrlsc-domain'),
			'input_label'		=>	_e('default input label', 'jrlsc-domain'),
			'textarea_example'	=>	'',
			'checkbox_example'	=>	'',
			'radio_example'		=>	'2',
			'time_options'		=>	'default'
		);

		return $defaults;
	}

	/**
	 * Renders a simple page to display for the theme menu defined above.
     *
     * @since 1.0.0
	 */
	public function jrlsc_calculator_plugin($active_tab = '') {
		?>
		<div class="wrap">
			<h2><?php _e( 'Junk Removal Load Size Calculator Options', 'jrlsc_domain' ); ?></h2>
			<?php settings_errors(); ?>
			<?php if( isset( $_GET[ 'tab' ] ) ) {
				$active_tab = $_GET[ 'tab' ];
			} else if( $active_tab == 'get_a_quote' ) {
				$active_tab = 'get_a_quote';
			} else if( $active_tab == 'entries' ) {
				$active_tab = 'entries';
			} else {
				$active_tab = 'price_table';
			}  ?>
			<h2 class="nav-tab-wrapper">
				<a href="?page=jrls-calculator&tab=get_a_quote" class="nav-tab <?php echo $active_tab == 'get_a_quote' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Get a Quote', 'jrlsc-domain' ); ?></a>
				<a href="?page=jrls-calculator&tab=price_table" class="nav-tab <?php echo $active_tab == 'price_table' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Price Table', 'jrlsc-domain' ); ?></a>
				<a href="?page=jrls-calculator&tab=entries" class="nav-tab <?php echo $active_tab == 'entries' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Entries', 'jrlsc-domain' ); ?></a>
			</h2>
			
				<?php
			if( $active_tab == 'get_a_quote' ) { ?>
			   	<form method="post" action="options.php">
					<?php		
					settings_fields( 'jrls_calc_get_a_quote_groups' );
					do_settings_sections( 'jrls_calc_get_a_quote_options' );
					
					do_settings_sections( 'jrls_calc_input_label_options' );
					submit_button();
					?>
				</form>
			<?php

				} elseif( $active_tab == 'price_table' ) {

                    ob_start();

                    include(JRLS_CALCULATOR_PATH."admin/partials/tmpl-static-table.php");

                    $template = ob_get_contents();

                    ob_end_clean();

                    echo $template;

				} else {

                    ob_start();

                    include_once(JRLS_CALCULATOR_PATH."admin/partials/tmpl-list-estimation-shelf.php");

                    $template = ob_get_contents();

                    ob_end_clean();

                    echo $template;
				}
				?>
		</div>
	<?php
	}
    /**
	 * The registering section and fields all option settings
	 *
	 * @since    1.0.0
	 */
    public function init_get_a_quote_options() {
	
		if( false == get_option( 'jrls_calc_get_a_quote_options' ) ) {
			$default_array = $this->default_input_options();
			update_option( 'jrls_calc_get_a_quote_options', $default_array );
		} 
		add_settings_section(
			'input_examples_section',
			__( 'Get a Quote', 'jrlsc-domain' ),
			array( $this, 'get_a_quote_options_callback'),
			'jrls_calc_get_a_quote_options'
		);
		add_settings_field(
			'Input Element',
			__( 'URL : ', 'jrlsc-domain' ),
			array( $this, 'input_element_callback'),
			'jrls_calc_get_a_quote_options',
			'input_examples_section'
		);
		register_setting(
			'jrls_calc_get_a_quote_groups',
			'jrls_calc_get_a_quote_options',
			array( $this, 'validate_input_data')
		); 

		if( false == get_option( 'jrls_calc_input_label_options' ) ) {
			$default_array = $this->default_input_options();
			update_option( 'jrls_calc_input_label_options', $default_array );
		} 
		add_settings_section(
			'input_label_section',
			__( 'Input Label', 'jrlsc-domain' ),
			array( $this, 'input_label_callback'),
			'jrls_calc_input_label_options'
		);
		add_settings_field(
			'Input label',
			__( 'INPUT LABEL  : ', 'jrlsc-domain' ),
			array( $this, 'input_element_label_callback'),
			'jrls_calc_input_label_options',
			'input_label_section'
		);
		register_setting(
			'jrls_calc_get_a_quote_groups',
			'jrls_calc_input_label_options',
			array( $this, 'validate_input_data')
		);
	} 

	public function get_a_quote_options_callback() {
		$options = get_option('jrls_calc_get_a_quote_options');
		return $options;
	}
    /**
	 * The display input field url vlaue 
	 *
	 * @since    1.0.0
	 */
	public function input_element_callback() {

		$options = get_option('jrls_calc_get_a_quote_options');
        ?>
        <input type="url" id="input_example" class="regular-text" name="jrls_calc_get_a_quote_options[input_example]" value="<?php echo !empty( $options['input_example'] ) ? esc_url($options['input_example']) : ''; ?>" />
        <?php
	} 

	public function input_label_callback() {
		$options =  get_option('jrls_calc_input_label_options');
		return $options;
	}
    /**
	 * The display input field  button label vlaue 
	 *
	 * @since    1.0.0
	 */
	public function input_element_label_callback() {

		$options = get_option('jrls_calc_input_label_options');
		?>
        <input type="text" id="input_label" class="regular-text" name="jrls_calc_input_label_options[input_label]" value="<?php echo !empty( $options['input_label'] ) ? esc_html( $options['input_label'] ) : ''; ?>" />
        <?php
	} 
    /**
	 * The registering fields input vlaue validatation settings
	 *
	 * @since    1.0.0
	 */
	public function validate_input_data( $input ) {

		$output = array();

		foreach( $input as $key => $value ) {

			if( isset( $input[$key] ) ) {

				$output[$key] = strip_tags( stripslashes( $input[ $key ] ) );
			} 
		} 
		return apply_filters( 'validate_input_data', $output, $input );
	} 

}
