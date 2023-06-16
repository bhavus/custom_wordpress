<?php
/*
Plugin Name: Custom logo upload admin side
Plugin URI: http://www.xyz.com
Description: This plugin custom log upload opttion in wordpress admin dashboard panel
Author: Shailesh Parmar
Version: 1.0
Author URI: http://www.xyz.com/
*/

//Add Menu in admin dashboard
function add_theme_menu_item(){
	add_menu_page("Theme Options", "Theme Options", "manage_options", "theme-panel", "theme_settings_page", null, 99);
 }
// Add Html code to that menu
add_action("admin_menu", "add_theme_menu_item");

function theme_settings_page(){
?>
	<div class="wrap">
	<h1>Theme Options</h1>
	<form method="post" action="options.php" enctype="multipart/form-data">
		<?php
		settings_fields("section");
		do_settings_sections("theme-options");
		submit_button();
		?>
	</form>
	</div>
<?php
}
function logo_display(){
 ?>
	<input type="file" name="logo" /> 
	<?php if(get_option('logo') != ""){ ?>
	   <?php echo "<img style='width:150px; height:150px;display:block;' src='".get_option('logo')."'>";
	 }
}
//Upload Logo
function handle_logo_upload(){

	global $option;
	if($_FILES["logo"]["tmp_name"])
	{
		$urls = wp_handle_upload($_FILES["logo"], array('test_form' => FALSE));
		$temp = $urls["url"];
		return $temp;
	}
	return $option;
}
function display_theme_panel_fields(){

	add_settings_section("section", "Logo Settings", null, "theme-options");

	add_settings_field("logo", "Add Logo", "logo_display", "theme-options", "section");
	register_setting("section", "logo", "handle_logo_upload");
}
add_action("admin_init", "display_theme_panel_fields");

echo "<img style='width:150px; height:150px;display:block;' src='".get_option('logo')."'>";
