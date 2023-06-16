<?php 
// create custom plugin settings menu
add_action('admin_menu', 'prof_create_menu');

function prof_create_menu() {

	//create new submenu
	add_submenu_page( 'themes.php', 'Prof Theme Options', 'Professor Theme Options', 'administrator', __FILE__, 'prof_settings_page');

	//call register settings function
	add_action( 'admin_init', 'prof_register_settings' );
}

function prof_register_settings() {
	//register our settings
  register_setting( 'prof-settings-group', 'prof_name' );
	register_setting( 'prof-settings-group', 'prof_office' );
	register_setting( 'prof-settings-group', 'prof_twitter' );
	register_setting( 'prof-settings-group', 'prof_email' );
	register_setting( 'prof-settings-group', 'prof_phone' );
  register_setting( 'prof-settings-group', 'prof_semester');
  register_setting( 'prof-settings-group', 'prof_semester_start');
  register_setting( 'prof-settings-group', 'prof_semester_end');
}

function prof_settings_page() {
  $prof_name= (get_option('prof_name') == '') ? get_bloginfo('name') :  get_option('prof_name');
?>

<div class="wrap">
<h2>Theme Settings</h2>

<form id="landingOptions" method="post" action="options.php">
    <?php settings_fields( 'prof-settings-group' ); ?>
    <table class="form-table">
         <tr valign="top">
        <th scope="row">Name:</th>
        <td>
          <input type="text" name="prof_name" value="<?php print $prof_name; ?>" />
        </td>
        </tr>
        <tr valign="top">
        <th scope="row">Office:</th>
        <td>
       		<input type="text" name="prof_office" value="<?php print get_option('prof_office'); ?>" />
       	</td>
        </tr>
          <tr valign="top">
        <th scope="row">Email:</th>
        <td>
       		<input type="text" name="prof_email" value="<?php print get_option('prof_email'); ?>" />
       	</td>
		</tr>
		<tr>
		<th scope="row">Phone:</th>
        <td>
       		<input type="text" name="prof_phone" value="<?php print get_option('prof_phone'); ?>" />
       	</td>
        </tr>
       <tr>
		<th scope="row">Twitter:</th>
        <td>
       		<input type="text" name="prof_twitter" value="<?php print get_option('prof_twitter'); ?>" />
       	</td>
        </tr>  
    <th scope="row">Current Semester:</th>
        <td>
          <input type="text" name="prof_semester" value="<?php print get_option('prof_semester'); ?>" />
        </td>
        </tr> 
      <th scope="row">Semester Start Date:</th>
        <td>
          <input type="text" name="prof_semester_start" value="<?php print get_option('prof_semester_start'); ?>" placehoder="YYYY-MM-DD" />
        </td>
        </tr>  
       <th scope="row">Semester End Date:</th>
        <td>
          <input type="text" name="prof_semester_end" value="<?php print get_option('prof_semester_end'); ?>" placehoder="YYYY-MM-DD" />
        </td>
        </tr>  
    </table>
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>
</div>
<?php } ?>
