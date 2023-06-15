<?php


///////////////////////////////////////
///How to submit custom post type in wordpress from the Front-End

if(isset($_POST['title'])){
	
	//echo $_POST['title']; // print title variable value
	
	// create post object
	
	$my_post = array(
			'post_type' => 'movie',
			'post_title' => $_POST['title'],
			'post_content' => $_POST['description'],
			'post_status' => 'publish', // and more status like publish,draft,private 
			
		);
	
	
	// i'm use wordpres predefine function/
	
	$post_id = wp_insert_post($my_post);
	///
	
		
		if (!function_exists('wp_generate_attachment_metadata')){
                require_once(ABSPATH . "wp-admin" . '/includes/image.php');
                require_once(ABSPATH . "wp-admin" . '/includes/file.php');
                require_once(ABSPATH . "wp-admin" . '/includes/media.php');
            }
             if ($_FILES) {
                foreach ($_FILES as $file => $array) {
                    if ($_FILES[$file]['error'] !== UPLOAD_ERR_OK) {
                        return "upload error : " . $_FILES[$file]['error'];
                    }
                    $attach_id = media_handle_upload( $file, $post_id );
                }   
            }
            if ($attach_id > 0){
                //and if you want to set that image as Post  then use:
                update_post_meta($post_id,'_thumbnail_id',$attach_id);
            }
	
	///
	
	
			
	echo 'New Post Saved !';
	
	
	die; // stop script after form submit
}


