<?php
/*
Plugin Name: npp
Description: Custom plugin for importing complete product data with attributes, variance, size, color, product images, and price using a CSV file in WooCommerce.
Version: 1.0
Author: Your Name
Author URI: Your Website
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

global $woocommerce;

// Add the admin menu
function woocommerce_custom_import_menu() {
    add_menu_page(
        'Custom Import',
        'Custom Import',
        'manage_options',
        'woocommerce-custom-import',
        'woocommerce_custom_import_page',
        'dashicons-upload',
        30
    );
}
add_action('admin_menu', 'woocommerce_custom_import_menu');

// Display the admin menu page
function woocommerce_custom_import_page() {
    ?>
    <div class="wrap">
        <h1>Custom Import</h1>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="csv_file" />
            <?php wp_nonce_field('woocommerce_custom_import', 'woocommerce_custom_import_nonce'); ?>
            <input type="submit" class="button button-primary" value="Import" />
        </form>
    </div>
    <?php
}

// Process the CSV file and import the products
function woocommerce_custom_import_process() {
    if (isset($_FILES['csv_file'])) {
        $file = $_FILES['csv_file'];

        // Check if the file is a CSV
        if (strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) !== 'csv') {
            wp_die('Invalid file format. Please upload a CSV file.');
        }

        // Verify the nonce
        if (!isset($_POST['woocommerce_custom_import_nonce']) || !wp_verify_nonce($_POST['woocommerce_custom_import_nonce'], 'woocommerce_custom_import')) {
            wp_die('Invalid security check.');
        }

        // Process the CSV file and import the products
        $handle = fopen($file['tmp_name'], 'r');
        if ($handle !== false) {
            global $woocommerce, $product, $post;
            while (($data = fgetcsv($handle, 0, ',')) !== false) {
                // Process each row of the CSV file and create/update the products

                // Get the product SKU from the CSV column 1
                $product_sku = $data[0];

                // Check if the product exists
                $product_id = wc_get_product_id_by_sku($product_sku);

              

                if ($product_id) {

                     // Product already exists, skip the row
                    continue;

                    // Product already exists, update its data

                    // Set the product title from the CSV column 2
                    $product_title = $data[1];
                    wp_update_post(array(
                        'ID'         => $product_id,
                        'post_title' => $product_title,
                    ));

                    // Update the product price from the CSV column 3
                    $product_price = $data[2];
                    update_post_meta($product_id, '_regular_price', $product_price);
                    update_post_meta($product_id, '_price', $product_price);

                    // Update product meta as needed
                    // ...

                    // Update variations if needed
                    // ...

                    // Update product attributes
                    $product_attributes = array();
                    for ($i = 3; $i < count($data); $i += 2) {
                        $attribute_name = $data[$i];
                        $attribute_value = $data[$i + 1];
                        $product_attributes[sanitize_title($attribute_name)] = array(
                            'name'         => $attribute_name,
                            'value'        => $attribute_value,
                            'is_visible'   => 1,
                            'is_taxonomy'  => 0,
                            'is_variation' => 0,
                        );
                    }
                    update_post_meta($product_id, '_product_attributes', $product_attributes);
                } else {
                    // Product does not exist, create a new product

                    // Set the product title from the CSV column 2
                    $product_title = $data[1];

                    // Set the product price from the CSV column 3
                    $product_price = $data[2];

                    // Create the product
                    $new_product = array(
                        'post_title'   => $product_title,
                        'post_status'  => 'publish',
                        'post_type'    => 'product',
                        'post_content' => '',
                    );

                    // Insert the product into the database
                    $product_id = wp_insert_post($new_product);

                    $new_createproduct = wcsv_create_product();

                    // Set the product SKU
                    update_post_meta($product_id, '_sku', $product_sku);

                    // Set the product price
                    update_post_meta($product_id, '_regular_price', $product_price);
                    update_post_meta($product_id, '_price', $product_price);

                    // Create attribute sets if needed
                    $attributes = array(
                    'size' => array(
                        'name'         => 'Size',
                        'value'        => $data[2], // Set the size from the CSV column 3
                        'position'     => 0,
                        'is_visible'   => 1,
                        'is_variation' => 1,
                        'is_taxonomy'  => 0,
                    ),
                    'color' => array(
                        'name'         => 'Color',
                        'value'        => $data[3], // Set the color from the CSV column 4
                        'position'     => 1,
                        'is_visible'   => 1,
                        'is_variation' => 1,
                        'is_taxonomy'  => 0,
                        ),
                    );

                    //  foreach ($attributes as $attribute) {
                    //     $attribute_id = wc_create_attribute($attribute['name']);
                    //     $new_product->set_attribute($attribute_id, $attribute['value']);
                    // }

                    $new_createatts = wcsv_create_attributes($product_id, $attributes);

                    // Set the product variations
                    $variations = array(
                        'size' => array(
                            'name'         => 'Size',
                            'options'      => explode(',', $data[2]), // Set the size options from the CSV column 3
                        ),
                        'color' => array(
                            'name'         => 'Color',
                            'options'      => explode(',', $data[3]), // Set the color options from the CSV column 4
                        ),
                    );

                    // foreach ($variations as $variation) {
                    //     $variation_id = wc_create_product_variation($product_id, array(
                    //         'attributes' => $variation,
                    //         'sku'        => $product_sku . '-' . $variation['options'][0], // Set the variation SKU
                    //     ));
                    // }


                    $new_createvariation = wcsv_create_variations($product_id, $variations);                 
                        spppp();


                    // Create variations if needed
                    $new_product = new WC_Product_Variable();
                    $new_product->set_name($data[1]); // Set the product name from the CSV column 2
                    $new_product->set_sku($product_sku); // Set the product SKU
                    $new_product->set_price($data[5]); // Set the price from the CSV column 6
                    $new_product->set_regular_price($data[5]); // Set the regular price

                    $new_product->set_status('publish');
                    $new_product->set_catalog_visibility('visible');
                    $new_product->set_manage_stock(false); // Set to true if you want to manage stock

          

                    // Update product attributes
                    $product_attributes = array();
                    for ($i = 3; $i < count($data); $i += 2) {
                        $attribute_name = $data[$i];
                        $attribute_value = $data[$i + 1];
                        $product_attributes[sanitize_title($attribute_name)] = array(
                            'name'         => $attribute_name,
                            'value'        => $attribute_value,
                            'is_visible'   => 1,
                            'is_taxonomy'  => 0,
                            'is_variation' => 0,
                        );
                    }
                    update_post_meta($product_id, '_product_attributes', $product_attributes);
                }
            }

            

            fclose($handle);
           
            wp_safe_redirect(admin_url(). 'admin.php?page=woocommerce-custom-import');
        } else {
            wp_die('Error opening the CSV file.');
        }
    }
}
add_action('admin_init', 'woocommerce_custom_import_process');

function spppp(){

    echo "shailesh Parmar parsotambhaidfsd";

}


function wcsv_create_product(){
    $product = new WC_Product_Variable();
    $product->set_description('T-shirt variable description');
    $product->set_name('T-shirt variable');
    $product->set_sku('test-shirt');
    $product->set_price(1);
    $product->set_regular_price(1);
    $product->set_stock_status();
    return $product->save();
}

function wcsv_create_attributes( $name, $options ){
    $attribute = new WC_Product_Attribute();
    $attribute->set_id(0);
    $attribute->set_name($name);
    $attribute->set_options($options);
    $attribute->set_visible(true);
    $attribute->set_variation(true);
    return $attribute;
}


function wcsv_create_variations( $product_id, $values ){
    $variation = new WC_Product_Variation();
    $variation->set_parent_id( $product_id );
    $variation->set_attributes($values);
    $variation->set_status('publish');
    $variation->set_sku($data->sku);
    $variation->set_price($data->price);
    $variation->set_regular_price($data->price);
    $variation->set_stock_status();
    $variation->save();
    $product = wc_get_product($product_id);
    $product->save();

}
