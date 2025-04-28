<?php
/**
 * CourePress Hooks
 *
 * @package ClassicPress
 * @subpackage BrainPress
 **/
class BrainPress_Hooks {
	static $warning_message = '';

	public static function init() {
		// Listen to course withdrawal request
		add_action( 'init', array( 'BrainPress_Data_Student', 'withdraw_from_course' ) );

		// Listen to module submission
		add_action( 'init', array( 'BrainPress_Module', 'process_submission' ) );

		// Listen to enrollment request
		add_action( 'init', array( 'BrainPress_Template_Student', 'process_enrollment' ) );

		// Listen to comment submission
		add_action( 'init', array( 'BrainPress_Data_Discussion', 'init' ), 100 );

		// Log student visit on modules
		add_action( 'brainpress_module_view', array( 'BrainPress_Data_Student', 'log_student_activity' ), 10, 2 );

		// Edit Course
		add_filter( 'post_updated_messages', array( 'BrainPress_Admin_Edit', 'updated_messages' ) );
		add_action('current_screen', array('BrainPress_Admin_Edit', 'init_hooks'));

		// Enable TinyMCE for course pages.
		add_filter( 'user_can_richedit', array( 'BrainPress_Admin_Edit', 'enable_tinymce' ) );

		// Per course certificate preview
		add_action( 'init', array( 'BrainPress_Admin_Edit', 'certificate_preview' ) );

		// Update Course
		add_action( 'brainpress_course_updated', array( 'BrainPress_Data_Course', 'get_expired_courses' ) );
		add_action( 'brainpress_course_updated', array( 'BrainPress_Data_Course', 'get_enrollment_ended_courses' ) );
		add_action( 'wp_ajax_update_course', array( 'BrainPress_Admin_Controller_Course', 'update_course' ) );

		// Update UnitBuilder
		add_action( 'wp_ajax_unit_builder', array( 'BrainPress_Admin_Controller_Unit', 'unit_builder_ajax' ) );

		// Hook to admin ajax request
		add_action( 'wp_ajax_brainpress_request', array( __CLASS__, 'process_request' ) );
		add_action( 'wp_ajax_nopriv_brainpress_request', array( __CLASS__, 'process_request' ) );

		// Course list
		add_action( 'admin_init', array( 'BrainPress_Admin_Courses', 'init' ) );

		// Course Instructors list
		add_action( 'admin_init', array( 'BrainPress_Admin_Instructors', 'init' ) );

		// Search user
		add_action( 'wp_ajax_brainpress_user_search', array( 'BrainPress_Admin_Students', 'search_user' ) );

		// Set front scripts
		add_action( 'wp_enqueue_scripts', array( 'BrainPress_Helper_Javascript', 'front_assets' ) );
		// Print assets at wp_footer if CP shortcode is used!
		add_action( 'wp_footer', array( 'BrainPress_Helper_Javascript', 'maybe_print_assets' ) );

		// Set admin scripts
		add_action( 'admin_enqueue_scripts', array( 'BrainPress_Helper_Javascript', 'enqueue_admin_scripts' ) );
		add_action( 'admin_footer', array( 'BrainPress_Helper_Javascript', 'enqueue_scripts' ) );

		// Update Communication
		add_action( 'wp_ajax_update_notification', array( 'BrainPress_Data_Notification', 'ajax_update' ) );
		add_action( 'wp_ajax_update_discussion', array( 'BrainPress_Data_Discussion', 'ajax_update' ) );

		// MP Notice
		add_action( 'admin_notices', array( 'BrainPress_Helper_Extension_MarketPress', 'mp_notice' ) );

		// Admin class
		add_filter( 'admin_body_class', array( __CLASS__, 'admin_classes' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'remove_css_overrides' ), 99 );
	}

	/**
	 * Receives ajax request.
	 **/
	public static function process_request() {
		$input = json_decode( file_get_contents( 'php://input' ) );

		if ( ! empty( $input->cpnonce ) && wp_verify_nonce( $input->cpnonce, 'brainpress_nonce' ) ) {
			$method = $input->method;
			$class = $input->className;

			if ( class_exists( $class ) && method_exists( $class, $method ) ) {
				$input = BrainPress_Helper_Utility::object_to_array( $input );

				call_user_func( array( $class, $method ), $input );

				exit;
			}
		}
	}

	public static function admin_classes( $class ) {
		$_class = '';

		if ( cp_is_chat_plugin_active() ) {
			$_class .= 'cp-with-chat';
		}

		if ( is_array( $class ) ) {
			array_push( $class, $_class );
		} else {
			$class .= $_class;
		}

		return $class;
	}

	public static function remove_css_overrides() {
		global $pagenow, $typenow;

		if ( 'course' === $typenow ) {
			wp_dequeue_style( 'jquery-ui-datepicker' );
			wp_dequeue_style( 'jquery-smoothness' );
		}
	}
}
