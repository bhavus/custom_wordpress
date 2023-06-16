<?php
/*
Plugin Name: CPT-Taxonomy-Tag-Metabox
Description: custom post tytpe and taxonomy ,Tag and metabox.
Version: 1.0.0
Author: Your Name
*/


function custom_post_type_book() {
  $labels = array(
    'name' => 'Books',
    'singular_name' => 'Book',
    'add_new' => 'Add New',
    'add_new_item' => 'Add New Book',
    'edit_item' => 'Edit Book',
    'new_item' => 'New Book',
    'view_item' => 'View Book',
    'search_items' => 'Search Books',
    'not_found' => 'No books found',
    'not_found_in_trash' => 'No books found in trash',
    'parent_item_colon' => 'Parent Book:',
    'menu_name' => 'Books',
  );

  $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true,
    'show_in_menu' => true,
    'query_var' => true,
    'rewrite' => array('slug' => 'book'),
    'capability_type' => 'post',
    'has_archive' => true,
    'hierarchical' => false,
    'menu_position' => null,
    'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
  );

  register_post_type('book', $args);
}
add_action('init', 'custom_post_type_book');


function custom_taxonomy_genre() {
  $labels = array(
    'name' => 'Genres',
    'singular_name' => 'Genre',
    'search_items' => 'Search Genres',
    'all_items' => 'All Genres',
    'edit_item' => 'Edit Genre',
    'update_item' => 'Update Genre',
    'add_new_item' => 'Add New Genre',
    'new_item_name' => 'New Genre Name',
    'menu_name' => 'Genres',
  );

  $args = array(
    'labels' => $labels,
    'hierarchical' => true,
    'public' => true,
    'show_admin_column' => true,
    'rewrite' => array('slug' => 'genre'),
  );

  register_taxonomy('genre', 'book', $args);
}
add_action('init', 'custom_taxonomy_genre');

/**
 * Register a custom tags called "book_tag".
 *
 * @see get_post_type_labels() for label keys.
 */
function munshaip_cpt_book_tags() {
 
  $labels = array(
    'name'                       => _x('Book Tags','tags general name', 'munshaip'),
    'singular_name'              => _x('Book Tag','tags singular name', 'munshaip'),
    'menu_name'                  => __('Book Tags','munshaip'),
    'all_items'                  => __('All Book Tags','munshaip'),
    'edit_item'                  => __('Edit Book Tag','munshaip'),
    'view_item'                  => __('View Book Tag','munshaip'),
    'update_item'                => __('Update Book Tag','munshaip'),
    'add_new_item'               => __('Add New Book Tag','munshaip'),
    'new_item_name'              => __('New Book Tag Name','munshaip'),
    'parent_item'                => __('Parent Book Tag','munshaip'),
    'parent_item_colon'          => __('Parent Book Tag:','munshaip'),
    'search_items'               => __('Search Book Tags','munshaip'),
    'popular_items'              => __('Popular Book Tags','munshaip'),
    'separate_items_with_commas' => __('Separate book tags with commas','munshaip'),
    'add_or_remove_items'        => __('Add or remove book tags','munshaip'),
    'choose_from_most_used'      => __('Choose from the most used book tags','munshaip'),
    'not_found'                  => __('No book tags found','munshaip'),
  );
  
  $args = array(
    'labels'            => $labels,
    'public'            => true,
    'hierarchical'      => false,
    'rewrite'           => array( 'slug' => 'book-tags' ),
  );
  
  register_taxonomy( 'book_tag', 'book', $args );
}
add_action( 'init', 'munshaip_cpt_book_tags' );

// Step 1: Register the Metabox
function custom_book_metabox() {
  add_meta_box(
    'book_details',
    'Book Details',
    'custom_book_metabox_callback',
    'book',
    'normal',
    'default'
  );
}
add_action('add_meta_boxes', 'custom_book_metabox');

// Step 2: Define the Metabox Callback Function
function custom_book_metabox_callback($post) {
  // Retrieve the current values for the custom fields
 $book_author = get_post_meta($post->ID, 'book_author', true);
  $book_publisher = get_post_meta($post->ID, 'book_publisher', true);
  $book_price = get_post_meta($post->ID, 'book_price', true);

  // Output the HTML markup for the custom fields
  ?>
  <label for="book_author">Author:</label>
  <input type="text" name="book_author" value="<?php echo esc_attr($book_author); ?>">

  <label for="book_publisher">Publisher:</label>
  <input type="text" name="book_publisher" value="<?php echo esc_attr($book_publisher); ?>">

  <label for="book_price">Price:</label>
  <input type="number" step="0.01" name="book_price" value="<?php echo esc_attr($book_price); ?>">
  <?php
}

// Step 3: Save Metabox Data
function save_custom_book_metabox($post_id) {
  // Verify the nonce
  if (!isset($_POST['custom_book_metabox_nonce']) || !wp_verify_nonce($_POST['custom_book_metabox_nonce'], 'custom_book_metabox')) {
    return;
  }

  // Check if this is an autosave
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }

  // Check the user's permissions
  if (!current_user_can('edit_post', $post_id)) {
    return;
  }

  // Save the custom field values
  if (isset($_POST['book_author'])) {
    update_post_meta($post_id, 'book_author', sanitize_text_field($_POST['book_author']));
  }
  if (isset($_POST['book_publisher'])) {
    update_post_meta($post_id, 'book_publisher', sanitize_text_field($_POST['book_publisher']));
  }
  if (isset($_POST['book_price'])) {
    update_post_meta($post_id, 'book_price', sanitize_text_field($_POST['book_price']));
  }
}
add_action('save_post', 'save_custom_book_metabox');

// Step 4: Add a nonce field to the metabox
function add_custom_book_metabox_nonce() {
  wp_nonce_field('custom_book_metabox', 'custom_book_metabox_nonce');
}
add_action('custom_book_metabox_callback', 'add_custom_book_metabox_nonce');
