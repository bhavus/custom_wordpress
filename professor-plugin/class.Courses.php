<?php

require_once (ABSPATH.'/wp-admin/includes/taxonomy.php'); 

class Prof_Course{
    var $courses_meta_box;
    var $categoryID;

    function __construct(){
        $this->courses_meta_box = array(
            'id' => 'courses-meta',
            'title' => __('Course Information'),
            'page' => 'prof_courses',
            'context' => 'normal',
            'priority' => 'high',
            'fields' => array(
                array(
                    'name' => __('Meeting Times'),
                    'desc' => __('When the class meets'),
                    'id' => 'meetingtimes',
                    'type' => 'textarea',
                    'std' => ""
                ),  
               array(
                    'name' => __('Classroom'),
                    'desc' => __('Where the class meets'),
                    'id' => 'classroom',
                    'type' => 'text',
                    'std' => ""
                ),
                
                 array(
                    'name' => __('Course ID'),
                    'desc' => __('ID number of course'),
                    'id' => 'courseid',
                    'type' => 'text',
                    'std' => ""
                ),
            )
        );

        add_action('init', array($this, 'registration'));
        add_action('admin_menu', array($this, 'courses_meta'));
        add_action('save_post', array($this, 'courses_save_data'));

        $this->categoryID= wp_create_category( 'Courses' );
    }

    function registration() {  
    
    //Arguments to create post type.
    $args = array(  
        'label' => __('Courses'),  
        'singular_label' => __('Course'),  
        'public' => true,  
        'show_ui' => true,  
        'capability_type' => 'post',  
        'hierarchical' => true,  
        'has_archive' => true,
        'supports' => array('title', 'editor'),
        'rewrite' => array('slug' => 'courses', 'with_front' => false),
       );  
  
    register_post_type( 'prof_courses' , $args );   
    }  



    // Add meta box
    function courses_meta() {
        
        add_meta_box($this->courses_meta_box['id'], $this->courses_meta_box['title'], array($this, 'courses_show_meta'), $this->courses_meta_box['page'], $this->courses_meta_box['context'], $this->courses_meta_box['priority']);
    }

    // Callback function to show fields in meta box
    function courses_show_meta() {
        global $post;
        
        // Use nonce for verification
        echo '<input type="hidden" name="courses_meta_box_nonce2" value="', wp_create_nonce(basename(__FILE__)), '" />';
        
        echo '<table class="form-table">';

        foreach ($this->courses_meta_box['fields'] as $field) {
            // get current post meta data
            $meta = get_post_meta($post->ID, $field['id'], true);
            
            echo '<tr>',
                    '<th style="width:20%"><label for="', $field['id'], '">', $field['name'], '</label></th>',
                    '<td>';
            switch ($field['type']) {
                case 'text':
                    echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" />', '<br />', $field['desc'];
                    break;
                case 'textarea':
                    echo '<textarea name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="4" style="width:97%">', $meta ? $meta : $field['std'], '</textarea>', '<br />', $field['desc'];
                    break;
            }
            echo     '<td>',
                '</tr>';
        }
        
        echo '</table>';
    }

    // Save data from meta box
    function courses_save_data($post_id) {    
        // verify nonce
        if (!wp_verify_nonce($_POST['courses_meta_box_nonce2'], basename(__FILE__))) {
            return $post_id;
        }

        // check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }
        
        foreach ($this->courses_meta_box['fields'] as $field) {
            $old = get_post_meta($post_id, $field['id'], true);
            $new = $_POST[$field['id']];
            
            if ($new && $new != $old) {
                update_post_meta($post_id, $field['id'], $new);
            } elseif ('' == $new && $old) {
                delete_post_meta($post_id, $field['id'], $old);
            }
        }

        //create new category here with same name as Course.
        wp_create_category(get_the_title($post_id), $this->categoryID);
    }


    function get_course_meta($post_id){
        
    }
}

// check autosave
if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return $post_id;
}

?>