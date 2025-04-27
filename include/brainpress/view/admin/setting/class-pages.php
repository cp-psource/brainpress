<?php

require_once dirname( __FILE__ ) . '/class-settings.php';

class BrainPress_View_Admin_Setting_Pages extends BrainPress_View_Admin_Setting_Setting {

	public static function init() {

		add_action( 'brainpress_settings_process_pages', array( __CLASS__, 'process_form' ), 10, 2 );
		add_filter( 'brainpress_settings_render_tab_pages', array( __CLASS__, 'return_content' ), 10, 3 );
		add_filter( 'brainpress_settings_tabs', array( __CLASS__, 'add_tabs' ) );

	}

	public static function add_tabs( $tabs ) {

		self::$slug = 'pages';
		$tabs[ self::$slug ] = array(
			'title' => __( 'BrainPress Seiten', 'brainpress' ),
			'description' => __( 'Konfiguriere die Seiten für BrainPress.', 'brainpress' ),
			'order' => 1,
		);

		return $tabs;

	}

	public static function return_content( $content, $slug, $tab ) {

		$page_dropdowns = array();

		$pages_args = array(
			'selected' => BrainPress_Core::get_setting( 'pages/enrollment', 0 ),
			'echo' => 0,
			'show_option_none' => __( '&mdash; Select &mdash;', 'brainpress' ),
			'option_none_value' => 0,
			'name' => 'brainpress_settings[pages][enrollment]',
		);
		$page_dropdowns['enrollment'] = wp_dropdown_pages( $pages_args );

		$pages_args['selected'] = BrainPress_Core::get_setting( 'pages/login', 0 );
		$pages_args['name'] = 'brainpress_settings[pages][login]';
		$page_dropdowns['login'] = wp_dropdown_pages( $pages_args );

		$pages_args['selected'] = BrainPress_Core::get_setting( 'pages/signup', 0 );
		$pages_args['name'] = 'brainpress_settings[pages][signup]';
		$page_dropdowns['signup'] = wp_dropdown_pages( $pages_args );

		$pages_args['selected'] = BrainPress_Core::get_setting( 'pages/student_dashboard', 0 );
		$pages_args['name'] = 'brainpress_settings[pages][student_dashboard]';
		$page_dropdowns['student_dashboard'] = wp_dropdown_pages( $pages_args );

		$pages_args['selected'] = BrainPress_Core::get_setting( 'pages/student_settings', 0 );
		$pages_args['name'] = 'brainpress_settings[pages][student_settings]';
		$page_dropdowns['student_settings'] = wp_dropdown_pages( $pages_args );

		$pages_args['selected'] = BrainPress_Core::get_setting( 'pages/instructor', 0 );
		$pages_args['name'] = 'brainpress_settings[pages][instructor]';
		$page_dropdowns['instructor'] = wp_dropdown_pages( $pages_args );

		$content = '';

		$content .= self::page_start( $slug, $tab );
		$content .= self::table_start();

		/**
		 * Student Dashboard
		 */
		$content .= self::row(
			__( 'Studenten-Dashboard', 'brainpress' ),
			$page_dropdowns['student_dashboard'],
			__( 'Wähle eine Seite aus, auf der die Studenten Kurse anzeigen können.', 'brainpress' )
		);

		/**
		 * Student Settings
		 */
		$content .= self::row(
			__( 'Studenten Einstellungen', 'brainpress' ),
			$page_dropdowns['student_settings'],
			__( 'Wähle eine Seite aus, auf der die Studenten ihre Kontoeinstellungen ändern können.', 'brainpress' )
		);

		/**
		 * login
		 */
		$content .= self::row(
			__( 'Einloggen', 'brainpress' ),
			$page_dropdowns['login'],
			__( 'Wähle eine Seite aus, auf der sich die Studenten anmelden können.', 'brainpress' )
		);

		/**
		 * Signup
		 */
		$content .= self::row(
			__( 'Registrierung', 'brainpress' ),
			$page_dropdowns['signup'],
			__( 'Wähle eine Seite aus, auf der die Studenten ein Konto erstellen können.', 'brainpress' )
		);

		/**
		 * Instructor.
		 */
		$content .= self::row(
			__( 'Kursleiter', 'brainpress' ),
			$page_dropdowns['instructor'],
			__( 'Wähle eine Seite aus, auf der das Kursleiterprofil angezeigt wird.', 'brainpress' )
		);

		/**
		 * Enrollment
		 */
		$content .= self::row(
			__( 'Einschreibungsprozess Seite', 'brainpress' ),
			$page_dropdowns['enrollment'],
			sprintf( __( 'Wähle eine Seite aus, auf der der Registrierungsprozess angezeigt wird.', 'brainpress' ) . '</a>' )
		);
		$content .= self::table_end();
		return $content;

	}
}
