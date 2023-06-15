<?php
/*
*Plugin Name: wp crud admin and frontend
*Description: My plugin to explain the submit custom crud from frontend functionality. [websitemaker] 
*Version: 1.0
*Author: Shailesh Parmar
*Author URI: https://xyz.com/
*/
?>
<style type="text/css">
	.formset{
		/*background-color: yellow;*/
        padding: 10px;
        margin: 40px;
	}
</style>

<?php
add_action('admin_menu','myfirstplugin');
function myfirstplugin(){
	add_menu_page(
		'websitemakerking',        //pagte title
		'CRUD SYSTEM',     //menu name
		'administrator',  //capability means which type user are use
		'amit',                    //slug name
		'websitemakerking_plugin', //calling function
		'dashicons-admin-tools',   //display icon
		'10'					   //position	
	);
}

function websitemakerking_plugin(){

	if(isset($_GET['del'])){
		include_once("delete.php");
	 }

	if(isset($_GET['id'])){
		include_once("update.php");
	 }
    else{
	?>
	<div class="formset">
		<form method="post" action="<?php echo admin_url('admin.php?page=amit'); ?>">
		  <label> Name: </label><br>
		  <input type="text" name="name" required><br><br>
		  <label> Email ID: </label><br>
		  <input type="text" name="email" required><br><br>
		  <label> Mobile No: </label><br>
		  <input type="text" name="mobile" required><br><br>
		  <input type="submit" name="save" value="Submit">
		</form>  
	</div>
	<?php

	if(isset($_POST['save'])){

		
	  $name   = $_POST['name'];
	  $email  = $_POST['email'];
	  $mobile = $_POST['mobile'];

	 
	  global $wpdb;
	    //$tablename = $wpdb->prefix.'register';
	    $dtablename = 'register'; 
	  	
	  	$wpdb->insert($dtablename, 
	  		array(
	  			'name'     => $name,
	  			'email'    => $email,
	  			'mobile'   => $mobile
	  		    )
	  	);
	  	echo "<script>alert('successfully registered')</script>";
	 }


	global $wpdb;
	
	$result = $wpdb->get_results("SELECT *FROM register");



	echo "<table border='2'>
		<tr>
		<th> UID </th>
		<th> NAME </th>
		<th> EMAIL ID </th>
		<th> MOBILE </th>
		<th> EDIT </th>
		<th> DELETE </th>
		</tr>";

	foreach ($result as $val) {
		
		$res = $val->uid;

        echo "<tr>";
        echo "<td>" .$val->uid."</td>";
        echo "<td>" .$val->name."</td>";
        echo "<td>" .$val->email."</td>";
        echo "<td>" .$val->mobile."</td>";
        ?>

        <td><a href="<?php echo admin_url('admin.php?page=amit&id='.$res); ?>">Edit </a></td>
        <td><a href="<?php echo admin_url('admin.php?page=amit&del='.$res); ?>">Delete </a></td>
        
        <?php

        echo "</tr>";
	  }
	echo "</table>";		
  } //else part of include update file
	
} //main function close



//calling shortcode

function register_shortcodes(){
	add_shortcode('websitemaker','websitemakerking_plugin');
}
add_action('init','register_shortcodes');