<?php
/**
 * Plugin Name: Munshaip WC Add Custom Fields Checkout Page
 * Description:  Wc checkout add fields (Text,Textarea,Select,checkbox,radip fields). 
 * Author:      Shailesh Parmar
 * Version:     8.6.7
 * Author URI:  https://www.xyz.com
 * Plugin URI: https://www.xyz.com
 * Text Domain: munshaip

 
 */



if ( ! defined( 'ABSPATH' ) ) {
	die( 'Kangaroos cannot jump here' );
}

if(!defined( 'ABSPATH' )) exit;


if (!function_exists('munshaip_is_woocommerce_active')){
	function munshaip_is_woocommerce_active(){
	    $active_plugins = (array) get_option('active_plugins', array());
	    if(is_multisite()){
		   $active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
	    }
	    return in_array('woocommerce/woocommerce.php', $active_plugins) || array_key_exists('woocommerce/woocommerce.php', $active_plugins);
	}
}


if(munshaip_is_woocommerce_active()) {
	

	/**
	 * woocommerce_init_checkout_field_editor function.
	 */
	function munshaip_init_checkout_field_editor_lite() {
		global $supress_field_modification;
		$supress_field_modification = false;
		
		 define('MUNSHAIP_VERSION', '1.2.3');
		!defined('MUNSHAIP_URL') && define('MUNSHAIP_URL', plugins_url( '/', __FILE__ ));
		!defined('MUNSHAIP_ASSETS_URL') && define('MUNSHAIP_ASSETS_URL', MUNSHAIP_URL . 'assets/');

		if(!class_exists('Munshaip_WC_Checkout_Field_Editor')){
			require_once('classes/class-munshaip-wc-checkout-add.php');
		}

		

		$GLOBALS['Munshaip_WC_Checkout_Field_Editor'] = new Munshaip_WC_Checkout_Field_Editor();
	}
	add_action('init', 'munshaip_init_checkout_field_editor_lite');
	
	function munshaip_is_locale_field( $field_name ){
		if(!empty($field_name) && in_array($field_name, array(
			'additional'
		))){
			return true;
		}
		return false;
	}
	 
	function munshaip_woocommerce_version_check( $version = '3.0' ) {
	  	if(function_exists( 'munshaip_is_woocommerce_active' ) && munshaip_is_woocommerce_active() ) {
			global $woocommerce;
			if( version_compare( $woocommerce->version, $version, ">=" ) ) {
		  		return true;
			}
	  	}
	  	return false;
	}
	
	
	
	
	function munshaip_admin_scripts( $hook ) {


    if ( $hook == 'user-edit.php' ) {
       wp_enqueue_script( 'wc-checkout-editor', plugin_dir_url( __FILE__ ) . 'assets/js/checkout.js', array( 'jquery', 'jquery-ui-datepicker' ), '', true );
    }
}
add_action( 'admin_enqueue_scripts', 'munshaip_admin_scripts', 10, 1 );

	
	/**
	 * Hide Additional Fields title if no fields available.
	 *
	 * @param mixed $old
	 */
	function munshaip_enable_order_notes_field() {
		global $supress_field_modification;

		if($supress_field_modification){
			return $fields;
		}

		$additional_fields = get_option('wc_fields_additional');
		if(is_array($additional_fields)){
			$enabled = 0;
			foreach($additional_fields as $field){
				if($field['enabled']){
					$enabled++;
				}
			}
			return $enabled > 0 ? true : false;
		}
		return true;
	}
	add_filter('woocommerce_enable_order_notes_field', 'munshaip_enable_order_notes_field', 1000);
		
	


	/**
	 * wc_checkout_fields_modify_shipping_fields function.
	 *
	 * @param mixed $old
	 */
	function munshaip_checkout_fields_lite( $fields ) {
		
		global $supress_field_modification;

		if($supress_field_modification){
			return $fields;
		}

		if($additional_fields = get_option('wc_fields_additional')){
			if( isset($fields['order']) && is_array($fields['order']) ){
				$fields['order'] = $additional_fields + $fields['order'];
			}

			
		}
				
		if(isset($fields['order']) && is_array($fields['order'])){
			$fields['order'] = munshaip_prepare_checkout_fields_lite($fields['order'], false);
		}

		if(isset($fields['order']) && !is_array($fields['order'])){
			unset($fields['order']);
		}

		return $fields;
	}
	add_filter('woocommerce_checkout_fields', 'munshaip_checkout_fields_lite', apply_filters('munshaip_checkout_fields_priority', 1000));

	
	/**
	 *
	 */
	function munshaip_prepare_address_fields($fieldset, $original_fieldset = false, $sname = 'billing', $country){
		if(is_array($fieldset) && !empty($fieldset)) {
			$locale = WC()->countries->get_country_locale();
			if(isset($locale[ $country ]) && is_array($locale[ $country ])) {

				foreach($locale[ $country ] as $key => $value){
					
					if(is_array($value) && isset($fieldset[$sname.'_'.$key])){
						if(isset($value['required'])){
							$fieldset[$sname.'_'.$key]['required'] = $value['required'];
						}
					}
					
				}
			}
		
			
			$fieldset = munshaip_prepare_checkout_fields_lite($fieldset, $original_fieldset, $sname);

			return $fieldset;
		}else {

			return $original_fieldset;
		}
	}
	
	

	/**
	 * checkout_fields_modify_fields function.
	 *
	 * @param mixed $data
	 * @param mixed $old
	 */
	 function munshaip_prepare_checkout_fields_lite($fields, $original_fields, $sname = "") {
	
		  
		if(is_array($fields) && !empty($fields)) {
			foreach($fields as $name => $field) {
				if(isset($field['enabled']) && $field['enabled'] == false ) {
					unset($fields[$name]);
				}else{
					$new_field = false;
					
					if($original_fields && isset($original_fields[$name])){
						$new_field = $original_fields[$name];
						
						$new_field['label'] = isset($field['label']) ? $field['label'] : '';
						$new_field['placeholder'] = isset($field['placeholder']) ? $field['placeholder'] : '';
						
						$new_field['class'] = isset($field['class']) && is_array($field['class']) ? $field['class'] : array();
						$new_field['label_class'] = isset($field['label_class']) && is_array($field['label_class']) ? $field['label_class'] : array();
						$new_field['validate'] = isset($field['validate']) && is_array($field['validate']) ? $field['validate'] : array();
						
						$new_field['required'] = isset($field['required']) ? $field['required'] : 0;
						$new_field['clear'] = isset($field['clear']) ? $field['clear'] : 0;
					}else{
						$new_field = $field;
					}
					
					if(isset($new_field['type']) && $new_field['type'] === 'select'){
						if(apply_filters('munshaip_enable_select2_for_select_fields', true)){
							$new_field['input_class'][] = 'munshaip-enhanced-select';
						}
					}

					$new_field['order'] = isset($field['order']) && is_numeric($field['order']) ? $field['order'] : 0;
					if(isset($new_field['order']) && is_numeric($new_field['order'])){
						$priority = ($new_field['order']+1)*10;
						$new_field['priority'] = $priority;
						//$new_field['priority'] = $new_field['order'];
					}
					
					
					$fields[$name] = $new_field;
				}
			}								
			return $fields;
		}else {
			return $original_fields;
		}
	}
	
	/*****************************************
	 ----- Display Field Values - START ------
	 *****************************************/
	
	/**
	 * Display custom fields in emails
	 *
	 * @param array $keys
	 * @return array
	 */
	function munshaip_display_custom_fields_in_emails_lite($order, $sent_to_admin, $plain_text){
		$fields_html = '';
		$value_check = false;
		if(get_option( 'munshaip_account_sync_fields') && get_option( 'munshaip_account_sync_fields') == "on"){
				
				
				$fields = array_merge(Munshaip_WC_Checkout_Field_Editor::get_fields('account'), 
		Munshaip_WC_Checkout_Field_Editor::get_fields('additional'));
		
			}
			else{
				$fields = array_merge( 
		Munshaip_WC_Checkout_Field_Editor::get_fields('additional'));
			}
			
		if($plain_text === false){
			$fields_html .=  '<h2>'.esc_html('Checkout Fields','munshaip').'</h2>';
			$fields_html .= '<table border="1" style="border: solid 1px; width: 100%; margin-bottom: 10px;">';
		}
		
		// Loop through all custom fields to see if it should be added
		foreach( $fields as $key => $options ) {
			if(isset($options['show_in_email']) && $options['show_in_email']){
				$value = '';
				if(munshaip_woo_version_check()){
				if($options['type'] == 'select'){
						$value = get_post_meta( $order->get_id(), $key, true );
						if(is_array($value)){
							$value = implode(",",$value);
						}
						else{
							$value = get_post_meta( $order->get_id(), $key, true );
						}
						
					}else{
						$value = get_post_meta( $order->get_id(), $key, true );
					}
					
				}else{
					if($options['type'] == 'select'){
						$value = get_post_meta( $order->id, $key, true );
						if(is_array($value)){
							$value = implode(",",$value);
						}
						else{
							$value = get_post_meta( $order->id, $key, true );
						}
						
					}else{
						$value = get_post_meta( $order->id, $key, true );
					}
					
					
					
				}
				

				
				if(!empty($value)){
					$value_check = true;
					$label = isset($options['label']) && $options['label'] ? $options['label'] : $key;
					$label = esc_attr($label);
					if($plain_text === false){
						$fields_html .= '<tr><td><strong>'.$label.': </strong></td><td>'.$value.'</td></tr>';
					}else{
						$fields_html .= $label .':'.$value;
					}
					
					
				}
			}
		}

		if($plain_text === false){
						$fields_html .= '</table>';
					}
					
					if($value_check){
						echo $fields_html;
					}
					
	}	
	//add_filter('woocommerce_email_order_meta_fields', 'munshaip_display_custom_fields_in_emails_lite', 10, 3);
	add_action( 'woocommerce_email_order_meta', 'munshaip_display_custom_fields_in_emails_lite', 10, 3 );


	add_action( 'woocommerce_admin_order_data_after_shipping_address', 'munshaip_checkout_field_display_admin_order_meta_shipping', 10, 1 );



	function munshaip_checkout_field_display_admin_order_meta_shipping($order){


		if(munshaip_woocommerce_version_check()){
			$order_id = $order->get_id();	
		}else{
			$order_id = $order->id;
		}

		$fields = array();
		if(!wc_ship_to_billing_address_only() && $order->needs_shipping_address()){
			$fields = array_merge( Munshaip_WC_Checkout_Field_Editor::get_fields('additional'));
		}
		$fields_html = '';
		if(is_array($fields) && !empty($fields)){
			// Loop through all custom fields to see if it should be added
			foreach($fields as $name => $options){
				
				$enabled = (isset($options['enabled']) && $options['enabled'] == false) ? false : true;
				$is_custom_field = (isset($options['custom']) && $options['custom'] == true) ? true : false;
			     
				if(isset($options['show_in_order']) && $options['show_in_order'] && $enabled && $is_custom_field){

					if($options['type'] == 'select'){
						$value = get_post_meta($order_id,$name,true);
						if(is_array($value)){
							$value = implode(",",$value);
						}
						else{
							$value = get_post_meta($order_id,$name,true);
						}
						
					}else{

						$value = get_post_meta($order_id,$name,true);
					}
					
					if(!empty($value)){
						$label = isset($options['label']) && !empty($options['label']) ? __( $options['label'], 'munshaip' ) : $name;
						$fields_html .= '<p><strong>'.__($label,'munshaip').':</strong> <br/>' . $value . '</p>';
					}



				}

			}//end of fields loop


			echo $fields_html;
		}


	    
	}


	function munshaip_order_details_after_customer_details_lite($order){
		
		if(munshaip_woocommerce_version_check()){
			$order_id = $order->get_id();	
		}else{
			$order_id = $order->id;
		}
		
		
		$fields = array();		
		if(!wc_ship_to_billing_address_only() && $order->needs_shipping_address()){
			
			if(get_option( 'munshaip_account_sync_fields') && get_option( 'munshaip_account_sync_fields') == "on"){
			$fields = array_merge(Munshaip_WC_Checkout_Field_Editor::get_fields('account'), 
			Munshaip_WC_Checkout_Field_Editor::get_fields('additional'));
			}
			else{
				$fields = array_merge(
			Munshaip_WC_Checkout_Field_Editor::get_fields('additional'));
			}
		}else{
			
			if(get_option( 'munshaip_account_sync_fields') && get_option( 'munshaip_account_sync_fields') == "on"){
				
				$fields = array_merge(Munshaip_WC_Checkout_Field_Editor::get_fields('account'),  Munshaip_WC_Checkout_Field_Editor::get_fields('additional'));
			}
			else{
				$fields = array_merge( Munshaip_WC_Checkout_Field_Editor::get_fields('additional'));
			}
			
			
		}
		
		
		if(is_array($fields) && !empty($fields)){
			
			$fields_html = '';
			// Loop through all custom fields to see if it should be added
			foreach($fields as $name => $options){

			
				
				$enabled = (isset($options['enabled']) && $options['enabled'] == false) ? false : true;
				$is_custom_field = (isset($options['custom']) && $options['custom'] == true) ? true : false;
			     
				if(isset($options['show_in_order']) && $options['show_in_order'] && $enabled && $is_custom_field){
					if($options['type'] == 'select'){
						$value = get_post_meta($order_id, $name, true);
						if(is_array($value)){
							$value = implode(",",$value);
						}
						else{
							$value = get_post_meta($order_id, $name, true);
						}
						
					}else{
						$value = get_post_meta($order_id, $name, true);
					}
					
					if(!empty($value)){
						$label = isset($options['label']) && !empty($options['label']) ? __( $options['label'], 'munshaip' ) : $name;
						
						if(is_account_page()){
							if(apply_filters( 'munshaip_view_order_customer_details_table_view', true )){
								
								$fields_html .= '<tr><th>'. esc_attr($label) .':</th><td>'. wptexturize($value) .'</td></tr>';
							}else{
								
								$fields_html .= '<br/><dt>'. esc_attr($label) .':</dt><dd>'. wptexturize($value) .'</dd>';
							}
						}else{
							
							if(apply_filters( 'munshaip_thankyou_customer_details_table_view', true )){
								$fields_html .= '<tr><th>'. esc_attr($label) .':</th><td>'. wptexturize($value) .'</td></tr>';
							}else{
								$fields_html .= '<br/><dt>'. esc_attr($label) .':</dt><dd>'. wptexturize($value) .'</dd>';
							}
						}
					}
				}
			}
			
			if($fields_html && !empty($fields_html)){
				do_action( 'munshaip_order_details_before_custom_fields_table', $order ); 
				?>
				<h2 class="woocommerce-order-details__title"><?php esc_html_e('Checkout Fields','munshaip'); ?></h2>
				<table class="woocommerce-table woocommerce-table--custom-fields shop_table custom-fields">
					<?php
						echo $fields_html;
					?>
				</table>
				<?php
				do_action( 'munshaip_order_details_after_custom_fields_table', $order ); 
			}
		}
	}
	

	/**
	 * Meta box display callback.
	 *
	 * @param WP_Post $post Current post object.
	 */
	function munshaip_orderbox_display_callback( $post ) {
		
		// Display code/markup goes here. Don't forget to include nonces!
		 $order = new WC_Order( $post->ID );
		
		if(munshaip_woocommerce_version_check()){
			$order_id = $order->get_id();	
		}else{
			$order_id = $order->id;
		}
		
		
		$fields = array();		
	
			
			if(get_option( 'munshaip_account_sync_fields') && get_option( 'munshaip_account_sync_fields') == "on"){
			$fields = array_merge(Munshaip_WC_Checkout_Field_Editor::get_fields('account'), 
			Munshaip_WC_Checkout_Field_Editor::get_fields('additional'));
			}
			else{
				$fields = array_merge(
			Munshaip_WC_Checkout_Field_Editor::get_fields('additional'));
			}
	
			
			if(get_option( 'munshaip_account_sync_fields') && get_option( 'munshaip_account_sync_fields') == "on"){
				
				$fields = array_merge(Munshaip_WC_Checkout_Field_Editor::get_fields('account'),  Munshaip_WC_Checkout_Field_Editor::get_fields('additional'));
			}
			else{
				$fields = array_merge(Munshaip_WC_Checkout_Field_Editor::get_fields('additional'));
			}
			
			
		
		
		
		if(is_array($fields) && !empty($fields)){
			
			$fields_html = '';
			// Loop through all custom fields to see if it should be added
			foreach($fields as $name => $options){

			
				
				$enabled = (isset($options['enabled']) && $options['enabled'] == false) ? false : true;
				$is_custom_field = (isset($options['custom']) && $options['custom'] == true) ? true : false;
			     
				if(isset($options['show_in_order']) && $options['show_in_order'] && $enabled && $is_custom_field){
					
					if($options['type'] == 'select'){
						$value = get_post_meta($order_id, $name, true);
						if(is_array($value)){
							$value = implode(",",$value);
						}
						else{
							
							$value = get_post_meta($order_id, $name, true);
						
						}
						
					}else{
						
						$value = get_post_meta($order_id, $name, true);
					}
					
					if(!empty($value)){
						$label = isset($options['label']) && !empty($options['label']) ? __( $options['label'], 'munshaip' ) : $name;
						
						if(is_account_page()){
							
								
								$fields_html .= '<tr><th style="text-align:left; width:50%">'. esc_attr($label) .':</th><td style="text-align:left; width:50%">'. wptexturize($value) .'</td></tr>';
							
						}else{
							
							
								$fields_html .= '<tr><th style="text-align:left; width:50%">'. esc_attr($label) .':</th><td style="text-align:left; width:50%">'. wptexturize($value) .'</td></tr>';
							
						}
					}
				}
			}
			
			if($fields_html){
				
				?>
				
				<table width="100%" class="woocommerce-table woocommerce-table--custom-fields shop_table custom-fields">
					<?php
						echo $fields_html;
					?>
				</table>
				<?php
				
			}
		}
	}
	 
	
	
	add_action('woocommerce_order_details_after_order_table', 'munshaip_order_details_after_customer_details_lite', 20, 1);
	
	/*****************************************
	 ----- Display Field Values - END --------
	 *****************************************/

	function munshaip_woo_version_check( $version = '3.0' ) {
	  	if(function_exists( 'munshaip_is_woocommerce_active' ) && munshaip_is_woocommerce_active() ) {
			global $woocommerce;
			if( version_compare( $woocommerce->version, $version, ">=" ) ) {
		  		return true;
			}
	  	}
	  	return false;
	}
	 
}



register_activation_hook( __FILE__, 'munshaip_activate' );
add_action( 'admin_init', 'munshaip_activation_redirect' );

/**
 * Plugin activation callback. Registers option to redirect on next admin load.
 */
function munshaip_activate() {

	add_option( 'munshaip_activation_redirect', true );
}

/**
 * Redirects the user after plugin activation
 */
function munshaip_activation_redirect() {
	// Make sure it's the correct user

	if ( get_option( 'munshaip_activation_redirect', false ) ) {
		// Make sure we don't redirect again after this one
		delete_option( 'munshaip_activation_redirect' );
		wp_safe_redirect( admin_url( 'admin.php?page=munshaip_checkout_register_editor' ) );
		exit;
	}
}