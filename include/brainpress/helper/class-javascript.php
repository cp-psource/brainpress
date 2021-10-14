<?php
class BrainPress_Helper_JavaScript {
	protected static $is_cp_called = false;

	public static $scripts = array();
	public static $styles = array();

	// Depracated!!!
	public static function init() {}

	/**
	 * Check if current page is CP page.
	 **/
	public static function is_valid_page() {
		$course_js_pages = array(
			'brainpress_course',
			'brainpress_assessments',
			'brainpress_reports',
			'brainpress_notifications',
			'brainpress_discussions',
		);

		$valid_pages = array_merge( $course_js_pages, array(
			'brainpress_settings',
			'brainpress',
		) );

		if ( isset( $_GET['page'] ) && in_array( $_GET['page'], $valid_pages ) || 'course' == get_post_type() ) {
			if ( 'course' == get_post_type() ) {
				$_GET['page'] = $_REQUEST['page'] = 'brainpress_course';
			}
			return true;
		}

		return false;
	}

	public static function enqueue_admin_scripts() {
		if ( self::is_valid_page() ) {
			// Enqueue needed scripts for UI
			wp_enqueue_media();
		}
	}

	public static function enqueue_scripts() {
		global $pagenow;

		$is_valid_page = self::is_valid_page();

		if ( false === $is_valid_page ) {
			return;
		}

		$course_type = BrainPress_Data_Course::get_post_type_name();
		$script = BrainPress::$url . 'asset/js/brainpress.js';

		wp_enqueue_script( 'brainpress_object', $script, array(
			'jquery',
			'backbone',
			'underscore',
		), BrainPress::$version );

		// Create a dummy editor to by used by the BrainPress JS object
		ob_start();
		wp_editor( 'dummy_editor_content', 'dummy_editor_id', array(
			'wpautop' => true,
			'textarea_name' => 'dummy_editor_name',
		) );
		$dummy_editor = ob_get_clean();

		$is_super_admin = user_can( 'manage_options', get_current_user_id() );

		$localize_array = array(
			'_ajax_url' => BrainPress_Helper_Utility::get_ajax_url(),
			'_dummy_editor' => $dummy_editor,
			'allowed_video_extensions' => wp_get_video_extensions(),
			'allowed_audio_extensions' => wp_get_audio_extensions(),
			'allowed_image_extensions' => BrainPress_Helper_Utility::get_image_extensions(),
			'allowed_extensions' => apply_filters( 'brainpress_custom_allowed_extensions', false ),
			'date_format' => get_option( 'date_format' ),
			'editor_visual' => __( 'Visuell', 'brainpress' ),
			'editor_text' => _x( 'Text', 'Name für die Registerkarte Texteditor (früher HTML)', 'brainpress' ),
			'invalid_extension_message' => __( 'Die Erweiterung der Datei ist ungültig. Bitte verwende eine der folgenden Möglichkeiten:', 'brainpress' ),
			'assessment_grid_url' => admin_url( 'admin.php?page=brainpress_assessments' ),
			'assessment_report_url' => admin_url( 'admin.php?page=brainpress_reports' ),
			'is_campus' => CP_IS_CAMPUS,
			'is_super_admin' => $is_super_admin,
			'user_caps' => BrainPress_Data_Capabilities::get_user_capabilities(),
			'server_error' => __( 'Bei der Bearbeitung Deiner Anfrage ist ein Fehler aufgetreten. Bitte versuche es später noch einmal!', 'brainpress' ),
			'labels' => array(
				'user_dropdown_placeholder' => __( 'Gib den Benutzernamen, den Vor- und Nachnamen oder die E-Mail-Adresse ein', 'brainpress' ),
				'required_fields' => __( 'Erforderliche Felder dürfen nicht leer sein!', 'brainpress' ),
			),
		);

		// Add course_id in edit page
		if ( $course_type == get_post_type() ) {
			$localize_array['course_id'] = get_the_ID();
		}

		// Models
		/** BRAINPRESS_COURSE */
		if ( $is_valid_page ) {

			$brainpress_course_depends_array = array(
				'jquery-ui-accordion',
				'jquery-effects-highlight',
				'jquery-effects-core',
				'jquery-ui-datepicker',
				'jquery-ui-spinner',
				'jquery-ui-droppable',
				'jquery-ui-draggable',
				'backbone',
			);

			if ( apply_filters( 'brainpress_use_select2_student_selector', true ) ) {
				/**
				 * Deregister script to avoid conflicts, we can do it,we just
				 * load this on CP related pages.
				 */
				wp_deregister_script( 'jquery-select2' );
				wp_register_script(
					'jquery-select2',
					BrainPress::$url . 'asset/js/external/select2.min.js',
					array( 'jquery' ),
					'4.0.2',
					true
				);
				/**
				 * Deregister style to avoid conflicts, we can do it,we just
				 * load this on CP related pages.
				 */
				wp_deregister_style( 'select2' );
				$brainpress_course_depends_array[] = 'jquery-select2';
				$src = BrainPress::$url . 'asset/css/external/select2.min.css';
				wp_enqueue_style(
					'select2',
					$src,
					array(),
					'4.0.2'
				);
			}
			$script = BrainPress::$url . 'asset/js/brainpress-course.js';
			wp_enqueue_script( 'brainpress_course', $script, $brainpress_course_depends_array, BrainPress::$version );

			$script = BrainPress::$url . 'asset/js/external/jquery.treegrid.min.js';
			wp_enqueue_script( 'jquery-treegrid', $script, array(
				'jquery'
			), BrainPress::$version );

			$localize_array['instructor_role_defined'] = defined( 'BRAINPRESS_INSTRUCTOR_ROLE' );
			$localize_array['instructor_avatars'] = BrainPress_Helper_UI::get_user_avatar_array();
			$localize_array['instructor_delete_confirm'] = __( 'Bitte bestätige, dass Du den Kursleiter aus diesem Kurs entfernen möchtest.', 'brainpress' );
			$localize_array['instructor_delete_invite_confirm'] = __( 'Bitte bestätige, dass Du die Kursleitereinladung aus diesem Kurs entfernen möchtest.', 'brainpress' );
			$localize_array['facilitator_delete_confirm'] = __( 'Bitte bestätige, dass Du den Moderator aus diesem Kurs entfernen möchtest.', 'brainpress' );
			$localize_array['facilitator_delete_invite_confirm'] = __( 'Bitte bestätige, dass Du die Moderatoreinladung aus diesem Kurs entfernen möchtest.', 'brainpress' );
			$localize_array['instructor_empty_message'] = __( 'Bitte beauftrage Kursleiter', 'brainpress' );
			$localize_array['facilitator_empty_message'] = __( 'Moderator zuweisen', 'brainpress' );
			$localize_array['instructor_pednding_status'] = __( 'Ausstehend', 'brainpress' );
			$localize_array['email_validation_pattern'] = __( '.+@.+', 'brainpress' );
			$localize_array['student_delete_confirm'] = __( 'Bitte bestätige, dass Du den Studenten aus diesem Kurs entfernen möchtest.', 'brainpress' );
			$localize_array['student_delete_all_confirm'] = __( 'Bitte bestätige, dass Du ALLE Studenten aus diesem Kurs entfernen möchtest. Warnung: Dies kann nicht rückgängig gemacht werden. Bitte stelle sicher, dass Du dies tun möchtest.', 'brainpress' );

			// Discussion / Notification
			$localize_array['notification_bulk_delete'] = __( 'Bitte bestätige, dass Du ALLE ausgewählten Benachrichtigungen löschen möchtest. Warnung: Dies kann nicht rückgängig gemacht werden. Bitte stelle sicher, dass Du dies tun möchtest.', 'brainpress' );
			$localize_array['notification_delete'] = __( 'Bitte bestätigen Sie, dass Du diese Benachrichtigung löschen möchtest. Warnung: Dies kann nicht rückgängig gemacht werden.', 'brainpress' );

			$localize_array['discussion_bulk_delete'] = __( 'Bitte bestätige, dass Du ALLE ausgewählten Diskussionen löschen möchtest. Warnung: Dies kann nicht rückgängig gemacht werden. Bitte stelle sicher, dass Du dies tun möchtest.', 'brainpress' );
			$localize_array['discussion_delete'] = __( 'Bitte bestätige, dass Du diese Diskussion löschen möchtest. Warnung: Dies kann nicht rückgängig gemacht werden.', 'brainpress' );

			if ( ! empty( $_REQUEST['id'] ) ) {
				$localize_array['course_id'] = (int) $_REQUEST['id'];
				$localize_array['course_title'] = get_the_title( $_REQUEST['id'] );
			}
		}

		$style_global = BrainPress::$url . 'asset/css/admin-global.css';
		$timepicker_css = BrainPress::$url . 'asset/css/external/jquery-ui-timepicker-addon.min.css';
		$timepicker_js = BrainPress::$url . 'asset/js/external/jquery-ui-timepicker-addon.min.js';
		wp_enqueue_style( 'brainpress_admin_timepicker', $timepicker_css, false, BrainPress::$version );
		wp_enqueue_script( 'brainpress_admin_timepicker', $timepicker_js, array( 'jquery-ui-slider', 'jquery-ui-datepicker' ), BrainPress::$version, true );
		wp_enqueue_style( 'brainpress_admin_global', $style_global, array( 'jquery-datepicker' ), BrainPress::$version );

		/** BRAINPRESS_COURSE|UNIT BUILDER */
		if ( 'brainpress_course' == $_GET['page'] && isset( $_GET['tab'] ) && 'units' == $_GET['tab'] ) {
			$script = BrainPress::$url . 'asset/js/brainpress-unitsbuilder.js';

			wp_enqueue_script( 'brainpress_unit_builder', $script, array(
				'brainpress_course',
			), BrainPress::$version );

			$localize_array['unit_builder_templates'] = BrainPress_Helper_UI_Module::get_template( true );
			$localize_array['unit_builder_module_types'] = BrainPress_Helper_UI_Module::get_types();
			$localize_array['unit_builder_module_labels'] = BrainPress_Helper_UI_Module::get_labels();
			$localize_array['unit_builder_delete_module_confirm'] = __( 'Bitte bestätige, dass Du dieses Modul und mögliche Antworten der Studenten entfernen möchtest.', 'brainpress' );
			$localize_array['unit_builder_delete_page_confirm'] = __( 'Bitte bestätige, dass Du diese Seite entfernen möchtes. Alle Module werden auf die erste verfügbare Seite verschoben (oder Du kannst sie zuerst auf anderen Seiten ablegen, bevor Du diese Seite löschst)..', 'brainpress' );
			$localize_array['unit_builder_delete_unit_confirm'] = __( 'Bitte bestätige, dass Du diese Einheit und alle seine Module und Studentenantworten entfernen möchten.', 'brainpress' );
			$localize_array['unit_builder_new_unit_title'] = __( 'Einheit ohne Titel', 'brainpress' );
			$localize_array['unit_builder_add_answer_label'] = __( 'Antwort hinzufügen', 'brainpress' );
			$localize_array['unit_builder_form_pleaceholder_label'] = __( 'Platzhaltertext', 'brainpress' );
			$localize_array['unit_builder_form_pleaceholder_desc'] = __( 'Platzhaltertext zum Einfügen in das Textfeld (zusätzliche Informationen)', 'brainpress' );
			$localize_array['unit_builder_form']['messages']['required_fields'] = __( "Antwortfelder dürfen nicht leer sein!\nBitte überprüfe die Module:", 'brainpress' );
			$localize_array['unit_builder_form']['messages']['saving_unit'] = __( 'Einheit speichert jetzt...', 'brainpress' );
			$localize_array['unit_builder_form']['messages']['successfully_saved'] = __( 'Einheit wurde erfolgreich gespeichert!', 'brainpress' );
			$localize_array['unit_builder_form']['messages']['error_while_saving'] = __( 'Etwas ist schief gelaufen. Einheit wurde nicht gespeichert!', 'brainpress' );
			$localize_array['unit_builder']['question_type'] = array(
				'single' => __( 'Single Choice', 'brainpress' ),
				'multiple' => __( 'Multiple Choice', 'brainpress' ),
				'short' => __( 'Kurze Antwort', 'brainpress' ),
				'long' => __( 'Lange Antwort', 'brainpress' ),
				'selectable' => __( 'Auswählbare Auswahl', 'brainpress' ),
			);
			$localize_array['unit_builder_form']['messages']['adding_module'] = __( 'Warte, Modul jetzt hinzufügen ...', 'brainpress' );
			$localize_array['unit_l8n'] = array(
				'pre_answers' => array(
					'a' => __( 'Antwort A', 'brainpress' ),
					'b' => __( 'Antwort B', 'brainpress' ),
				),
			);
		}

		/** COURSE LIST */
		$screen = get_current_screen();
		if ( 'brainpress_course' === $_GET['page'] && 'edit.php' == $pagenow && 'edit-course' == $screen->id ) {
			$script = BrainPress::$url . 'asset/js/brainpress-courselist.js';
			wp_enqueue_script( 'brainpress_course_list', $script, array(
				'jquery-ui-accordion',
				'jquery-effects-highlight',
				'jquery-effects-core',
				'jquery-ui-datepicker',
				'jquery-ui-spinner',
				'jquery-ui-droppable',
				'jquery-ui-draggable',
				'backbone',
			), BrainPress::$version );

			$localize_array['courselist_bulk_delete'] = __( 'Bitte bestätige, dass Du ALLE ausgewählten Kurse löschen möchtest. Warnung: Dies kann nicht rückgängig gemacht werden. Bitte stelle sicher, dass Du dies tun möchtest.', 'brainpress' );
			$localize_array['courselist_delete_course'] = __( 'Bitte bestätige, dass Du diese Kurse löschen möchtest. Warnung: Dies kann nicht rückgängig gemacht werden.', 'brainpress' );
			$localize_array['courselist_duplicate_course'] = __( 'Bist Du sicher, dass Du eine Kopie dieses Kurses erstellen möchtest?', 'brainpress' );
			$localize_array['courselist_export'] = __( 'Bitte wähle mindestens einen Kurs zum Exportieren aus.', 'brainpress' );
		}

		if ( 'brainpress_assessments' === $_GET['page'] ) {
			$script = BrainPress::$url . '/asset/js/brainpress-assessment.js';
			wp_enqueue_script( 'brainpress_assessment',
				$script,
				null,
			BrainPress::$version );
			$localize_array['courseinstructor_id'] = get_current_user_id();
			$localize_array['instructor_name'] = BrainPress_Helper_Utility::get_user_name( get_current_user_id(), true );
			$localize_array['assessment_labels'] = array(
				'pass' => __( 'Bestanden', 'brainpress' ),
				'fail' => __( 'Gescheitert', 'brainpress' ),
				'add_feedback' => __( 'Feedback hinzufügen', 'brainpress' ),
				'edit_feedback' => __( 'Feedback bearbeiten', 'brainpress' ),
				'cancel_feedback' => __( 'Abbrechen', 'brainpress' ),
				'help_tooltip' => __( 'Wenn ein Student durch die Einreichung dieser Note den Kurs abschließt, wird automatisch eine E-Mail mit Zertifikat gesendet.', 'brainpress' ),
				'minimum_help' => __( 'Du kannst diese Mindestnote in der Kurseinstellung ändern.', 'brainpress' ),
			);
		}

		/**
		 * save unit message.
		 */
			$localize_array['unit_builder_form']['messages']['setup']['saving'] = __( 'Schritt wird jetzt gespeichert...', 'brainpress' );
			$localize_array['unit_builder_form']['messages']['setup']['saved'] = __( 'Schritt wurde erfolgreich gespeichert!', 'brainpress' );
			$localize_array['unit_builder_form']['messages']['setup']['error'] = __( 'Etwas ist schief gelaufen. Schritt wurde nicht gespeichert!', 'brainpress' );

		wp_localize_script( 'brainpress_object', '_brainpress', $localize_array );
	}

	public static function enqueue_front_scripts() {
		global $wp_query;

		// See if we are inside a course.
		$is_cp = BrainPress_Helper_Utility::the_course( true );

		if ( ! $is_cp ) {
			// See if we are on a special BrainPress page.
			$post_type = get_post_type();
			$valid_cpt = array(
				BrainPress_Data_Course::get_post_type_name(),
				'course_notifications_archive',
				'course_workbook',
				'course_discussion_archive',
				'course_discussion',
				'course_archive',
				'brainpress_instructor', // virtual post type
				'brainpress_student_dashboard',
				'brainpress_student_login',
				'brainpress_student_signup',
				BrainPress_Data_Discussion::get_post_type_name(),
			);
			$is_cp = in_array( $post_type, $valid_cpt );
		}
		if ( ! $is_cp ) {
			// Check if there is a course object in wp_query.
			$is_cp = isset( $wp_query->query['course'] );
		}
		if ( ! $is_cp ) {
			// Check if there is a course object in wp_query.
			$is_cp = isset( $wp_query->query['coursename'] );
		}

		// Stop here, if front-end page does not contain BrainPress data!
		if ( ! $is_cp ) { return; }

		// BrainPress Object
		$script = BrainPress::$url . 'asset/js/brainpress.js';
		wp_enqueue_script(
			'brainpress_object',
			$script,
			array(
				'jquery',
				'backbone',
				'underscore',
			),
			BrainPress::$version
		);

		self::brainpress_front_js();

		$course_id = BrainPress_Helper_Utility::the_course( true );

		$localize_array = array(
			'_ajax_url' => BrainPress_Helper_Utility::get_ajax_url(),
			'allowed_video_extensions' => wp_get_video_extensions(),
			'allowed_audio_extensions' => wp_get_audio_extensions(),
			'allowed_image_extensions' => BrainPress_Helper_Utility::get_image_extensions(),
			'allowed_extensions' => apply_filters( 'brainpress_custom_allowed_extensions', false ),
			'allowed_student_extensions' => BrainPress_Helper_Utility::allowed_student_mimes(),
			'no_browser_upload' => __( 'Bitte versuche es mit einem anderen Browser, um Deine Datei hochzuladen.', 'brainpress' ),
			'invalid_upload_message' => __( 'Bitte lade nur eine der folgenden Dateien hoch: ', 'brainpress' ),
			'file_uploaded_message' => __( 'Deine Datei wurde erfolgreich übermittelt.', 'brainpress' ),
			'file_upload_fail_message' => __( 'Beim Verarbeiten Deiner Datei ist ein Problem aufgetreten.', 'brainpress' ),
			'response_saved_message' => __( 'Deine Antwort wurde erfolgreich aufgezeichnet.', 'brainpress' ),
			'response_fail_message' => __( 'Beim Speichern Deiner Antwort ist ein Problem aufgetreten. Bitte lade diese Seite neu und versuche es erneut.', 'brainpress' ),
			'current_course' => $course_id,
			'current_course_is_paid' => BrainPress_Data_Course::is_paid_course( $course_id )? 'yes':'no',
			'current_course_type' => BrainPress_Data_Course::get_setting( $course_id, 'enrollment_type', 'manually' ),
			'course_url' => get_permalink( BrainPress_Helper_Utility::the_course( true ) ),
			'home_url' => home_url(),
			'current_student' => get_current_user_id(),
			'workbook_view_answer' => __( 'Ansehen', 'brainpress' ),
			'labels' => BrainPress_Helper_UI_Module::get_labels(),
			'signup_errors' => array(
				'all_fields' => __( 'Alle Felder benötigt.', 'brainpress' ),
				'email_invalid' => __( 'Ungültige E-Mail-Adresse.', 'brainpress' ),
				'email_exists' => __( 'Diese E-Mail-Adresse ist bereits vergeben.', 'brainpress' ),
				'user_exists' => __( 'Dieser Benutzername ist bereits vergeben.', 'brainpress' ),
				'weak_password' => __( 'Schwache Passwörter nicht erlaubt.', 'brainpress' ),
				'mismatch_password' => __( 'Passwörter stimmen nicht überein.', 'brainpress' ),
			),
			'comments' => array(
				'require_valid_comment' => __( 'Please type a comment.', 'brainpress' ),
			),
		);

		/**
		 * add unit-not-available url
		 */
		$url = $localize_array['course_url'].BrainPress_Core::get_slug( 'units/' );
		$localize_array['course_url_unit_nor_available'] = BrainPress_Helper_Message::add_message_query_arg( $url, 'unit-not-available' );

		/**
		 * Filter localize script to allow data insertion.
		 *
		 * @since 2.0
		 *
		 * @param (array) $localize_array.
		 **/
		$localize_array = apply_filters( 'brainpress_localize_object', $localize_array );

		wp_localize_script(
			'brainpress_object',
			'_brainpress',
			$localize_array
		);

		$script = BrainPress::$url . 'asset/js/external/circle-progress.min.js';
		wp_enqueue_script(
			'circle-progress',
			$script,
			array( 'jquery' ),
			BrainPress::$version
		);

		$script = BrainPress::$url . 'asset/js/external/backbone.modal-min.js';
		wp_enqueue_script(
			'backbone-modal',
			$script,
			array(
				'backbone',
				'password-strength-meter',
			),
			BrainPress::$version
		);

		$fontawesome = BrainPress::$url . 'asset/css/external/font-awesome.min.css';
		wp_enqueue_style(
			'fontawesome',
			$fontawesome,
			array(),
			BrainPress::$version
		);

		$front_css = BrainPress::$url . 'asset/css/front.css';
		wp_enqueue_style( 'brainpress-front', $front_css, array(), BrainPress::$version );
	}

	public static function maybe_print_assets() {
		if ( BrainPress_Core::$is_cp_page ) {
			if ( false == self::$is_cp_called ) {
				// Load the script
				self::front_assets();
			}
		}

		if ( self::$is_cp_called && false === is_user_logged_in() ) {
			// Print enrollment templates
			echo do_shortcode( '[brainpress_enrollment_templates]' );
		}
	}

	public static function front_assets() {
		if ( false === BrainPress_Core::$is_cp_page ) {
			return;
		}

		self::$is_cp_called = true;
		$script_url = BrainPress::$url . 'asset/js/';
		$css_url = BrainPress::$url . 'asset/css/';
		$version = BrainPress::$version;
		$course_id = BrainPress_Helper_Utility::the_course( true );

		// Fontawesome
		$fontawesome = $css_url . 'external/font-awesome.min.css';
		wp_enqueue_style( 'fontawesome', $fontawesome, array(), $version );
		$bbm_modal_css = $css_url . 'bbm.modal.css';
		wp_enqueue_style( 'brainpress-modal-css', $bbm_modal_css, array(), $version );
		// Front CSS
		wp_enqueue_style( 'brainpress-front-css', $css_url . 'front.css', array( 'dashicons', 'wp-mediaelement' ), $version );

		wp_enqueue_script( 'comment-reply' );

		$script = $script_url . 'external/circle-progress.min.js';
		wp_enqueue_script( 'circle-progress', $script, array( 'jquery' ), $version );

		$modal_script_url = $script_url . 'external/backbone.modal-min.js';
		wp_enqueue_script( 'brainpress-backbone-modal', $modal_script_url, array( 'jquery', 'backbone', 'underscore', 'password-strength-meter' ) );

		$deps = array( 'underscore', 'wp-mediaelement' );
		wp_enqueue_script( 'brainpress-front-js', $script_url . 'front.js', $deps, $version );

		$localize_array = array(
			'_ajax_url' => BrainPress_Helper_Utility::get_ajax_url(),
			'cpnonce' => wp_create_nonce( 'brainpress_nonce' ),
			'allowed_video_extensions' => wp_get_video_extensions(),
			'allowed_audio_extensions' => wp_get_audio_extensions(),
			'allowed_image_extensions' => BrainPress_Helper_Utility::get_image_extensions(),
			'allowed_extensions' => apply_filters( 'brainpress_custom_allowed_extensions', false ),
			'allowed_student_extensions' => BrainPress_Helper_Utility::allowed_student_mimes(),
			'no_browser_upload' => __( 'Bitte versuche es mit einem anderen Browser, um Deine Datei hochzuladen.', 'brainpress' ),
			'invalid_upload_message' => __( 'Ungültiges Dateiformat!', 'brainpress' ),
			'file_uploaded_message' => __( 'Deine Datei wurde erfolgreich übermittelt.', 'brainpress' ),
			'file_upload_fail_message' => __( 'Beim Verarbeiten Deiner Datei ist ein Problem aufgetreten.', 'brainpress' ),
			'response_saved_message' => __( 'Deine Antwort wurde erfolgreich aufgezeichnet.', 'brainpress' ),
			'response_fail_message' => __( 'Beim Speichern Deiner Antwort ist ein Problem aufgetreten. Bitte lade diese Seite neu und versuche es erneut.', 'brainpress' ),
			'current_course_is_paid' => BrainPress_Data_Course::is_paid_course( $course_id )? 'yes':'no',
			'current_course_type' => BrainPress_Data_Course::get_setting( $course_id, 'enrollment_type', 'manually' ),
			'course_url' => get_permalink( BrainPress_Helper_Utility::the_course( true ) ),
			'home_url' => home_url(),
			'current_student' => get_current_user_id(),
			'workbook_view_answer' => __( 'Ansehen', 'brainpress' ),
			'labels' => BrainPress_Helper_UI_Module::get_labels(),
			'signup_errors' => array(
				'all_fields' => __( 'Alle Felder sind erforderlich.', 'brainpress' ),
				'email_invalid' => __( 'Ungültige E-Mail-Adresse.', 'brainpress' ),
				'email_exists' => __( 'Diese E-Mail-Adresse ist bereits vergeben.', 'brainpress' ),
				'user_exists' => __( 'Dieser Benutzername ist bereits vergeben.', 'brainpress' ),
				'weak_password' => __( 'Schwache Passwörter nicht erlaubt.', 'brainpress' ),
				'mismatch_password' => __( 'Passwörter stimmen nicht überein.', 'brainpress' ),
			),
			'login_errors' => array(
				'required' => __( 'Dein Benutzername und/oder Passwort ist erforderlich!', 'brainpress' ),
			),
			'comments' => array(
				'require_valid_comment' => __( 'Ups! Du hast nichts geschrieben!', 'brainpress' ),
			),
			'server_error' => __( 'Während der Verarbeitung tritt ein unerwarteter Fehler auf. Bitte versuche es erneut.', 'brainpress' ),
			'module_error' => array(
				'required' => __( 'Du musst dieses Modul ausfüllen!', 'brainpress' ),
				'normal_required' => __( 'Du musst alle erforderlichen Module ausfüllen!', 'brainpress' ),
				'participate' => __( 'Deine Teilnahme an der Diskussion ist erforderlich!', 'brainpress' ),
				'passcode_required' => __( 'PASSCODE eingeben!', 'brainpress' ),
				'invalid_passcode' => __( 'Ungültiger PASSCODE!', 'brainpress' ),
			),
			'confirmed_withdraw' => __( 'Bitte bestätige, dass Du vom Kurs zurücktreten möchtest. Wenn Du dich zurückziehst, werden auch alle Deine Unterlagen und der Zugang zu diesem Kurs entfernt.', 'brainpress' ),
			'confirmed_edit' => __( 'Bitte bestätige, dass Du diesen Kurs bearbeiten möchtest.', 'brainpress' ),
			'buttons' => array(
				'ok' => __( 'OK', 'brainpress' ),
				'cancel' => __( 'Abbrechen', 'brainpress' ),
			),
			'password_strength_meter_enabled' => BrainPress_Helper_Utility::is_password_strength_meter_enabled()
		);

		/**
		 * Filter localize script to allow data insertion.
		 *
		 * @since 2.0
		 *
		 * @param (array) $localize_array.
		 **/
		$localize_array = apply_filters( 'brainpress_localize_object', $localize_array );

		wp_localize_script( 'brainpress-front-js', '_brainpress', $localize_array );

		$script = BrainPress::$url . 'asset/js/external/video.min.js';
		wp_enqueue_script(
			'brainpress-video-js',
			$script,
			array('jquery'),
			BrainPress::$version
		);

		$script = BrainPress::$url . 'asset/js/external/video-youtube.min.js';
		wp_enqueue_script(
			'brainpress-video-youtube-js',
			$script,
			array('jquery', 'brainpress-video-js'),
			BrainPress::$version
		);

		$script = BrainPress::$url . 'asset/js/external/videojs-vimeo.min.js';
		wp_enqueue_script(
			'brainpress-videojs-vimeo-js',
			$script,
			array('jquery', 'brainpress-video-js'),
			'3.0.0'
		);

		$video_js = BrainPress::$url . 'asset/css/external/video-js.min.css';
		wp_enqueue_style(
			'brainpress-video-js-style',
			$video_js,
			array(),
			BrainPress::$version
		);
	}

	/**
	 * brainpress-front_js
	 *
	 * @since 2.0.7
	 */
	public function brainpress_front_js() {
		$script = BrainPress::$url . 'asset/js/brainpress-front.js';
		wp_enqueue_script(
			'brainpress_front',
			$script,
			array(
				'jquery',
				'jquery-ui-dialog',
				'underscore',
				'backbone',
			),
			BrainPress::$version,
			true
		);
	}
}
