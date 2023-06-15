<?php 

// Upload file
if(isset($_POST['but_submit'])){

	if($_FILES['file']['name'] != ''){
		$uploadedfile = $_FILES['file'];
        $upload_overrides = array( 'test_form' => false );

        $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
        $imageurl = "";
        if ( $movefile && ! isset( $movefile['error'] ) ) {
           	$imageurl  = $movefile['url'];
            echo "url : ".$imageurl;
        } else {
            echo $movefile['error'];
        }
	}
	
}

?>
<h1>Upload File</h1>

<!-- Form -->
<form method='post' action='' name='myform' enctype='multipart/form-data'>
	<table>
		<tr>
			<td>Upload file</td>
			<td><input type='file' name='file'></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type='submit' name='but_submit' value='Submit'></td>
		</tr>
	</table>
</form>