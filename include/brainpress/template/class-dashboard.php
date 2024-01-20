<?php

class BrainPress_Template_Dashboard {

	public static function render_dashboard_page() {
		return do_shortcode( '[brainpress_dashboard]' );
	}

	public static function render_login_page() {
		return do_shortcode( '[course_signup page="login"]' );
	}

	public static function render_signup_page() {
		return do_shortcode( '[course_signup page="signup"]' );
	}
}