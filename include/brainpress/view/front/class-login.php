<?php

class BrainPress_View_Front_Login {

	public static $title = ''; // The page title

	public static function init() {
		add_action( 'parse_request', array( __CLASS__, 'parse_request' ) );
		add_action( 'wp_login', array( __CLASS__, 'log_student_activity_login' ), 10, 2 );
	}

	/**
	 * @todo: Why is this commented? Find out and finish function is needed!
	 */
	public static function render_login_page() {
		$content = BrainPress_Template_Dashboard::render_login_page();
		return $content;
	}


	public static function parse_request( &$wp ) {
			$check = BrainPress_Helper_Front::check_and_redirect( 'login', false );
		if ( ! $check ) {
			return;
		}
			$content = '';
			$page_title = __( 'Student Login', 'brainpress' );
			$args = array(
				'slug' => BrainPress_Core::get_slug( 'login' ),
				'title' => esc_html( $page_title ),
				'content' => ! empty( $content ) ? esc_html( $content ) : self::render_login_page(),
				'type' => 'brainpress_student_login',
			);
			$pg = new BrainPress_Data_VirtualPage( $args );
	}

	public static function render_student_login_page() {

		if ( is_user_logged_in() ) {
			_e( 'Du bist bereits eingeloggt.', 'brainpress' );
			return;
		}

		$redirect_url = '';
		if ( ! empty( $_REQUEST['redirect_url'] ) ) {
			$redirect_url = $_REQUEST['redirect_url'];
		}
		echo do_shortcode(
			sprintf(
				'[course_signup page="login" login_title="" redirect_url="%s" signup_url="%s" login_url="%s"]',
				$redirect_url,
				BrainPress_Core::get_slug( 'signup', true ),
				cp_student_login_address()
			)
		);

	}

	/**
	 * Save student activity - login
	 *
	 * @since 2.0.0
	 */
	public static function log_student_activity_login( $user_login, $user ) {
		BrainPress_Data_Student::log_student_activity( 'login', $user->ID );
	}
}
