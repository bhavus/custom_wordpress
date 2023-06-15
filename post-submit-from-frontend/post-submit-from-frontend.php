<?php
/*
 * Plugin Name: Submit post from frontend
 * Description: My plugin to explain the submit post from frontend functionality [da_frontend_post_form].
 * Version: 1.0
 * Author: Shailesh Parmar
 */


add_shortcode( 'da_frontend_post_form', 'da_frontend_post_form' );
function da_frontend_post_form() {
	if(is_user_logged_in()){
	 $msg = '';
     $errors = 0;


    if (isset($_REQUEST['submit'])) {
        if (wp_verify_nonce($_REQUEST['post_nonce'], 'post_action_nonce')) {
			
            if (empty($_REQUEST['title'])) {
                $errors++;
            } 

            if (empty($_REQUEST['content'])) {
                 $errors++;
            } 

            if (empty($errors)) {
				
				
				$post = array(
					'post_title'    => wp_strip_all_tags($_POST['title']),
					'post_content'  => $_POST['content'],
					'post_category' => array($_POST['cat']), 
					'tags_input'    => $_POST['post_tags'],
					'post_status'   => 'draft',   // Could be: publish
					'post_type' 	=> 'post' // Could be: `page` or your CPT
				);
			    
			   
				$post_id = wp_insert_post($post);
				
				
				if( $post_id){
					$msg = '<h5 style="text-align:center">Saved your post successfully! :)</h5>';
		
					if ( $_FILES["image"]["name"]) {
						$upload = wp_upload_bits( $_FILES["image"]["name"], null, file_get_contents( $_FILES["image"]["tmp_name"] ) );
 
						if ( ! $upload['error'] ) {
							
							$filename = $upload['file'];
							$wp_filetype = wp_check_filetype( $filename, null );
							$attachment = array(
								'post_mime_type' => $wp_filetype['type'],
								'post_title' => sanitize_file_name( $filename ),
								'post_content' => '',
								'post_status' => 'inherit'
							);
				 
							$attachment_id = wp_insert_attachment( $attachment, $filename, $post_id );
				 
							if ( ! is_wp_error( $attachment_id ) ) {
								require_once(ABSPATH . 'wp-admin/includes/image.php');
				 
								$attachment_data = wp_generate_attachment_metadata( $attachment_id, $filename );
								wp_update_attachment_metadata( $attachment_id, $attachment_data );
								set_post_thumbnail( $post_id, $attachment_id );
							}
						}
					}
				}
				echo $msg;
				
                unset($_POST['title']);
				unset($_POST['content']);
				unset($_POST['post_tags']);
            }
        }
		
		
		if(!empty($errors)){
			echo "<p style='text-align: center; color:red;'>Please fill in all the required fields</p>";
		}
    }

    ?>
<div class="postbox">
    <form method="post" enctype="multipart/form-data">

    <p><label for="title">Title *</label><br />
        <input type="text" value="<?php echo !empty( $_POST['title']) ? $_POST['title'] : ''; ?>"
         name="title" />
    </p>

    <p>
        <label for="content">Post Content *</label><br />
        <textarea name="content" rows="6"><?php echo !empty( $_POST['content']) ? $_POST['content'] : ''; ?></textarea>
    </p>
	<p><label>Select Category</label>
    <?php wp_dropdown_categories( 'show_option_none=Category&taxonomy=category&hide_empty=0' ); ?>
	</p>

    <p><label for="post_tags">Tags</label>

    <input type="text" value="<?php echo !empty( $_POST['post_tags']) ? $_POST['post_tags'] : ''; ?>" 
     name="post_tags" /></p>
	
	<p><label for="post_tags">Feature Image</label>
	<input type="file" name="image"></p>

    <?php wp_nonce_field('post_action_nonce', 'post_nonce'); ?>

    <p><input type="submit" value="Submit" name="submit" /></p>
    
    </form>
</div>
    <?php
	}else{
		echo '<h5 style="text-align:center">After Login you can see form</h5>';
	}
}


function wpb_adding_styles() {
	wp_register_style('my_stylesheet', plugin_dir_url( __FILE__ ).'/css/style.css');
	wp_enqueue_style('my_stylesheet');
}
add_action( 'wp_enqueue_scripts', 'wpb_adding_styles' ); 