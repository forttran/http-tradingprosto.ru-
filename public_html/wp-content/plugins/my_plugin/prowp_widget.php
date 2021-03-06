<?php
class prowp_widget extends WP_Widget{

	public function __construct() {
		parent::__construct("text_widget", "Simple Text Widget",
				array("description" => "A simple widget to show how WP Plugins work"));
	}
	
	function form( $instance ) {
		$defaults = array(
				'title' => 'My Bio',
				'name' => 'Michael Myers',
				'bio' => '' );
		$instance = wp_parse_args( (array) $instance, $defaults );
		$title = $instance['title'];
		$name = $instance['name'];
		$bio = $instance['bio'];
		?>
			<p>Title:
			<input class="widefat"
			name="<?php echo $this->get_field_name( 'title' ); ?>"
			type="text" value="<?php echo esc_attr( $title ); ?>" /></p>
			<p>Name:
			<input class="widefat"
			name="<?php echo $this->get_field_name( 'name' ); ?>"
			type="text" value="<?php echo esc_attr( $name ); ?>" /></p>
			<p>Bio:
			<textarea class="widefat"
			name="<?php echo $this->get_field_name( 'bio' ); ?>" >
			<?php echo esc_textarea( $bio ); ?></textarea><p>
			<?php
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['name'] = sanitize_text_field( $new_instance['name'] );
		$instance['bio'] = sanitize_text_field( $new_instance['bio'] );
		return $instance;
	}
	function widget( $args, $instance ) {
		extract( $args );
		echo $before_widget;
		$title = apply_filters( 'widget_title', $instance['title'] );
		$name = ( empty( $instance['name'] ) ) ? '&nbsp;' : $instance['name'];
		$bio = ( empty( $instance['bio'] ) ) ? 'Snbsp;' : $instance['bio'];
		if(!empty($title)){ 
			echo $before_title.esc_html($title).$after_title;
		};
		echo '<p>Name: ' . esc_html( $name ) . '</p>';
		echo '<p>Bio: ' . esc_html( $bio ) . '</p>';
		echo $after_widget;
	}
}

?>
