<?php

function childtheme_style_andscripts(){
	wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_script('ajax-function',  get_stylesheet_directory_uri() . '/js/ajaxfunction.js', array('jquery'), '1.0', true );
	wp_localize_script( 'ajax-function', 'usersubmitform', array(
		'url'=> admin_url('admin-ajax.php'),
		'security'=> wp_create_nonce('our-nonce')
	) );
}

add_action('wp_enqueue_scripts','childtheme_style_andscripts');


function form_action_function(){
	$data = $_POST['data'];
	
	if( !check_ajax_referer('our-nonce', 'security' ) ){
		
		wp_send_json_error('security failed');
		
		return;
		
	}
	
	//var_dump($data);
	
	$post_id = wp_insert_post (
	array(
		'post_type'=>'post',
		'post_status'=>'draft',
		'post_content'=>$data['content'],
		'post_title'=>$data['name'],
	
	),
	
	 
	true
	
	);
	
	if($post_id){
	update_post_meta($post_id, '_user_metabox_post',$data['email']);
	wp_set_object_terms($post_id, $data['option'], 'category' );
	}
	
	echo 'From Submitted Successfully';
	
	
	die();
}
add_action('wp_ajax_nopriv_form_action_function','form_action_function');
add_action('wp_ajax_form_action_function','form_action_function');