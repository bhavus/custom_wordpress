<?php

class Professor{
	
	
	
	/** Getters **/

	function get_all_courses(){
		$args= array(
			'post_type' => 'prof_courses'
		);
		
		$courses= get_posts($args);	
	}

	function get_course_by_id($course_id){
		$course= get_post($course_id);
		$course_meta= $this->fix_meta(get_post_custom($course_id));
		$course->meta= $course_meta;
		return $course;
	}

	function get_all_assignments(){
		
		$args= array(
			'post_type' => 'assignments'
		);
		
		$assns= get_posts($args);
		
		return $assns;
	}

	function get_course_assignments($course_id){
	
	$args= array(
		'post_type' => 'prof_assignments',
		'meta_query' => array(
			array(
				'key' => 'course',
				'value' => $course_id,
			),
		'order_by' => 'meta_value',
		'meta_key' => 'duedate',
		'meta_value' => date('Y-m-d'),
		'meta_compare' => '>=',
		'order' => ASC
		)
	);
	
	$assns= new WP_Query($args);
	
	return $assns;
	
	}

	function get_course_notes($course_id){
	
	$args= array(
		'post_type' => 'prof_notes',
		'meta_query' => array(
			array(
				'key' => 'course',
				'value' => $course_id,
			),
		)
	);
	
	$notes= new WP_Query($args);
	
	return $notes;
	
	}

	function fix_meta($meta){
		$new_meta= array();
		foreach($meta as $key => $value){
			$new_meta[$key]= $value[0];
		}

		return $new_meta;
	}

	function treat_cid($cid){
		return strtolower(str_ireplace(" ", "-", $cid));
		}


	/**Some Stuff for Printing Things**/
	function prof_print_assn_info($pid){ //probably don't have a function for this.
	?>
		<h3>Assignment Information</h3>
				<?php $assn_info = get_post_custom($pid); ?>
				<dl>
					<dt>Due Date</dt>
						<dd><?php print $assn_info['duedate'][0]; ?></dd>
						
					<dt>Course</dt>
						<dd><a href="<?php print get_permalink($assn_info['course'][0]); ?>"><?php print get_the_title($assn_info['course'][0]); ?></a></dd>
				</dl>
	<?php
	}



/**Accepts 1 argument: $post-ID**/
function prof_print_assn_files($assn_id){
	$args = array(
		'post_type' => 'attachment',
		'numberposts' => null,
		'post_status' => null,
		'post_parent' => $assn_id
	);
	 
	$attachments = get_posts($args);
	if ($attachments) {
		print "<ul>";
		foreach ($attachments as $attachment) {
			if(strpos(strtolower($attachment->post_mime_type), 'image') === false){
				print "<li><a href=\"". wp_get_attachment_url($attachment->ID) ."\">{$attachment->post_title}</a></li>";
			}
		}
		print "</ul>";
	}
}



}

?>