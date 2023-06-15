<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Kangaroos cannot jump here' );
}

/**
 * WC_Checkout_Field_Editor class.
 */
class Munshaip_WC_Checkout_Field_Editor {

	/**
	 * __construct function.
	 */
	function __construct() {
		// Validation rules are controlled by the local fields and can't be changed
		$this->locale_fields = array(
			// 'additional', 
			'order_comments'
		);

		add_action('admin_menu', array($this, 'admin_menu'));
		add_filter('woocommerce_screen_ids', array($this, 'add_screen_id'));
		add_action('woocommerce_checkout_update_order_meta', array($this, 'save_data'), 10, 2);
		add_action( 'wp_enqueue_scripts', array($this, 'wc_checkout_fields_scripts'));
		add_filter( 'woocommerce_form_field_text', array($this, 'munshaip_checkout_fields_text_field'), 10, 4 );
		add_filter( 'woocommerce_form_field_select', array($this, 'munshaip_checkout_fields_select_field'), 10, 4 );
		add_filter( 'woocommerce_form_field_radio', array($this, 'munshaip_checkout_fields_radio_field'), 10, 4 );
		

	}
	
	/**
	 * menu function.
	 */
	function admin_menu() {
		$this->screen_id = add_submenu_page('woocommerce', esc_html__('WooCommerce Checkout & Register Form Editor', 'munshaip'), esc_html__('Checkout & Add Fields', 'munshaip'), 
		'manage_woocommerce', 'munshaip_checkout_register_editor', array($this, 'the_editor'));

		add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
	}
	
	/**
	 * scripts function.
	 */
	function enqueue_admin_scripts() {
		wp_enqueue_style ('munshaip-style', plugins_url('/assets/css/munshaip-style.css', dirname(__FILE__)));
		
		wp_enqueue_script( 'munshaip-admin-script', plugins_url('/assets/js/munshaip-admin-add.js', dirname(__FILE__)), array('jquery','jquery-ui-tabs','jquery-ui-dialog', 'jquery-ui-sortable',
		'woocommerce_admin', 'select2', 'jquery-tiptip'), MUNSHAIP_VERSION, true );
		
	  		wp_localize_script( 'munshaip-admin-script', 'WcfeAdmin', array(
		    'MSG_INVALID_NAME' => 'NAME contains only following ([a-z,A-Z]), digits ([0-9]) and dashes ("-") underscores ("_")'
		  ));	
	}


	/**
	 * wc_checkout_fields_scripts function.
	 *
	 */
	function wc_checkout_fields_scripts() {
		global $wp_scripts;

		if ( is_checkout() || is_account_page()) {
			wp_enqueue_script( 'wc-checkout-editor-frontend', plugins_url('/assets/js/checkout.js', dirname(__FILE__)), array( 'jquery', 'jquery-ui-datepicker' ), WC()->version, true );

			$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';


			$pattern = array(
				//day
				'd',		//day of the month
				'j',		//3 letter name of the day
				'l',		//full name of the day
				'z',		//day of the year
				'S',

				//month
				'F',		//Month name full
				'M',		//Month name short
				'n',		//numeric month no leading zeros
				'm',		//numeric month leading zeros

				//year
				'Y', 		//full numeric year
				'y'		//numeric year: 2 digit
			);
			$replace = array(
				'dd','d','DD','o','',
				'MM','M','m','mm',
				'yy','y'
			);
			foreach( $pattern as &$p ) {
				$p = '/' . $p . '/';
			}

			wp_localize_script( 'wc-checkout-editor-frontend', 'wc_checkout_fields', array(
				'date_format' => preg_replace( $pattern, $replace, wc_date_format() )
			) );
		}
	}
	


	function munshaip_checkout_fields_text_field( $field = '', $key, $args, $value ) {
		
		
			if ( ( ! empty( $args['clear'] ) ) ) $after = '<div class="clear"></div>'; else $after = '';
			$data_validations = '';
			if ( $args['required'] ) {
				$args['class'][] = 'validate-required';
				$data_validations = 'validate-required';
				$required = ' <abbr class="required" title="' . esc_attr__( 'required', 'munshaip'  ) . '">*</abbr>';
			} else {
				$required = '';
			}

			$args['maxlength'] = ( $args['maxlength'] ) ? 'maxlength="' . absint( $args['maxlength'] ) . '"' : '';
			
			
			$fieldLabel = '';
			$field = '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $key ) . '_field" data-validations="'.$data_validations.'" >';
			if ( $args['label'] ) {
				$fieldLabel = $args['label'];
				$field .= '<label for="' . esc_attr( $key ) . '" class="' . implode( ' ', $args['label_class'] ) .'">' . __($args['label'],'munshaip') . $required . '</label>';
			}
			
			$field .= '<input type="text" class="input-text '.esc_attr( implode( ' ', $args['input_class'] ) ).'" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" placeholder="' . __($args['placeholder'], 'munshaip') . '" '.$args['maxlength'].' value="' . esc_attr( $value ) . '" />';
			
			$field .= '</p>' . $after;

			return $field;
		}



/**
	 * munshaip_checkout_fields_select_field function.
	 *
	 * @param string $field (default: '')
	 * @param mixed $key
	 * @param mixed $args
	 * @param mixed $value
	 */
	function munshaip_checkout_fields_select_field( $field = '', $key, $args, $value ) {
	$customer_user_id = get_current_user_id(); // current user ID here for example
	
			// Getting current customer orders
			$customer_orders = wc_get_orders( array(
				'meta_key' => '_customer_user',
				'meta_value' => $customer_user_id,
				'posts_per_page'=>1,
				'orderby'=>'ID',
                'orderby'=>'DESC'
			) );
			
		$selectedVal = '';
		// Loop through each customer WC_Order objects
		foreach($customer_orders as $order ){

			// Order ID (added WooCommerce 3+ compatibility)
			$order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
			$valArr = "";
			
			$valArr = get_post_meta( $order_id, $key, true );
				
			if(!empty($valArr) && is_array($valArr)){
				
				foreach($valArr as $selectedVal){
					$selectedVal = $selectedVal;
				}
			}
			
			
		}
		
		
		$data_rules_action = '';
		$data_rules = '';
		
		if(isset($args['rules_action_ajax']) && !empty($args['rules_action_ajax'])){
			$data_rules_action = $args['rules_action_ajax'];
			$data_rules = urldecode($args['rules_ajax']);
			
		}
		

		$singleq = "'";
		
		
		if ( ( ! empty( $args['clear'] ) ) ) $after = '<div class="clear"></div>'; else $after = '';

		if ( $args['required'] ) {
			$args['class'][] = 'validate-required';
			$required = ' <abbr class="required" title="' . esc_attr__( 'required', 'munshaip' ) . '">*</abbr>';
		} else {
			$required = '';
		}
		$hasPricing =false;
		$args['maxlength'] = ( $args['maxlength'] ) ? 'maxlength="' . absint( $args['maxlength'] ) . '"' : '';

		$options = '';
		$options .= '<option disabled selected value>'.__('Please Select','munshaip').'</option>';
		
		if ( ! empty( $args['options_json'] ) ) {
			foreach ( $args['options_json'] as $option ) {
			
					$selectedOptions = selected( $selectedVal, $option['key'], false );
					if(empty($selectedOptions)){
						$options .= '<option value = "'. $option['key'] . '">' . esc_html__( $option['text'],'munshaip' ) .'</option>';
					}
					else{
						$options .= '<option value = "'. $option['key'] . '" '.selected( $selectedVal, $option['key'], false ).' >' . esc_html__( $option['text'],'munshaip' ) .'</option>';
					}
				
			}

			$field = '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $key ) . '_field" data-rules='.$singleq.$data_rules.$singleq.' data-rules-action="'.$data_rules_action.'">';

			if ( $args['label'] ) {
				$fieldLabel = $args['label'];
				$field .= '<label for="' . esc_attr( $key ) . '" class="' . implode( ' ', $args['label_class'] ) .'">' .esc_html__($args['label'],'munshaip'). $required . '</label>';
			}

			$class = '';
			
				$field .= '<select name="' . esc_attr( $key ) . '[]" id="' . esc_attr( $key ) . '" class="checkout_chosen_select select wc-enhanced-select ' . $class . '">';
			
			
			
			$field .= $options;
			$field .= '</select>
			</p>' . $after;
		}

		return $field;
	}
	

	/**
		 * munshaip_checkout_fields_radio_field function.
		 *
		 * @param string $field (default: '')
		 * @param mixed $key
		 * @param mixed $args
		 * @param mixed $value
		 */
		function munshaip_checkout_fields_radio_field( $field = '', $key, $args, $value ) {

			if ( ( ! empty( $args['clear'] ) ) ) $after = '<div class="clear"></div>'; else $after = '';

			if ( $args['required'] ) {
				$args['class'][] = 'validate-required';
				$required = ' <abbr class="required" title="' . esc_attr__( 'required', 'munshaip' ) . '">*</abbr>';
			} else {
				$required = '';
			}
			
			$data_rules_action = '';
			$data_rules = '';
			
			if(isset($args['rules_action_ajax']) && !empty($args['rules_action_ajax'])){
				$data_rules_action = $args['rules_action_ajax'];
				$data_rules = urldecode($args['rules_ajax']);
				
			}
			

			$singleq = "'";
			$args['maxlength'] = ( $args['maxlength'] ) ? 'maxlength="' . absint( $args['maxlength'] ) . '"' : '';

			$field = '<div class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $key ) . '_field" data-rules='.$singleq.$data_rules.$singleq.' data-rules-action="'.$data_rules_action.'">';

			$field .= '<fieldset><legend>' . esc_html__($args['label'], 'munshaip') . $required . '</legend>';

			if ( ! empty( $args['options_json'] ) ) {
				
				foreach ( $args['options_json'] as $option ) {
					
					
						$field .= '<label><input type="radio" id="'.$key.'_'.$option['key'].'" ' . checked( $value, $option['key'], false ) . ' name="' . esc_attr( $key ) . '" value="' . esc_attr__( $option['key'], 'munshaip' ) . '" /> ' . esc_html__( $option['text'], 'munshaip' ) . '</label>';
					
				}
				
			}

			$field .= '</fieldset></div>' . $after;

			return $field;
		}



	/**
	 * add_screen_id function.
	 */
	function add_screen_id($ids){
		$ids[] = 'woocommerce_page_munshaip_checkout_register_editor';
		$ids[] = strtolower(esc_html__('WooCommerce', 'munshaip')) .'_page_munshaip_checkout_register_editor';

		return $ids;
	}

	/**
	 * Reset checkout fields.
	 */
	function reset_checkout_fields() {
		
		delete_option('wc_fields_additional');
		echo '<div class="updated"><p>'. esc_html__('SUCCESS: Checkout fields successfully reset', 'munshaip') .'</p></div>';
	}
	
	function is_reserved_field_name( $field_name ){
		if($field_name && in_array($field_name, array(
			 'customer_note', 'order_comments',
			'account_username','account_password'
		))){
			return true;
		}
		return false;
	}
	
	function is_default_field_name($field_name){
		if($field_name && in_array($field_name, array(
			
			 'customer_note', 'order_comments',

			'account_username','account_password'
		))){
			return true;
		}
		return false;
	}
	
	

	/**
	 * Save Data function.
	 */
	function save_data($order_id, $posted){
		
			$types = array( 'additional');
			
		
		$counter  = 0;
		foreach($types as $type){
			$fields = $this->get_fields($type);

			foreach($fields as $name => $field){
				
				if(isset($field['custom']) && $field['custom'] && isset($posted[$name])){
					$value = wc_clean($posted[$name]);
					if($value){
						update_post_meta($order_id, $name, $value);
						
					}
					
				}
		
				
			}

			$counter++;
		}
	}
	
	public static function get_fields($key){

		$fields = array_filter(get_option('wc_fields_'. $key, array()));

		if(empty($fields) || sizeof($fields) == 0){
			 if($key === 'additional'){
				$fields = array(
					'order_comments' => array(
						'type'        => 'text',
						'class'       => array('notes'),
						'label'       => esc_html__('Order Notes', 'munshaip'),
						'fmaxlength'  => 10,
						'default'    => 10,
						'placeholder' => _x('Notes about your order', 'placeholder', 'munshaip')
					)
				);
			}
			
			
			else if($key === 'account'){
				$fields = array(
							
					'account_username' => array(
						'type' => 'text',
						'label' => esc_html__('Email address', 'munshaip')
					),
					'account_password' => array(
						'type' => 'password',
						'label' => esc_html__('Password', 'munshaip')
					)

				);
			}
		}
		return $fields;
	}
			
	function sort_fields_by_order($a, $b){
	    if(!isset($a['order']) || $a['order'] == $b['order']){
	        return 0;
	    }
	    return ($a['order'] < $b['order']) ? -1 : 1;
	}
	
	function get_field_types(){
		
				return array(
					'text' => 'Text',
					'password' => 'Password',
					'email' => 'Email',
					'phone' => 'Phone',
					'textarea' => 'Textarea',
					'select' => 'Select',
					'radio' => 'Radio',
					'checkbox' => 'Checkbox'

				);
		
	}

	/*
	 * New field form popup
	 */	
	function munshaip_new_field_form_pp(){
		$field_types = $this->get_field_types();
		$formTitle = 'New Checkout Field';
		$addClass = '';
		if(isset($_GET['section']) && $_GET['section'] == 'account'){
			$formTitle = 'New Account Page Field';
			$addClass = 'accountdialog';
		}
		?>
		
        <div id="munshaip_new_field_form_pp" title="<?php echo esc_html($formTitle); ?>" class="<?php echo $addClass; ?> munshaip_popup_wrapper">
        <form method="post" id="munshaip_new_field_form" action="">
		 <div class="munshaip_tabs" class="munshaip-tabs">
			<ul>
				<li><a href="#tab-1"><?php echo esc_html__('General Settings','munshaip'); ?></a></li>
				
			</ul>
		
		<div id="munshaip_field_editor_form_new">
		<div id="tab-1">
		
			<input type="hidden" name="i_options" value="" />
						
			<table>
            	<tr>                
                	<td colspan="2" class="err_msgs"></td>
				</tr>
            	<tr>                    
                	<td width="40%"><?php esc_html_e('Field Type','munshaip'); ?></td>
                    <td>
                    	<select name="ftype" style="width:250px;" onchange="munshaipFieldTypeChangeListner(this)">
                        <?php foreach($field_types as $value=>$label){

                         ?>
                        	<option value="<?php echo trim($value); ?>"><?php echo $label; ?></option>
                        <?php } ?>
                       
                     
                       
                        
                        
                        </select>
                    </td>
				</tr>
            	<tr class="rowName">                
                	<td><?php esc_html_e('Name','munshaip'); ?><font color="red"><?php echo esc_html__('*','munshaip'); ?></font></td>
                    <td><input type="text" name="fname" placeholder="<?php esc_attr_e('eg. new_field', 'munshaip'); ?>" style="width:250px;"/>
					<br><span><?php esc_html_e(' Must be unique of each field', 'munshaip'); ?></span>
					</td>
				</tr>         
                <tr class="rowLabel">
                    <td><?php esc_html_e('Label of Field','munshaip'); ?></td>
                    <td><input type="text" name="flabel" placeholder="<?php esc_attr_e('eg. new_field', 'munshaip'); ?>" style="width:250px;"/></td>
				</tr>

				<tr class="rowCustomText">
                    <td><?php esc_html_e('Type your custom text','munshaip'); ?></td>
                    <td><textarea type="text" name="ftext" placeholder="" style="width:250px;"></textarea></td>
				</tr>

                <tr class="rowPlaceholder">                    
                    <td><?php esc_html_e('Placeholder','munshaip'); ?></td>
                    <td><input type="text" name="fplaceholder" placeholder="<?php esc_attr_e('eg. new_field', 'munshaip'); ?>" style="width:250px;"/></td>
				</tr>
				<tr class="rowMaxlength">                    
                    <td><?php esc_html_e('Character limit','munshaip'); ?></td>
                    <td><input type="number" value="15" name="fmaxlength" style="width:250px;"/></td>
				</tr>
				 <tr class="rowClass">
                    <td><?php esc_html_e('Field Width','munshaip'); ?></td>
                    <td>
                    	<select name="fclass" style="width:250px;">
							<option value="form-row-wide"><?php esc_html_e('Full-Width','munshaip'); ?></option>
							<option value="form-row-first"><?php esc_html_e('Half-Width','munshaip'); ?></option>
						</select>
                    </td>
				</tr>
                <tr class="rowOptions">                    
                    <td><?php esc_html_e('Options','munshaip'); ?><font color="red"><?php echo esc_html__('*','munshaip'); ?></font></td>
                    <td>
					<table border="0" cellpadding="0" cellspacing="0" class="munshaip-option-list thpladmin-dynamic-row-table"><tbody class="ui-sortable">
					<tr>
						<td style="width:190px;"><input type="text" name="i_options_key[]" placeholder="<?php esc_attr_e('Option Value', 'munshaip'); ?>" style="width:180px;"></td>
						<td style="width:190px;"><input type="text" name="i_options_text[]" placeholder="<?php esc_attr_e('Option Text', 'munshaip'); ?>" style="width:180px;"></td>
						
						
						
						
						
						<td class="action-cell"><a href="javascript:void(0)" onclick="munshaipAddNewOptionRow(this)" class="btn btn-blue" title="Add new option">+</a></td>
						<td class="action-cell"><a href="javascript:void(0)" onclick="munshaipRemoveOptionRow(this)" class="btn btn-red" title="Remove option">x</a></td>
						<td class="action-cell sort ui-sortable-handle"></td>
					</tr>
				</tbody></table>
					
					</td>
					
				</tr>
               
                                            
                <tr class="rowValidate">                    
                    <td><?php esc_html_e('Validation','munshaip'); ?></td>
                    <td>
                    	<select multiple="multiple" name="fvalidate" placeholder="<?php esc_attr_e('Selecgt Validations', 'munshaip'); ?>" class="munshaip-enhanced-multi-select" 
                        style="width: 250px; height:30px;">
                            <option value="email"><?php esc_html_e('Email','munshaip'); ?></option>
                            <option value="phone"><?php esc_html_e('Phone','munshaip'); ?></option>
							
                        </select>
                    </td>
				</tr>
				
				
			

				
				<?php
				if(isset($_GET['section']) && $_GET['section'] == 'account'){
					?>
				<tr class="rowAccess">
                	<td>&nbsp;</td>                     
                    <td>                 	
						
						<input type="checkbox" name="faccess" value="yes"/>
                        <label><?php esc_html_e("User Can't edit this field",'munshaip'); ?></label><br/>

                    </td>
                </tr>
				<?php
				}
			
				?>
				
                <tr class="rowRequired">
                	<td>&nbsp;</td>                     
                    <td>                    	
                    	<input type="checkbox" name="frequired" value="yes" checked/>
                        <label><?php esc_html_e('Required','munshaip'); ?></label><br/>
                                             
                    	
                             
                    	<input type="checkbox" name="fenabled" value="yes" checked/>
                        <label><?php esc_html_e('Show / Hide','munshaip'); ?></label>
                    </td>
                </tr>
                <tr class="rowShowInEmail"> 
                	<td>&nbsp;</td>                
                    <td>                    	
                    	<input type="checkbox" name="fshowinemail" value="email" checked/>
                        <label><?php esc_html_e('Display in Emails','munshaip'); ?></label>
                    </td>
                </tr>
               
                <tr class="rowShowInOrder"> 
                	<td>&nbsp;</td>                   
                    <td>                    	
                    	<input type="checkbox" name="fshowinorder" value="order-review" checked/>
                        <label><?php esc_html_e('Display in Order Detail Pages','munshaip'); ?></label>
                    </td>
            	</tr>
            	                          
            </table>
			
		</div>
			  
	
		
		</div>
		
		</div>
          	
         </form>
			
		  
        </div>
        <?php
	}
	
	/*
	 * New field form popup
	 */	
	function munshaip_edit_field_form_pp(){
		$field_types = $this->get_field_types();
		$formTitle = 'Edit Checkout Field';
		if(isset($_GET['section']) && $_GET['section'] == 'account'){
			$formTitle = 'Edit Account Page Field';
			$addClass = 'accountdialog';
		}
		?>
        <div id="munshaip_edit_field_form_pp" title="<?php echo $formTitle; ?>" class="<?php echo $addClass; ?> munshaip_popup_wrapper">
          <form>
			<div class="munshaip_tabs" class="munshaip-tabs">
			<ul>
				<li><a href="#tab-1"><?php echo esc_html__('General Settings','munshaip'); ?></a></li>
			</ul>
		<div id="munshaip_field_editor_form_edit">
		<div id="tab-1">
			<input type="hidden" name="i_options" value="" />
			
			
		  <table>
            	<tr>                
                	<td colspan="2" class="err_msgs"></td>
				</tr>
            	<tr class="rowName">                
                	<td width="40%"><?php esc_html_e('Name','munshaip'); ?><font color="red"><?php echo esc_html__('*','munshaip'); ?></font></td>
                    <td>
                    	<input type="hidden" name="rowId"/>
                    	<input type="hidden" name="fname"/>
                    	<input type="text" name="fnameNew" placeholder="<?php esc_attr_e('eg. New Field','munshaip'); ?>" style="width:250px;"/>
						<br><span><?php esc_html_e(' Must be unique of each field', 'munshaip'); ?></span>
                    </td>
				</tr>
                <tr>                   
                    <td><?php esc_html_e('Field Type','munshaip'); ?></td>
                    <td>
                    	<select name="ftype" style="width:250px;" onchange="munshaipFieldTypeChangeListner(this)">
                        <?php foreach($field_types as $value=>$label){ ?>
                        	<option value="<?php echo trim($value); ?>"><?php echo $label; ?></option>
                        <?php } ?>
                        </select>
                    </td>
				</tr>                
                <tr class="rowLabel">
                    <td><?php esc_html_e('Label','munshaip'); ?></td>
                    <td><input type="text" name="flabel" placeholder="<?php esc_attr_e('eg. New Field','munshaip'); ?>" style="width:250px;"/></td>
				</tr>
				<tr class="rowCustomText">
                    <td><?php esc_html_e('Type your custom text','munshaip'); ?></td>
                    <td><textarea type="text" name="ftext" placeholder="" style="width:250px;"></textarea></td>
				</tr>
                <tr class="rowPlaceholder">                    
                    <td><?php esc_html_e('Placeholder','munshaip'); ?></td>
                    <td><input type="text" name="fplaceholder" placeholder="<?php esc_attr_e('eg. New Field','munshaip'); ?>" style="width:250px;"/></td>
				</tr>
				<tr class="rowMaxlength">                    
                    <td><?php esc_html_e('Character limit','munshaip'); ?></td>
                    <td><input type="number" value="15" name="fmaxlength" style="width:250px;"/></td>
				</tr>
				<tr class="rowClass">

                    <td><?php esc_html_e('Field Width','munshaip'); ?></td>
                    <td>
                    	<select name="fclass" style="width:250px;">
							<option value="form-row-wide"><?php esc_html_e('Full-Width','munshaip'); ?></option>
							<option value="form-row-first"><?php esc_html_e('Half-Width','munshaip'); ?></option>
						</select>
                    </td>
				</tr>
                <tr class="rowOptions">                    
                    <td><?php esc_html_e('Options','munshaip'); ?><font color="red"><?php echo esc_html__('*','munshaip'); ?></font></td>
                    <td><table border="0" cellpadding="0" cellspacing="0" class="munshaip-option-list thpladmin-dynamic-row-table"><tbody class="ui-sortable">
					<tr>
						<td style="width:190px;"><input type="text" name="i_options_key[]" placeholder="<?php esc_attr_e('Option Value','munshaip'); ?>" style="width:180px;"></td>
						<td style="width:190px;"><input type="text" name="i_options_text[]" placeholder="<?php esc_attr_e('Option Text','munshaip'); ?>" style="width:180px;"></td>
						
						<td class="action-cell"><a href="javascript:void(0)" onclick="munshaipAddNewOptionRow(this)" class="btn btn-blue" title="Add new option">+</a></td>
						<td class="action-cell"><a href="javascript:void(0)" onclick="munshaipRemoveOptionRow(this)" class="btn btn-red" title="Remove option">x</a></td>
						<td class="action-cell sort ui-sortable-handle"></td>
					</tr>
				</tbody></table>
					</td>
				</tr>        
                
				
               
                <tr class="rowValidate">                    
                    <td><?php esc_html_e('Validation','munshaip'); ?></td>
                    <td>
                    	<select multiple="multiple" name="fvalidate" placeholder="Select validations" class="munshaip-enhanced-multi-select" 
                        style="width: 250px; height:30px;">
                            <option value="email"><?php esc_html_e('Email','munshaip'); ?></option>
                            <option value="phone"><?php esc_html_e('Phone','munshaip'); ?></option>
							
                        </select>
                    </td>
				</tr>
			
			
				


				<?php
				if(isset($_GET['section']) && $_GET['section'] == 'account'){
					?>
				<tr class="rowAccess">
                	<td>&nbsp;</td>                     
                    <td>
						
                    	<input type="checkbox" name="faccess" value="yes"/>
                        <label><?php esc_html_e("User Can't edit this field",'munshaip'); ?></label><br/>
                                                
                    </td>
                </tr>
				<?php
				}
		
				?>
				
                <tr class="rowRequired">  
                	<td>&nbsp;</td>                     
                    <td>             	
                    	<input type="checkbox" name="frequired" value="yes" checked/>
                        <label><?php esc_html_e('Required','munshaip'); ?></label><br/>
                                                
                    	
                    	<input type="checkbox" name="fenabled" value="yes" checked/>
                        <label><?php esc_html_e('Show / Hide','munshaip'); ?></label>
                    </td>                    
                </tr>  
                <tr class="rowShowInEmail"> 
                	<td>&nbsp;</td>                   
                    <td>                    	
                    	<input type="checkbox" name="fshowinemail" value="email" checked/>
                        <label><?php esc_html_e('Display in Emails','munshaip'); ?></label>
                    </td>
                </tr> 
                <tr class="rowShowInOrder"> 
                	<td>&nbsp;</td>                   
                    <td>                    	
                    	<input type="checkbox" name="fshowinorder" value="order-review" checked/>
                        <label><?php esc_html_e('Display in Order Detail Pages','munshaip'); ?></label>
                    </td>
                </tr> 
				
				
		
		
		 
		
            </table>
			
			</div>
			
			
		
		</div>
			
          </form>
        </div>
        <?php
	}
	
	function render_tabs_and_sections(){
		$tabs = array( 'fields' => 'Checkout & Add Fields' );
		$tab  = isset( $_GET['tab'] ) ? esc_attr( $_GET['tab'] ) : 'fields';
		
		$sections = ''; $section  = '';
		if($tab === 'fields'){
		
				
			$sections = array(  'additional' );

			
			$section  = isset( $_GET['section'] ) ? esc_attr( $_GET['section'] ) : 'additional';
		

		}
		
		echo '<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">';
		foreach( $tabs as $key => $value ) {
			$active = ( $key == $tab ) ? 'nav-tab-active' : '';
			echo '<a class="nav-tab '.$active.'" href="'.admin_url('admin.php?page=munshaip_checkout_register_editor&tab='.$key).'">'.$value.'</a>';
		}
		echo '</h2>';
		
		if(!empty($sections)){
			echo '<ul class="munshaip-sections">';
			$size = sizeof($sections); $i = 0;
			foreach( $sections as $key ) {
				$i++;
				$active = ( $key == $section ) ? 'current' : '';
				$url = 'admin.php?page=munshaip_checkout_register_editor&tab=fields&section='.$key;
				echo '<li>';
				echo '<a href="'.admin_url($url).'" class="'.$active.'" >'.ucwords($key).' '.esc_html__('Section', 'munshaip').'</a>';
				echo ($size > $i) ? ' ' : '';
				echo '</li>';				
			}

			echo '</ul>';
		}
		
			
			
		
	}
	
	function get_current_tab(){
		return isset( $_GET['tab'] ) ? esc_attr( $_GET['tab'] ) : 'fields';
	}
	
	function get_current_section(){
		$tab = $this->get_current_tab();
		$section = '';
		if($tab === 'fields'){
			$section = isset( $_GET['section'] ) ? esc_attr( $_GET['section'] ) : 'additional';
		}
		return $section;
	}

	function render_checkout_fields_heading_row(){
		?>
		<th class="sort"></th>
		<th class="check-column" style="padding-left:0px !important;"><input type="checkbox" style="margin-left:7px;" onclick="munshaipSelectAllCheckoutFields(this)"/></th>
		<th class="name"><?php esc_html_e('Name','munshaip'); ?></th>
		<th class="id"><?php esc_html_e('Type','munshaip'); ?></th>
		<th><?php esc_html_e('Label','munshaip'); ?></th>
		<th><?php esc_html_e('Placeholder','munshaip'); ?></th>
		<th><?php esc_html_e('Validation Rules','munshaip'); ?></th>
        <th class="status"><?php esc_html_e('Required','munshaip'); ?></th>
		
		<th class="status"><?php esc_html_e('Show / Hide','munshaip'); ?></th>	
        <th class="status"><?php esc_html_e('Edit','munshaip'); ?></th>	
        <?php
	}
	
	function render_actions_row($section){
		?>
        <th colspan="7">
            <button type="button" class="button button-primary" onclick="openNewFieldForm('<?php echo $section; ?>')"><?php _e( '+ Add New field', 'munshaip' ); ?></button>
            <button type="button" class="button" onclick="removeSelectedFields()"><?php _e( 'Remove', 'munshaip' ); ?></button>
            <button type="button" class="button" onclick="enableSelectedFields()"><?php _e( 'Show', 'munshaip' ); ?></button>
            <button type="button" class="button" onclick="disableSelectedFields()"><?php _e( 'Hide', 'munshaip' ); ?></button>
        </th>
        <th colspan="4">
        	<input type="submit" name="save_fields" class="button-primary" value="<?php _e( 'Save changes', 'munshaip' ) ?>" style="float:right" />
            <input type="submit" name="reset_fields" class="button" value="<?php _e( 'Reset to default fields', 'munshaip' ) ?>" style="float:right; margin-right: 5px !important;" 
			onclick="return confirm('Are you sure you want to reset to default fields? all your changes will be deleted.');"/>
        </th>  
    	<?php 
	}
	
	function the_editor() {
		$tab = $this->get_current_tab();
		if($tab === 'fields'){
			$this->checkout_form_field_editor();
		}
	}
	
	function checkout_form_field_editor() {
		$section = $this->get_current_section();
						
		echo '<div class="wrap woocommerce munshaip-wrap"><div class="icon32 icon32-attributes" id="icon-woocommerce"><br /></div>';
			$this->render_tabs_and_sections();
			
			if ( isset( $_POST['save_fields'] ) )
				echo $this->save_options( $section );
			
				
			if ( isset( $_POST['reset_fields'] ) )
				echo $this->reset_checkout_fields();		
	
			global $supress_field_modification;
			$supress_field_modification = false;
		
			
						
			if( $section != 'account' )
			{
			?>
			<form method="post" id="munshaip_checkout_fields_form" action="">
            	<table id="munshaip_checkout_fields" class="wc_gateways widefat" cellspacing="0">
					<thead>
                    	<tr><?php $this->render_actions_row($section); ?></tr>
                    	<tr><?php $this->render_checkout_fields_heading_row(); ?></tr>						
					</thead>
                    <tfoot>
                    	<tr><?php $this->render_checkout_fields_heading_row(); ?></tr>
						<tr><?php $this->render_actions_row($section); ?></tr>
					</tfoot>
					<tbody class="ui-sortable">
                    <?php 
					$i=0;
				
	
					foreach( $this->get_fields( $section ) as $name => $options ) :	
						if ( isset( $options['custom'] ) && $options['custom'] == 1 ) {
							$options['custom'] = '1';
						} else {
							$options['custom'] = '0';
						}
											
						if ( !isset( $options['label'] ) ) {
							$options['label'] = '';
						}
						
						if ( !isset( $options['placeholder'] ) ) {
							$options['placeholder'] = '';
						}
										
						if( isset( $options['options_json'] ) && is_array($options['options_json']) ) {
					
							$options['options_json'] =  urlencode(json_encode($options['options_json']));
						}else{
							$options['options_json'] = '';
						}
					
						
						if( isset( $options['class'] ) && is_array($options['class']) ) {
							$options['class'] = implode(",", $options['class']);
						}else{
							$options['class'] = '';
						}
						
						if( isset( $options['label_class'] ) && is_array($options['label_class']) ) {
							$options['label_class'] = implode(",", $options['label_class']);
						}else{
							$options['label_class'] = '';
						}
						
						if( isset( $options['validate'] ) && is_array($options['validate']) ) {
							$options['validate'] = implode(",", $options['validate']);
						}else{
							$options['validate'] = '';
						}
												
						if ( isset( $options['required'] ) && $options['required'] == 1 ) {
							$options['required'] = '1';
						} else {
							$options['required'] = '0';
						}
						
											
						
						if ( !isset( $options['enabled'] ) || $options['enabled'] == 1 ) {
							$options['enabled'] = '1';
						} else {
							$options['enabled'] = '0';
						}

						if ( !isset( $options['type'] ) ) {
							$options['type'] = 'text';
						} 
						
						if ( isset( $options['show_in_email'] ) && $options['show_in_email'] == 1 ) {
							$options['show_in_email'] = '1';
						} else {
							$options['show_in_email'] = '0';
						}
						
						if ( isset( $options['show_in_order'] ) && $options['show_in_order'] == 1 ) {
							$options['show_in_order'] = '1';
						} else {
							$options['show_in_order'] = '0';
						}
					?>
						<?php
						if($name == 'account_username' || $name == 'account_password'){ ?>
						<tr class="row_<?php echo $i; echo ' munshaip-disabled'; ?>">
						<?php } else { ?>
						<tr class="row_<?php echo $i; echo($options['enabled'] == 1 ? '' : ' munshaip-disabled') ?>">
							<?php } ?>
                        	<td width="1%" class="sort ui-sortable-handle">
                            	<input type="hidden" name="f_custom[<?php echo $i; ?>]" class="f_custom" value="<?php echo $options['custom']; ?>" />
                                <input type="hidden" name="f_order[<?php echo $i; ?>]" class="f_order" value="<?php echo $i; ?>" />
                                                                                                
                                <input type="hidden" name="f_name[<?php echo $i; ?>]" class="f_name" value="<?php echo esc_attr( $name ); ?>" />
                                <input type="hidden" name="f_name_new[<?php echo $i; ?>]" class="f_name_new" value="" />
                                <input type="hidden" name="f_type[<?php echo $i; ?>]" class="f_type" value="<?php echo $options['type']; ?>" />                                
                                <input type="hidden" name="f_label[<?php echo $i; ?>]" class="f_label" value="<?php echo $options['label']; ?>" />
         <input type="hidden" name="f_text[<?php echo $i; ?>]" class="f_text" value="<?php echo stripcslashes(stripcslashes($options['text']));  ?>" />
        
								 <?php if(isset($options['maxlength'])){ ?>
                                <input type="hidden" name="f_maxlength[<?php echo $i; ?>]" class="f_maxlength" value="<?php echo $options['maxlength']; ?>" />
								<?php } ?>
                                
                                <input type="hidden" name="f_placeholder[<?php echo $i; ?>]" class="f_placeholder" value="<?php echo $options['placeholder']; ?>" />
                               
								<input type="hidden" name="f_class[<?php echo $i; ?>]" class="f_class" value="<?php echo $options['class']; ?>" />
                                <input type="hidden" name="f_label_class[<?php echo $i; ?>]" class="f_label_class" value="<?php echo $options['label_class']; ?>" />                          
                                
								
								<input type="hidden" name="f_required[<?php echo $i; ?>]" class="f_required" value="<?php echo($options['required']) ?>" />
                                
                                 <input type="hidden" name="f_options[<?php echo $i; ?>]" class="f_options" value="<?php echo($options['options_json']); ?>" />                               
                                <input type="hidden" name="f_enabled[<?php echo $i; ?>]" class="f_enabled" value="<?php echo($options['enabled']) ?>" />
                                <input type="hidden" name="f_validation[<?php echo $i; ?>]" class="f_validation" value="<?php echo($options['validate']) ?>" />
                                <input type="hidden" name="f_show_in_email[<?php echo $i; ?>]" class="f_show_in_email" value="<?php echo($options['show_in_email']) ?>" />
                                <input type="hidden" name="f_show_in_order[<?php echo $i; ?>]" class="f_show_in_order" value="<?php echo($options['show_in_order']) ?>" />
                                <input type="hidden" name="f_deleted[<?php echo $i; ?>]" class="f_deleted" value="0" />
                                
                                <!--$properties = array('type', 'label', 'placeholder', 'class', 'required', 'clear', 'label_class', 'options');-->
                            </td>
                            <td class="td_select"><input type="checkbox" name="select_field"/></td>
                            <td class="td_name"><?php echo esc_attr( $name ); ?></td>
                            <td class="td_type"><?php echo $options['type']; ?></td>
                            <td class="td_label"><?php echo $options['label']; ?></td>
                            
                            <td class="td_placeholder"><?php echo $options['placeholder']; ?></td>
                            <td class="td_validate"><?php echo $options['validate']; ?></td>
                            <td class="td_required status"><?php echo($options['required'] == 1 ? '<span class="status-enabled tips">Yes</span>' : '-' ) ?></td>
                            
                            <td class="td_enabled status"><?php echo($options['enabled'] == 1 ? '<span class="status-enabled tips">Yes</span>' : '-' ) ?></td>
                            <td class="td_edit">
                            	<button type="button" class="f_edit_btn" <?php echo($options['enabled'] == 1 ? '' : 'disabled') ?> 
                                onclick="openEditFieldForm(this,<?php echo $i; ?>)"><?php _e( 'Edit', 'munshaip' ); ?></button>
                            </td>
                    	</tr>
                    <?php $i++; endforeach; ?>
                	</tbody>
				</table> 
            </form>
			
        <?php
        } 
    ?>
            <?php
            $this->munshaip_new_field_form_pp();
			$this->munshaip_edit_field_form_pp();
			?>
    	</div>
    <?php 		
	}
	
						
	function save_options( $section ) {
		$o_fields      = $this->get_fields( $section );
		$fields        = $o_fields;
		//$core_fields   = array_keys( WC()->countries->get_address_fields( WC()->countries->get_base_country(), $section . '_' ) );
		//$core_fields[] = 'order_comments';
		
		$f_order       = ! empty( $_POST['f_order'] ) ? $_POST['f_order'] : array();
		$f_names       = ! empty( $_POST['f_name'] ) ? $_POST['f_name'] : array();
		$f_names_new   = ! empty( $_POST['f_name_new'] ) ? $_POST['f_name_new'] : array();
		$f_types       = ! empty( $_POST['f_type'] ) ? $_POST['f_type'] : array();
		$f_labels      = ! empty( $_POST['f_label'] ) ? $_POST['f_label'] : array();
		$f_placeholder = ! empty( $_POST['f_placeholder'] ) ? $_POST['f_placeholder'] : array();
		$f_maxlength = ! empty( $_POST['f_maxlength'] ) ? $_POST['f_maxlength'] : array();
		
		if(isset($_POST['f_options'])){
			$f_options     = ! empty( $_POST['f_options'] ) ? $_POST['f_options'] : array();
		}
		$f_text      = ! empty( $_POST['f_text'] ) ? $_POST['f_text'] : array();
		
		$f_class       = ! empty( $_POST['f_class'] ) ? $_POST['f_class'] : array();
		
		
		
		$f_required    = ! empty( $_POST['f_required'] ) ? $_POST['f_required'] : array();
		
		$f_enabled     = ! empty( $_POST['f_enabled'] ) ? $_POST['f_enabled'] : array();
		
		$f_show_in_email = ! empty( $_POST['f_show_in_email'] ) ? $_POST['f_show_in_email'] : array();

		$f_show_in_order = ! empty( $_POST['f_show_in_order'] ) ? $_POST['f_show_in_order'] : array();
		
		$f_validation  = ! empty( $_POST['f_validation'] ) ? $_POST['f_validation'] : array();

		

		$f_deleted     = ! empty( $_POST['f_deleted'] ) ? $_POST['f_deleted'] : array();
						
		$f_position        = ! empty( $_POST['f_position'] ) ? $_POST['f_position'] : array();				
		$f_display_options = ! empty( $_POST['f_display_options'] ) ? $_POST['f_display_options'] : array();
		
		$max   = max( array_map( 'absint', array_keys( $f_names ) ) );

		
		for ( $i = 0; $i <= $max; $i ++ ) {
			$name     = empty( $f_names[$i] ) ? '' : urldecode( sanitize_title( wc_clean( stripslashes( $f_names[$i] ) ) ) );
			$new_name = empty( $f_names_new[$i] ) ? '' : urldecode( sanitize_title( wc_clean( stripslashes( $f_names_new[$i] ) ) ) );
			
			if(!empty($f_deleted[$i]) && $f_deleted[$i] == 1){
				unset( $fields[$name] );
				continue;
			}
						
			// Check reserved names
			if($this->is_reserved_field_name( $new_name )){
				continue;
			}
		
			//if update field
			if( $name && $new_name && $new_name !== $name ){
				
				if ( isset( $fields[$name] ) ) {
					$fields[$new_name] = $fields[$name];
				} else {
					$fields[$new_name] = array();
				}

				unset( $fields[$name] );
				$name = $new_name;
			} else {
				$name = $name ? $name : $new_name;

			}

			if(!$name){
				continue;
			}
						
			//if new field
			if ( !isset( $fields[$name] ) ) {
				$fields[$name] = array();
			}

			$o_type  = isset( $o_fields[$name]['type'] ) ? $o_fields[$name]['type'] : 'text';
			
			//$o_class = isset( $o_fields[$name]['class'] ) ? $o_fields[$name]['class'] : array();
			//$classes = array_diff( $o_class, array( 'form-row-first', 'form-row-last', 'form-row-wide' ) );

			$fields[$name]['type']    	  = empty( $f_types[$i] ) ? $o_type : wc_clean( $f_types[$i] );
			$fields[$name]['label']   	  = empty( $f_labels[$i] ) ? '' : wp_kses_post( trim( stripslashes( $f_labels[$i] ) ) );
			$fields[$name]['text']   	  = empty( $f_text[$i] ) ? '' : $f_text[$i];
			
			
			$fields[$name]['placeholder'] = empty( $f_placeholder[$i] ) ? '' : wc_clean( stripslashes( $f_placeholder[$i] ) );

			$fields[$name]['options_json'] 	  = empty( $f_options[$i] ) ? '' : json_decode(urldecode($f_options[$i]),true);

			$fields[$name]['maxlength'] = empty( $f_maxlength[$i] ) ? '' : wc_clean( stripslashes( $f_maxlength[$i] ) );
			$fields[$name]['class'] 	  = empty( $f_class[$i] ) ? array() : array_map( 'wc_clean', explode( ',', $f_class[$i] ) );
			$fields[$name]['label_class'] = empty( $f_label_class[$i] ) ? array() : array_map( 'wc_clean', explode( ',', $f_label_class[$i] ) );
			
			
			
			$fields[$name]['required']    = empty( $f_required[$i] ) ? false : true;
			
			$fields[$name]['enabled']     = empty( $f_enabled[$i] ) ? false : true;
			$fields[$name]['order']       = empty( $f_order[$i] ) ? '' : wc_clean( $f_order[$i] );
				
			/*if (!empty( $fields[$name]['options'] )) {
				$fields[$name]['options'] = array_combine( $fields[$name]['options'], $fields[$name]['options'] );
			}*/

			


			if (!in_array( $name, $this->locale_fields )){
				$fields[$name]['validate'] = empty( $f_validation[$i] ) ? array() : explode( ',', $f_validation[$i] );
			}

			if (!$this->is_default_field_name( $name )){
				$fields[$name]['custom'] = true;
				$fields[$name]['show_in_email'] = empty( $f_show_in_email[$i] ) ? false : true;
				$fields[$name]['show_in_order'] = empty( $f_show_in_order[$i] ) ? false : true;
			} else {
				$fields[$name]['custom'] = false;
			}
			
			$fields[$name]['label']   	  = sanitize_text_field($fields[$name]['label']);
			$fields[$name]['placeholder'] = sanitize_text_field($fields[$name]['placeholder']);
			
		}
		
		uasort( $fields, array( $this, 'sort_fields_by_order' ) );
		$result = update_option( 'wc_fields_' . $section, $fields );
	
		if ( $result == true ) {
			echo '<div class="updated"><p>' . esc_html__( 'Your changes were saved.', 'munshaip' ) . '</p></div>';
		} else {
			echo '<div class="error"><p> ' . esc_html__( 'Your changes were not saved due to an error (or you made none!).', 'munshaip' ) . '</p></div>';
		}
		
	}

}
