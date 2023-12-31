<?php

function metadata_handle_callback( $post_id, $post, $update ) {

	if ( $update ) {
		return;
	}

	 add_post_meta( $post_id, 'test_meta_key', 'yes' );

	 update_post_meta( $post_id, 'test_meta_key', 'no' );

	delete_post_meta( $post_id, 'test_meta_key' );

}

add_action( 'save_post', 'metadata_handle_callback', 10, 3 );

// Add meta box.
function wporg_add_custom_box() {

	$screens = [ 'post', 'book' ];
	foreach ( $screens as $screen ) {
		add_meta_box(
			'wporg_box_id',                 // Unique ID
			'Custom Meta Box Title',      // Box title
			'wporg_custom_box_html',  // Content callback, must be of type callable
			$screen,                            // Post type
			'side'
		);
	}

}

add_action( 'add_meta_boxes', 'wporg_add_custom_box' );

function wporg_custom_box_html( $post ) {

	$value = get_post_meta( $post->ID, 'custom_meta_key', true );
	?>
	<label for="wporg_field">Description for this field</label>
	<input type="text" name="custom_meta_key" value="<?php echo $value; ?>">
	<?php
}

function wporg_save_postdata( $post_id ) {

	if ( array_key_exists( 'custom_meta_key', $_POST ) ) {
		update_post_meta(
			$post_id,
			'custom_meta_key',
			$_POST['custom_meta_key']
		);
	}
}

add_action( 'save_post', 'wporg_save_postdata' );

function wp_custom_body_class( $classes ) {

	if ( is_single() ) {
		$classes[] = 'buntywp';
	}

	return $classes;

}

add_filter( 'body_class', 'wp_custom_body_class' );

function wp_custom_css_js() {

	wp_enqueue_script(
		'myplugin-script',
		trailingslashit( MY_PLUGIN_URL ) . 'assets/js/plugin.js',
		array(
			'jquery',
		),
		MY_PLUGIN_VER,
		true
	);

	wp_enqueue_style(
		'myplugin-style',
		trailingslashit( MY_PLUGIN_URL ) . 'assets/css/plugin.css'
	);
}

function bunty_custom_rule() {

	global $wp_filesystem;

	include_once ABSPATH . 'wp-admin/includes/file.php';
	WP_Filesystem();

	$states_json = $wp_filesystem->get_contents( trailingslashit( MY_PLUGIN_PATH ) . 'assets/states.json' );

	$states = json_decode( $states_json, true );

}

add_action( 'init', 'bunty_custom_rule' );
