<?php

$id = $_GET['id'];

update_form1($id);

function update_form1($id){

	global $wpdb;

	$result = $wpdb->get_results("SELECT * FROM register WHERE uid= $id");

	foreach ($result as $val) {

		$name   = $val->name;
		$email  = $val->email;
		$mobile = $val->mobile;
	}

    ?>

	<form method="post" >
	  <label> Name: </label><br>
	  <input type="text" name="name" value="<?php echo esc_attr($name); ?>"><br><br>
	  <label> Email ID: </label><br>
	  <input type="text" name="email" value="<?php echo esc_url($email); ?>"><br><br>
	  <label> Mobile No: </label><br>
	  <input type="text" name="mobile" value="<?php echo esc_attr($mobile); ?>"><br><br>
	  <input type="submit" name="updatevalue" value="Update Record">
	</form>  
	<?php

	if(isset($_POST['updatevalue']))
	 {
	 	global $wpdb;
	 	$tablename = "register";

	 	$name = $_POST['name'];
	 	$email = $_POST['email'];
	 	$mobile = $_POST['mobile'];

	 	$update = $wpdb->update($tablename, array('name'=>$name,'email'=>$email,'mobile'=>$mobile),array('uid'=>$id));
        
        echo "your data update sucessfully...!";

	 }
}