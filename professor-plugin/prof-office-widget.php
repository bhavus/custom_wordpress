<?php
class Prof_Office_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
	 		'prof_office_widget', // Base ID
			'Professor Office Hourse Widget', // Name
			array( 'description' => __( 'Fill in Office Hours Info.')) // Args
		);
	}
	
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['office_hours'] = strip_tags( $new_instance['office_hours'] );
		$instance['show_scheduler'] = strip_tags( $new_instance['show_scheduler'] );

		return $instance;
	}

	public function form( $instance ) {
		$title = (isset( $instance[ 'title' ])) ? $instance[ 'title' ] : 'Contact Information';
		$office_hours = (isset( $instance[ 'office_hours' ])) ? $instance[ 'office_hours' ] : '';
		$show_scheduler = (isset( $instance[ 'show_scheduler' ])) ? $instance[ 'show_scheduler' ] : true;
		

	?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'office_hours' ); ?>"><?php _e( 'Office Hours:' ); ?></label> 
		<textarea class="widefat" id="<?php echo $this->get_field_id( 'office_hours' ); ?>" name="<?php echo $this->get_field_name( 'office_hours' ); ?>"><?php echo esc_attr($office_hours ); ?>
		</textarea>
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'show_scheduler' ); ?>"><?php _e( 'Show Scheduler:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'show_scheduler' ); ?>" name="<?php echo $this->get_field_name( 'show_scheduler' ); ?>" type="checkbox" <?php if ($show_scheduler){ print 'checked="checked"'; } ?> />
		</p>
	<?php 
	}


	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		$office_hours = apply_filters( 'widget_title', $instance['office_hours'] );
		$show_scheduler = apply_filters( 'widget_title', $instance['show_scheduler'] );

		echo $before_widget;
		if ( ! empty( $title ) ) echo $before_title . $title . $after_title;

		print "<p>$office_hours</p>";
				
		if($show_scheduler){ print '<a class="button" href="#">Schedule a Meeting</a>'; }
?>
		
<?php
		echo $after_widget; 
	}

}

add_action('widgets_init', 'prof_register_office_widget');
function prof_register_office_widget() {
    register_widget('Prof_Office_Widget');
}

?>