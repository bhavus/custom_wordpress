<?php
/**
	Template Name: Form For User
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other 'pages' on your WordPress site will use a different template.
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */

get_header(); ?>

<div id="main-content" class="main-content">

<?php
	if ( is_front_page() && twentyfourteen_has_featured_posts() ) {
		// Include the featured content template.
		get_template_part( 'featured-content' );
	}
?>
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
			<h1 class="headingform">User Form</h1>
			<div class="msg"></div>
			<form  class="userform">
				<p>Name</p>
				<input type="text" id="username" />
				<p>Email</p>
				<input type="email" id="useremail" />
				<p>Option</p>
				<select name="" id="useroption">
					<option value="Option One">Option One</option>
					<option value="Option Two">Option Two</option>
					<option value="Option Two">Option Three</option>
				</select>
				<textarea name="" id="usercontent" cols="30" rows="10"></textarea>
				<input  id="usersubmit"type="submit" Value="Submit" />
			</form>

		</div><!-- #content -->
	</div><!-- #primary -->
	<?php get_sidebar( 'content' ); ?>
</div><!-- #main-content -->

<?php
get_sidebar();
get_footer();
