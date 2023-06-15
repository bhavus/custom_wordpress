<?php

if(isset($_GET['del'])){
	global $wpdb;

	$del    = $_GET['del'];
	$tablename = 'register';
	$wpdb->delete($tablename, array('uid'=> $del));
	echo "<script>alert('Deleted Record')</script>";
}
