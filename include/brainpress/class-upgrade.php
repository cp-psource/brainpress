<?php

class BrainPress_Upgrade {

	private static $map = array(
		'2.0' => '_2p0',
	);

	public static function init() {
		// If this setting does not exist, then default to last 1.0 release
		$last_version = BrainPress_Core::get_network_setting( 'general/version', '1.2.5.8' );

		$last_version = '1.2.5.8'; // DEBUG VALUE, TO ALWAYS RUN THIS SCRIPT!

		foreach ( self::$map as $v => $f ) {
			if ( version_compare( $last_version, $v ) < 0 ) {
				call_user_func( __CLASS__ . '::' . $f );
			}
		}
	}


	private static function _2p0() {
		/**
		 * Upgrade blog options
		 *
		 * Store settings in one key rather than all over the options in the table
		 */
		// delete_option( 'brainpress_settings' );
		$settings = get_option( 'brainpress_settings', array() );

		// General Meta
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'general/show_brainpress_menu', get_option( 'display_menu_items', 1 ) );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'general/use_custom_login', get_option( 'use_custom_login_form', 1 ) );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'general/redirect_after_login', get_option( 'redirect_students_to_dashboard', 1 ) );

		// Slugs
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'slugs/course', get_option( 'brainpress_course_slug', 'courses' ) );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'slugs/category', get_option( 'brainpress_course_category_slug', 'course_category' ) );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'slugs/module', get_option( 'brainpress_module_slug', 'module' ) );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'slugs/units', get_option( 'brainpress_units_slug', 'units' ) );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'slugs/notifications', get_option( 'brainpress_notifications_slug', 'notifications' ) );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'slugs/discussions', get_option( 'brainpress_discussion_slug', 'discussion' ) );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'slugs/discussions_new', get_option( 'brainpress_discussion_slug_new', 'add_new_discussion' ) );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'slugs/grades', get_option( 'brainpress_grades_slug', 'grades' ) );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'slugs/workbook', get_option( 'brainpress_workbook_slug', 'workbook' ) );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'slugs/enrollment', get_option( 'enrollment_process_slug', 'enrollment_process' ) );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'slugs/login', get_option( 'login_slug', 'student-login' ) );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'slugs/signup', get_option( 'signup_slug', 'courses-signup' ) );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'slugs/student_dashboard', get_option( 'student_dashboard_slug', 'courses-dashboard' ) );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'slugs/student_settings', get_option( 'student_settings_slug', 'student-settings' ) );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'slugs/instructor_profile', get_option( 'instructor_profile_slug', 'instructor' ) );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'slugs/inbox', get_option( 'brainpress_inbox_slug', 'student-inbox' ) );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'slugs/sent_messages', get_option( 'brainpress_sent_messages_slug', 'student-sent-messages' ) );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'slugs/new_messages', get_option( 'brainpress_new_message_slug', 'student-new-message' ) );

		// Pages
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'pages/enrollment', get_option( 'brainpress_enrollment_process_page', 0 ) );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'pages/login', get_option( 'brainpress_login_page', 0 ) );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'pages/signup', get_option( 'brainpress_signup_page', 0 ) );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'pages/student_dashboard', get_option( 'brainpress_student_dashboard_page', 0 ) );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'pages/student_settings', get_option( 'brainpress_student_settings_page', 0 ) );

		// Course
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'course/details_media_type', get_option( 'details_media_type', 'default' ) );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'course/details_media_priority', get_option( 'details_media_priority', 'video' ) );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'course/listing_media_type', get_option( 'listings_media_type', 'default' ) );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'course/listing_media_priority', get_option( 'listings_media_priority', 'image' ) );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'course/order_by', get_option( 'course_order_by', 'post_date' ) );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'course/order_by_direction', get_option( 'course_order_by_type', 'DESC' ) );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'course/image_width', get_option( 'course_image_width', 235 ) );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'course/image_height', get_option( 'course_image_height', 235 ) );

		// Reports
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'reports/font', get_option( 'reports_font', 'helvetica' ) );

		// Instructor
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'instructor/show_username', get_option( 'show_instructor_username', 1 ) );
		$default_capabilities = BrainPress_Data_Capabilities::get_instructor_capabilities();
		$instructor_capabilities = get_option( 'brainpress_instructor_capabilities', array() );

		if ( ! empty( $instructor_capabilities ) && is_array( $instructor_capabilities ) ) {
			foreach ( $instructor_capabilities as $capability ) {
				$default_capabilities[ $capability ] = 1;
			}
		}

		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'instructor/capabilities', $default_capabilities );

		// Basic Certificate
		/**
		 * @todo Use method in basic certificate class when implemented.
		 */
		$options = get_option( 'brainpress_basic_certificate' );
		$value = isset( $options['basic_certificate_enable'] ) ? $options['basic_certificate_enable'] : 1;
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'basic_certificate/enabled', $value );
		$value = isset( $options['certificate_content'] ) ? $options['certificate_content'] : BrainPress_View_Admin_Setting_BasicCertificate::default_certificate_content();
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'basic_certificate/content', $value );
		$value = isset( $options['background_url'] ) ? $options['background_url'] : '';
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'basic_certificate/background_image', $value );
		$value = isset( $options['padding_top'] ) ? $options['padding_top'] : 0;
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'basic_certificate/padding/top', $value );
		$value = isset( $options['padding_bottom'] ) ? $options['padding_bottom'] : 0;
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'basic_certificate/padding/bottom', $value );
		$value = isset( $options['padding_left'] ) ? $options['padding_left'] : 0;
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'basic_certificate/padding/left', $value );
		$value = isset( $options['padding_right'] ) ? $options['padding_right'] : 0;
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'basic_certificate/padding/right', $value );
		$value = isset( $options['orientation'] ) ? $options['orientation'] : 'L';
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'basic_certificate/orientation', $value );
		$value = isset( $options['styles'] ) ? $options['styles'] : '';
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'basic_certificate/styles', $value );

		// Email Settings
		// Basic Certficate
		$default_settings = BrainPress_Helper_Setting_Email::get_defaults();
		$value = isset( $options['auto_email'] ) ? $options['auto_email'] : 1;
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'email/basic_certificate/auto_email', $value );
		$value = isset( $options['from_name'] ) ? $options['from_name'] : $default_settings['basic_certificate']['from'];
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'email/basic_certificate/from', $value );
		$value = isset( $options['from_email'] ) ? $options['from_email'] : $default_settings['basic_certificate']['email'];
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'email/basic_certificate/email', $value );
		$value = isset( $options['email_subject'] ) ? $options['email_subject'] : $default_settings['basic_certificate']['subject'];
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'email/basic_certificate/subject', $value );
		$value = isset( $options['email_content'] ) ? $options['email_content'] : $default_settings['basic_certificate']['content'];
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'email/basic_certificate/content', $value );

		// Registration
		$value = get_option( 'registration_from_name', $default_settings['registration']['from'] );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'email/registration/from', $value );
		$value = get_option( 'registration_from_email', $default_settings['registration']['email'] );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'email/registration/email', $value );
		$value = get_option( 'registration_email_subject', $default_settings['registration']['subject'] );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'email/registration/subject', $value );
		$value = get_option( 'registration_content_email', $default_settings['registration']['content'] );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'email/registration/content', $value );

		// Enrollment Confirmation
		$value = get_option( 'enrollment_from_name', $default_settings['enrollment_confirm']['from'] );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'email/enrollment_confirm/from', $value );
		$value = get_option( 'enrollment_from_email', $default_settings['enrollment_confirm']['email'] );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'email/enrollment_confirm/email', $value );
		$value = get_option( 'enrollment_email_subject', $default_settings['enrollment_confirm']['subject'] );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'email/enrollment_confirm/subject', $value );
		$value = get_option( 'enrollment_content_email', $default_settings['enrollment_confirm']['content'] );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'email/enrollment_confirm/content', $value );

		// Course Invitation
		$value = get_option( 'invitation_from_name', $default_settings['course_invitation']['from'] );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'email/course_invitation/from', $value );
		$value = get_option( 'invitation_from_email', $default_settings['course_invitation']['email'] );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'email/course_invitation/email', $value );
		$value = get_option( 'invitation_email_subject', $default_settings['course_invitation']['subject'] );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'email/course_invitation/subject', $value );
		$value = get_option( 'invitation_content_email', $default_settings['course_invitation']['content'] );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'email/course_invitation/content', $value );

		// Invitation with passcode
		$value = get_option( 'invitation_passcode_from_name', $default_settings['course_invitation_password']['from'] );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'email/course_invitation_password/from', $value );
		$value = get_option( 'invitation_passcode_from_email', $default_settings['course_invitation_password']['email'] );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'email/course_invitation_password/email', $value );
		$value = get_option( 'invitation_passcode_email_subject', $default_settings['course_invitation_password']['subject'] );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'email/course_invitation_password/subject', $value );
		$value = get_option( 'invitation_content_passcode_email', $default_settings['course_invitation_password']['content'] );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'email/course_invitation_password/content', $value );

		// Instructor Invitation
		$value = get_option( 'instructor_invitation_from_name', $default_settings['instructor_invitation']['from'] );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'email/instructor_invitation/from', $value );
		$value = get_option( 'instructor_invitation_from_email', $default_settings['instructor_invitation']['email'] );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'email/instructor_invitation/email', $value );
		$value = get_option( 'instructor_invitation_email_subject', $default_settings['instructor_invitation']['subject'] );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'email/instructor_invitation/subject', $value );
		$value = get_option( 'instructor_invitation_email', $default_settings['instructor_invitation']['content'] );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'email/instructor_invitation/content', $value );

		// New Order
		$value = get_option( 'mp_order_from_name', $default_settings['new_order']['from'] );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'email/new_order/from', $value );
		$value = get_option( 'mp_order_from_email', $default_settings['new_order']['email'] );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'email/new_order/email', $value );
		$value = get_option( 'mp_order_email_subject', $default_settings['new_order']['subject'] );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'email/new_order/subject', $value );
		$value = get_option( 'mp_order_content_email', $default_settings['new_order']['content'] );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'email/new_order/content', $value );

		// MP
		$value = is_plugin_active( 'marketpress/marketpress.php' );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'marketpress/enabled', $value );
		$value = get_option( 'redirect_mp_to_course', false );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'marketpress/redirect', $value );

		// WooCommerce Integration
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'woocommerce/enabled', get_option( 'use_woo', 0 ) );
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'woocommerce/redirect', get_option( 'redirect_woo_to_course',0 ) );

		// Terms of Service Integration
		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'tos/use', get_option( 'show_tos', 0 ) );

		update_option( 'brainpress_settings', $settings );

		/**
		 * Upgrade site meta (or blog option)
		 *
		 * Store settings in one key rather than all over the options in the table
		 */
		if ( ! is_multisite() ) {
			$settings = get_option( 'brainpress_settings' );
		} else {
			$settings = get_site_option( 'brainpress_settings', array() );
		}

		$settings = BrainPress_Helper_Utility::set_array_value( $settings, 'general/version', BrainPress::$version );

		if ( ! is_multisite() ) {
			update_option( 'brainpress_settings', $settings );
		} else {
			update_site_option( 'brainpress_settings', $settings );
		}

		/**
		 * Clean up time
		 * DO NOT DELETE THOSE OPTIONS - most of them are used in CP 2.0!!
		 *
		 * @todo  remove this block once 2.0 is stable or document this list somewhere else...
		 */
		// delete_option( 'display_menu_items' );
		// delete_option( 'use_custom_login_form' );
		// delete_option( 'redirect_students_to_dashboard' );
		// delete_option( 'brainpress_course_slug' );
		// delete_option( 'brainpress_course_category_slug' );
		// delete_option( 'brainpress_module_slug' );
		// delete_option( 'brainpress_units_slug' );
		// delete_option( 'brainpress_notifications_slug' );
		// delete_option( 'brainpress_discussion_slug' );
		// delete_option( 'brainpress_discussion_slug_new' );
		// delete_option( 'brainpress_grades_slug' );
		// delete_option( 'brainpress_workbook_slug' );
		// delete_option( 'enrollment_process_slug' );
		// delete_option( 'student_dashboard_slug' );
		// delete_option( 'student_settings_slug' );
		// delete_option( 'instructor_profile_slug' );
		// delete_option( 'brainpress_inbox_slug' );
		// delete_option( 'brainpress_sent_messages_slug' );
		// delete_option( 'brainpress_new_message_slug' );
		// delete_option( 'enrollment_process_slug' );
		// delete_option( 'brainpress_enrollment_process_page' );
		// delete_option( 'brainpress_login_page' );
		// delete_option( 'brainpress_signup_page' );
		// delete_option( 'brainpress_student_dashboard_page' );
		// delete_option( 'brainpress_student_settings_page' );
		// delete_option( 'details_media_type' );
		// delete_option( 'details_media_priority' );
		// delete_option( 'listings_media_type' );
		// delete_option( 'listings_media_priority' );
		// delete_option( 'course_order_by' );
		// delete_option( 'course_order_by_type' );
		// delete_option( 'course_image_width' );
		// delete_option( 'course_image_height' );
		// delete_option( 'reports_font' );
		// delete_option( 'show_instructor_username' );
		// delete_option( 'brainpress_instructor_capabilities' );
		// delete_option( 'brainpress_basic_certificate' );
		// delete_option( 'registration_from_name' );
		// delete_option( 'registration_from_email' );
		// delete_option( 'registration_email_subject' );
		// delete_option( 'registration_content_email' );
		// delete_option( 'enrollment_from_name' );
		// delete_option( 'enrollment_from_email' );
		// delete_option( 'enrollment_email_subject' );
		// delete_option( 'enrollment_content_email' );
		// delete_option( 'invitation_from_name' );
		// delete_option( 'invitation_from_email' );
		// delete_option( 'invitation_email_subject' );
		// delete_option( 'invitation_content_email' );
		// delete_option( 'invitation_passcode_from_name' );
		// delete_option( 'invitation_passcode_from_email' );
		// delete_option( 'invitation_passcode_email_subject' );
		// delete_option( 'invitation_content_passcode_email' );
		// delete_option( 'instructor_invitation_from_name' );
		// delete_option( 'instructor_invitation_from_email' );
		// delete_option( 'instructor_invitation_email_subject' );
		// delete_option( 'instructor_invitation_email' );
		// delete_option( 'mp_order_from_name' );
		// delete_option( 'mp_order_from_email' );
		// delete_option( 'mp_order_email_subject' );
		// delete_option( 'mp_order_content_email' );
		// delete_option('redirect_woo_to_course' );
		// delete_option( 'use_woo' );
		// delete_option( 'show_tos' );
	}
}