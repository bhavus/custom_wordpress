

<?php
/**
 * Plugin Name
 *
 * @package           PluginPackage
 * @author            Shailesh Parmar
 * @copyright         2022 Shailesh Parmar or Munshaip Infosys
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Custom Post Type Shailesh
 * Plugin URI:        https://www.munshaip.com/custom_post_type_shailesh
 * Description:       Custom Post Type Plugin.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Shailesh Parmar
 * Author URI:        https://www.munshaip.com/
 * Text Domain:       munshaip
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Update URI:        https://example.com/my-plugin/
 */

defined( 'ABSPATH' ) or die( "No Direct Access" );



/**
 * Register a custom post type called "book".
 *
 * @see get_post_type_labels() for label keys.
 */
function munshaip_cpt_book_init() {
    $labels = array(
        'name'                  => _x( 'Books', 'Post type general name', 'munshaip' ),
        'singular_name'         => _x( 'Book', 'Post type singular name', 'munshaip' ),
        'menu_name'             => _x( 'Books', 'Admin Menu text', 'munshaip' ),
        'name_admin_bar'        => _x( 'Book', 'Add New on Toolbar', 'munshaip' ),
        'add_new'               => __( 'Add New', 'munshaip' ),
        'add_new_item'          => __( 'Add New Book', 'munshaip' ),
        'new_item'              => __( 'New Book', 'munshaip' ),
        'edit_item'             => __( 'Edit Book', 'munshaip' ),
        'view_item'             => __( 'View Book', 'munshaip' ),
        'all_items'             => __( 'All Books', 'munshaip' ),
        'search_items'          => __( 'Search Books', 'munshaip' ),
        'parent_item_colon'     => __( 'Parent Books:', 'munshaip' ),
        'not_found'             => __( 'No books found.', 'munshaip' ),
        'not_found_in_trash'    => __( 'No books found in Trash.', 'munshaip' ),
        'featured_image'        => _x( 'Book Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'munshaip' ),
        'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'munshaip' ),
        'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'munshaip' ),
        'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'munshaip' ),
        'archives'              => _x( 'Book archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'munshaip' ),
        'insert_into_item'      => _x( 'Insert into book', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'munshaip' ),
        'uploaded_to_this_item' => _x( 'Uploaded to this book', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'munshaip' ),
        'filter_items_list'     => _x( 'Filter books list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'munshaip' ),
        'items_list_navigation' => _x( 'Books list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'munshaip' ),
        'items_list'            => _x( 'Books list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'munshaip' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'book' ),
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
        'taxonomies'         => array( 'book_type', 'book_tag' ),
        'show_in_rest'       => true
    );

    register_post_type( 'book', $args );
}

add_action( 'init', 'munshaip_cpt_book_init' );

/**
 * Register a custom taxonomy called "book_type".
 *
 * @see get_post_type_labels() for label keys.
 */

function munshaip_cpt_book_taxonomies() {
    // Add new taxonomy, make it hierarchical (like categories)
    $labels = array(
        'name'              => _x('Book Type', 'taxonomy general name', 'munshaip'),
        'singular_name'     => _x('Book Type', 'taxonomy singular name', 'munshaip'),
        'search_items'      => __('Search Book Type', 'munshaip'),
        'all_items'         => __('All Book Type', 'munshaip'),
        'parent_item'       => __('Parent Book Type', 'munshaip'),
        'parent_item_colon' => __('Parent Book Type:', 'munshaip'),
        'edit_item'         => __('Edit Book Type', 'munshaip'),
        'update_item'       => __('Update Book Type', 'munshaip'),
        'add_new_item'      => __('Add New Book Type', 'munshaip'),
        'new_item_name'     => __('New Book Type Name', 'munshaip'),
        'menu_name'         => __('Book Type', 'munshaip'),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => false,
        'rewrite'           => array('slug' => 'book_type'),
    );

    register_taxonomy('book_type', array('book'), $args);
}

add_action('init', 'munshaip_cpt_book_taxonomies');


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
