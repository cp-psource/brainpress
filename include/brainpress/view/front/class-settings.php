<?php

class BrainPress_View_Front_Settings {

	public static function init() {

		add_action( 'parse_request', array( __CLASS__, 'parse_request' ) );

	}

	public static function render_dashboard_page() {

		ob_start();
			BrainPress_View_Front_Student::render_student_settings_page();
		$content = ob_get_contents();
		ob_end_clean();
		return $content;

	}

	public static function parse_request( &$wp ) {
		$check = BrainPress_Helper_Front::check_and_redirect( 'student_settings' );
		if ( ! $check ) {
			return;
		}
		$content = '';
		$page_title = __( 'Mein Profil', 'brainpress' );
		$args = array(
			'slug' => BrainPress_Core::get_slug( 'student_settings' ),
			'title' => esc_html( $page_title ),
			'content' => ! empty( $content ) ? esc_html( $content ) : self::render_dashboard_page(),
			'type' => 'brainpress_student_settings',
		);
		$pg = new BrainPress_Data_VirtualPage( $args );
	}
}
