<?php
class Prof_Contact_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
	 		'prof_contact_widget', // Base ID
			'Professor Contact Widget', // Name
			array( 'description' => __( 'Information pulled from theme options page.')) // Args
		);
	}
	
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	public function form( $instance ) {
		$title = (isset( $instance[ 'title' ])) ? $instance[ 'title' ] : get_option('prof_name');

	?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<p>** Make sure you add this information on the Professor Theme Options page!</p>
	<?php 
	}


	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		if ( ! empty( $title ) ) echo $before_title . $title . $after_title;

		$office= get_option('prof_office');
		$email= get_option('prof_email');
		$phone= get_option('prof_phone');
		$twitter= get_option('prof_twitter');
?>
	<div class="prof-contact-widget group">
		<div class="prof-photo">
			<?php echo get_avatar( $email, $size = '250') ?>
		</div>

		<ul class="prof-info">
			<li>Office: <?php print $office; ?> </li>
			<li>Email Address: <?php print $email; ?> </li>
			<li>Phone Number: <a href="tel:<?php print $phone; ?>"><?php print $phone; ?></a></li>
			<li>Twitter: <a href="http://www.twitter.com/<?php print $twitter; ?>">@<?php print $twitter; ?></a></li>
		</ul>
	</div>

<?php
		echo $after_widget; 
	}

}

add_action('widgets_init', 'prof_register_contact_widget');
function prof_register_contact_widget() {
    register_widget('Prof_Contact_Widget');
}

?>