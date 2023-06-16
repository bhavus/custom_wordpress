<?php
/*
Plugin Name: The Professor
Plugin URI: (forthcoming)
Description: Adds assignments, courses, and office hours to a WordPress site
Author: Joe Casabona
Author URI: http://casabona.org
Version: 1.0
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

/**For Widgets**/

require_once('prof-theme-options.php');
require_once('prof-office-widget.php');
require_once('prof-contact-widget.php');


/***GET CUSTOM POST TYPES***/

require_once('class.Professor.php');
require_once('class.Courses.php');
require_once('class.Assignments.php');
require_once('class.Notes.php');

$prof= new Professor();
$prof_courses= new Prof_Course();
$prof_assignments= new Prof_Assignment();
$prof_notes= new Prof_Notes();

function prof_get_assns(){
	global $prof;
	return $prof->get_all_assignments();
}

function prof_is_assn($type){
	global $prof_assignments;
	return $prof_assignments->is_assn($type);
}

function prof_get_course_meta($course_id){
	global $prof;
	$course= $prof->get_course_by_id($course_id);
	return $course->meta;
}

function prof_get_course_assignments($course_id){
	global $prof;
	return $prof->get_course_assignments($course_id);
}

function prof_get_course_notes($course_id){
	global $prof;
	return $prof->get_course_notes($course_id);
}

function prof_print_assns($assns){
//lkjh
}

function prof_convert_date($date){
	return date('M. d', strtotime($date));
}

function prof_get_nice_name($type){
	return (strpos($type, 'prof') !== false) ? substr($type, 5) : $type;
}

?>
