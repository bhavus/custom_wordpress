<?php

/** Create the Custom Post Type**/
  
class Prof_Assignment{
 
    var $meta_box;
    var $slug;

    function __construct(){
        $this->slug= 'prof_assignments';

        $this->meta_box = array(
            'id' => 'assignments-meta',
            'title' => __('Assignment Information'),
            'page' => $this->slug,
            'context' => 'normal',
            'priority' => 'high',
            'fields' => array(
                array(
                    'name' => __('Due Date'),
                    'desc' => __('When is it due?'),
                    'id' => 'duedate',
                    'type' => 'date',
                    'std' => ""
                ),  
               array(
                    'name'  => __('Course'),
                    'desc'  => __('Select the course.'),
                    'id'    => 'course',
                    'type'  => 'post_list',
                    'std' => ""
                ),
            )
        );
    
        add_action('init', array($this, 'registration'));
        add_action('admin_menu', array($this, 'meta'));
        add_action('save_post', array($this, 'save_data'));
    }
    
    function registration() { 
    //Arguments to create post type.
    $args = array(  
        'label' => __('Assignments'),  
        'singular_label' => __('Assignment'),  
        'public' => true,  
        'show_ui' => true,  
        'capability_type' => 'post',  
        'hierarchical' => true,  
        'has_archive' => true,
        'supports' => array('title', 'editor', 'comments'),
        'rewrite' => array('slug' => 'assignments', 'with_front' => false),
       );  
  
  	//Register type and custom taxonomy for type.
    register_post_type( $this->slug , $args );   
    }  
     


    // Add meta box
    function meta() {

        add_meta_box($this->meta_box['id'], $this->meta_box['title'], array($this, 'show_meta'), $this->meta_box['page'], $this->meta_box['context'], $this->meta_box['priority']);
    }

    // Callback function to show fields in meta box
    function show_meta() {
        global $post;
        
        // Use nonce for verification
        echo '<input type="hidden" name="assignments_meta_box_nonce2" value="', wp_create_nonce(basename(__FILE__)), '" />';
        
        echo '<table class="form-table">';

        foreach ($this->meta_box['fields'] as $field) {
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
                case 'date':
                    echo '<input type="date" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" />', '<br />', $field['desc'];
                    break;
                case 'post_list':  
    			$items = get_posts( array (  
    				'post_type' => 'prof_courses',  
    				'posts_per_page' => -1  
    			));  
    				echo '<select name="', $field['id'],'" id="'.$field['id'],'"> 
    						<option value="">Select One</option>'; // Select One  
    					foreach($items as $item) {  
    						echo '<option value="'.$item->ID.'"', $meta == $item->ID ? ' selected' : '','> '.$item->post_title.'</option>';  
    					} // end foreach  
    				echo '</select><br />'.$field['desc'];  
    			break; 
            }
            echo     '<td>',
                '</tr>';
        }
        
        echo '</table>';
?>
        <script>
           (function() {
              var elem = document.createElement('input');
              elem.setAttribute('type', 'date');
         
              if ( elem.type === 'text' ) {
                 $('#duedate').datepicker(); 
              }
           })();
        </script>

<?php
    }

    // get current post meta data

    // Save data from meta box
    function save_data($post_id) {
       
        // verify nonce
        if (!wp_verify_nonce($_POST['assignments_meta_box_nonce2'], basename(__FILE__))) {
            return $post_id;
        }

        // check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }
        
        foreach ($this->meta_box['fields'] as $field) {
            $old = get_post_meta($post_id, $field['id'], true);
            $new = $_POST[$field['id']];
            
            if ($new && $new != $old) {
                update_post_meta($post_id, $field['id'], $new);
            } elseif ('' == $new && $old) {
                delete_post_meta($post_id, $field['id'], $old);
            }
        }
    }

    //**Usful Stuff Here**/
    function get_nice_name(){
        return "Assignments";
    }

    function is_assn($type){
        return $type == $this->slug;
    }


}

// check autosave
if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
 return $post_id;
}




?>