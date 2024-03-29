<?php
/**
 * Course Calendar Widget
 *
 * @package ClassicPress
 * @subpackage BrainPress
 **/
class BrainPress_Widget_Calendar extends WP_Widget {

	public static function init() {
		add_action( 'widgets_init', array( 'BrainPress_Widget_Calendar', 'register' ) );
	}

	public static function register() {
		register_widget( 'BrainPress_Widget_Calendar' );
	}

	public function __construct() {
		$widget_ops = array(
			'classname' => 'cp_course_calendar_widget',
			'description' => __( 'Zeigt den Kurskalender an.', 'brainpress' ),
		);

		$this->date_indicator = array(
			'indicator_light_block' => __( 'Helles Theme - Block', 'brainpress' ),
			'indicator_light_line' => __( 'Helles Theme - Line', 'brainpress' ),
			'indicator_dark_block' => __( 'Dunkles Theme - Block', 'brainpress' ),
			'indicator_dark_line' => __( 'Dunkles Theme - Line', 'brainpress' ),
			'indicator_none' => __( 'Theme/Benutzerdefinierte CSS', 'brainpress' ),
		);

		parent::__construct( 'CP_Course_Calendar', __( 'Kurskalender', 'brainpress' ), $widget_ops );
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array(
			'title' => '',
			'pre_text' => null,
			'next_text' => null,
			'course' => 'false',
			'indicator' => 'indicator_light_block',
		) );
		$title = $instance['title'];
		$pre_text = $instance['pre_text'];
		$next_text = $instance['next_text'];
		$selected_course = $instance['course'];
		$selected_indicator = $instance['indicator'];

		$args = array(
			'posts_per_page' => - 1,
			'post_type' => 'course',
			'post_status' => 'publish',
		);

		$courses = get_posts( $args );
		?>

		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Titel', 'brainpress' ); ?>:
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>"/></label>
		</p>

		<p><label for="<?php echo $this->get_field_id( 'course' ); ?>"><?php _e( 'Kurs', 'brainpress' ); ?><br/>
			<select name="<?php echo $this->get_field_name( 'course' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'course' ); ?>">
				<option value="false" <?php selected( $selected_course, 'false', true ); ?>><?php _e( '- momentaner -', 'brainpress' ); ?></option>
					<?php
					foreach ( $courses as $course ) {
						?>
						<option value="<?php echo $course->ID; ?>" <?php selected( $selected_course, $course->ID, true ); ?>><?php echo $course->post_title; ?></option>
					<?php
					}
					?>
				</select>
			</label>
		</p>

		<p><label for="<?php echo $this->get_field_id( 'pre_text' ); ?>"><?php _e( 'Text für den vorherigen Monat:', 'brainpress' ); ?>:
				<input class="widefat" id="<?php echo $this->get_field_id( 'pre_text' ); ?>" name="<?php echo $this->get_field_name( 'pre_text' ); ?>" type="text" value="<?php echo( ! isset( $pre_text ) ? __( '&laquo; Bisherige', 'brainpress' ) : esc_attr( $pre_text ) ); ?>"/></label>
		</p>
		<p><label for="<?php echo $this->get_field_id( 'next_text' ); ?>"><?php _e( 'Text für den nächsten Monat:', 'brainpress' ); ?>:
				<input class="widefat" id="<?php echo $this->get_field_id( 'next_text' ); ?>" name="<?php echo $this->get_field_name( 'next_text' ); ?>" type="text" value="<?php echo( ! isset( $next_text ) ? __( 'Nächste &raquo;', 'brainpress' ) : esc_attr( $next_text ) ); ?>"/></label>
		</p>

		<p><label for="<?php echo $this->get_field_id( 'indicator' ); ?>"><?php _e( 'Datumsanzeigen', 'brainpress' ); ?><br/>
				<select name="<?php echo $this->get_field_name( 'indicator' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'indicator' ); ?>">
					<?php
					foreach ( $this->date_indicator as $key => $value ) {
						?>
						<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $selected_indicator, $key, true ); ?>><?php echo $value; ?></option>
					<?php
					}
					?>
				</select>
			</label>
		</p>

	<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		// Admin on single sites, Super admin on network
		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance['title'] = $new_instance['title'];
			$instance['pre_text'] = $new_instance['pre_text'];
			$instance['next_text'] = $new_instance['next_text'];
		} else {
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['pre_text'] = strip_tags( $new_instance['pre_text'] );
			$instance['next_text'] = strip_tags( $new_instance['next_text'] );
		}

		$instance['indicator'] = $new_instance['indicator'];
		$instance['course'] = $new_instance['course'];

		return $instance;
	}

	public function widget( $args, $instance ) {
		global $post;

		extract( $args, EXTR_SKIP );

		$course_id = $instance['course'];

		//if ( ( $post && ( 'course' == $post->post_type || 'unit' == $post->post_type ) && ! is_post_type_archive( 'course' ) ) || 'false' != $instance['course'] ) {
		if ( ! empty( $course_id ) ) {
			echo $before_widget;

			$title = empty( $instance['title'] ) ? ' ' : apply_filters( 'widget_title', $instance['title'] );

			if ( ! empty( $title ) ) {
				echo $before_title . $title . $after_title;
			}

			if ( $course_id && 'false' != $course_id ) {
				echo do_shortcode( '[course_calendar course_id="' . $course_id . '" date_indicator="' . $instance['indicator'] . '" pre="' . esc_attr( $instance['pre_text'] ) . '" next="' . esc_attr( $instance['next_text'] ) . '"]' );
			} else {
				echo do_shortcode( '[course_calendar date_indicator="' . $instance['indicator'] . '" pre="' . esc_attr( $instance['pre_text'] ) . '" next="' . esc_attr( $instance['next_text'] ) . '"]' );
			}

			echo $after_widget;
		}
	}
}
