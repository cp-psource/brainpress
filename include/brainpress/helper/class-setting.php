<?php

class BrainPress_Helper_Setting {

	protected static $page_refs = array();
	protected static $valid_pages = array();
	protected static $pages = array();
	protected static $default_capability = null;
	protected static $message_meta_name = 'brainpress_migration_message';

	public static function init() {
		add_action( 'plugins_loaded', array( __CLASS__, 'admin_plugins_loaded' ) );
		//add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );
		//add_action( 'shutdown', array( __CLASS__, 'update_post_meta' ) );
		/** This filter is documented in /wp-admin/includes/misc.php */
		add_filter( 'set-screen-option', array( __CLASS__, 'set_screen_option' ), 10, 3 );
		add_filter( 'screen_settings', array( __CLASS__, 'screen_settings' ), 10, 2 );
		add_filter( 'default_hidden_columns', array( __CLASS__, 'default_hidden_columns' ), 10, 2 );
		add_filter( 'the_title', array( 'BrainPress_Data_Course', 'add_numeric_identifier_to_course_name' ), 10, 2 );
	}

	/**
	 * Return the default capability required to see the BrainPress menu.
	 *
	 * @since 2.0.0
	 * @return string default capability
	 */
	protected static function get_default_capability() {
		if ( empty( self::$default_capability ) ) {
			$userdata = get_userdata( get_current_user_id() );

			if ( current_user_can( 'manage_options' ) ) {
				self::$default_capability = 'manage_options';
			} else {
				self::$default_capability = 'brainpress_dashboard_cap';
			}

			/**
			 * Filer allow to change default capability.
			 *
			 * @since 2.0.0
			 *
			 * @param string $capability BrainPress capability.
			 * @param string $slug BrainPress page slug
			 *
			 */
			self::$default_capability = apply_filters(
				'brainpress_default_capability',
				self::$default_capability
			);
		}
		return self::$default_capability;
	}

	public static function admin_menu() {
		if ( ! BrainPress_Data_Capabilities::can_manage_courses() ) {
			// Admin menu is only available to admin/instructors.
			return;
		}

		$parent_handle = 'brainpress';
		$capability = self::get_default_capability();

		$page = self::$page_refs[ $parent_handle ] = add_menu_page(
			BrainPress::$name,
			BrainPress::$name,
			$capability,
			$parent_handle,
			array( __CLASS__, 'menu_handler' ),
			BrainPress::$url . 'asset/img/brainpress-icon.png'
		);

		add_action( 'load-' . $page, array( __CLASS__, 'add_screen_options' ) );

		$pages = self::_get_pages();

		foreach ( $pages as $handle => $page ) {
			// Use default callback if not defined
			if ( empty( $page['callback'] ) ) {
				$callback = array( __CLASS__, 'menu_handler' );
			} else {
				$callback = $page['callback'];
			}

			// Remove callback to use URL instead
			if ( 'none' == $callback ) {
				$callback = '';
			}

			// Use default capability if not defined
			if ( empty( $page['cap'] ) ) {
				$capability = self::get_default_capability();
			} else {
				$capability = $page['cap'];
			}

			if ( empty( $page['parent'] ) ) {
				$page['parent'] = $parent_handle;
			}

			if ( empty( $page['handle'] ) ) {
				$page['handle'] = $handle;
			}

			if ( 'none' != $page['parent'] ) {
				$parent = $page['parent'];
			} else {
				$parent = null;
			}

			$add_page_handler = self::$page_refs[ $handle ] = add_submenu_page(
				$parent,
				$page['title'],
				$page['menu_title'],
				$capability,
				$page['handle'],
				$callback
			);

			/**
			 * load callback
			 */
			if ( isset( $page['load_action_callback'] ) && is_callable( $page['load_action_callback'] ) ) {
				add_action( 'load-' . $add_page_handler, $page['load_action_callback'] );
			}
		}
	}

	public static function menu_handler() {
		$page = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : 'brainpress';

		if ( in_array( $page, self::get_valid_pages() ) ) {
			do_action( 'brainpress_admin_render_page' );
			do_action( 'brainpress_admin_' . $page );
		}
	}

	public static function get_valid_pages() {
		return apply_filters( 'brainpress_admin_valid_pages', self::$valid_pages );
	}

	public static function get_page_references() {
		return self::$page_refs;
	}

	public static function reorder_menu( $a, $b ) {
		return $a > $b;
	}

	/**
	 * Internal helper function used by array_map() to get numeric order values.
	 *
	 * @since  2.0.0
	 * @param  array $a
	 * @return int
	 */
	public static function _page_order( $a ) {
		if ( empty( $a['order'] ) ) {
			return 0;
		} else {
			return (int) $a['order'];
		}
	}

	protected static function _get_pages() {
		$pages = apply_filters( 'brainpress_admin_pages', self::$pages );
		$order = array_map( array( __CLASS__, '_page_order' ), $pages );
		$max_order = max( $order );
		$new_order = array();

		foreach ( $pages as $key => $page ) {
			$page_order = ! empty( $page['order'] ) ? $page['order'] : ( $max_order += 5 );
			$page['order'] = $page_order;
			$pages[ $key ] = $page;
			$new_order[ $page_order ] = $key;
		}

		uksort( $new_order, array( __CLASS__, 'reorder_menu' ) );
		$new_pages = array();
		foreach ( $new_order as $order => $key ) {
			$new_pages[ $key ] = $pages[ $key ];
		}

		return $new_pages;
	}

	public static function admin_init() {

		$page = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : 'brainpress';
		if ( 'course' == get_post_type() || in_array( $page, self::get_valid_pages() ) ) {
			do_action( 'brainpress_settings_page_pre_render_' . $page );
		}

		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_style' ) );
		add_filter( 'brainpress_custom_allowed_extensions', array( __CLASS__, 'allow_zip_extension' ) );
		add_action( 'before_delete_post', array( 'BrainPress_Data_Course', 'delete_course_number' ) );
	}

	public static function admin_plugins_loaded() {
		do_action( 'brainpress_settings_page_pre_render' );
	}

	public static function admin_style() {
		global $typenow;

		if ( 'course' !== $typenow ) {
			// Include css needed for menu-icon
			wp_enqueue_style( 'brainpress-menu', BrainPress::$url . 'asset/css/admin-menu.min.css', false, BrainPress::$version );

			return;
		}

		$style = BrainPress::$url . 'asset/css/admin-general.css';
		$style_global = BrainPress::$url . 'asset/css/admin-global.css';
		$script = BrainPress::$url . 'asset/js/admin-general.js';
		$sticky = BrainPress::$url . 'asset/js/external/sticky.min.js';
		$editor_style = BrainPress::$url . 'asset/css/editor.css';
		$fontawesome = BrainPress::$url . 'asset/css/external/font-awesome.min.css';

		$page = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : '';

		if ( 'course' == get_post_type() || in_array( $page, self::get_valid_pages() ) ) {
			wp_enqueue_style( 'brainpress_admin_general', $style, array( 'dashicons' ), BrainPress::$version );
			wp_enqueue_script( 'brainpress_admin_general_js', $script, array( 'jquery' ), BrainPress::$version, true );
			wp_enqueue_script( 'sticky_js', $sticky, array( 'jquery' ), BrainPress::$version, true );

			add_editor_style( $editor_style );

			// Add chosen.
			$style = BrainPress::$url . 'asset/css/external/chosen.min.css';
			$script = BrainPress::$url . 'asset/js/external/chosen.jquery.min.js';
			wp_enqueue_style( 'chosen_css', $style, array( 'dashicons' ), BrainPress::$version );
			wp_enqueue_script( 'chosen_js', $script, array( 'jquery' ), BrainPress::$version, true );

			// Font Awesome.
			wp_enqueue_style( 'fontawesome', $fontawesome, array(), BrainPress::$version );

			// Add instructor stylesheet
			if ( BrainPress_View_Admin_Instructor::$slug == $page ) {
				$instructor_css = BrainPress::$url . 'asset/css/admin-instructor.css';
				wp_enqueue_style( 'brainpress_admin_instructor', $instructor_css, false, BrainPress::$version );
			}

			// Add student stylesshet
			$slug = BrainPress_View_Admin_Student::get_slug();
			if ( $slug == $page ) {
				$student_css = BrainPress::$url . 'asset/css/admin-student.css';
				wp_enqueue_style( 'brainpress_admin_student', $student_css, false, BrainPress::$version );
			}

			// Add timepicker
			$slug = BrainPress_View_Admin_Course_Edit::get_slug();
			if ( $slug == $page ) {
				$timepicker_css = BrainPress::$url . 'asset/css/external/jquery-ui-timepicker-addon.min.css';
				$timepicker_js = BrainPress::$url . 'asset/js/external/jquery-ui-timepicker-addon.min.js';
				wp_enqueue_style( 'brainpress_admin_timepicker', $timepicker_css, false, BrainPress::$version );
				wp_enqueue_script( 'brainpress_admin_timepicker', $timepicker_js, array( 'jquery-ui-slider', 'jquery-ui-datepicker' ), BrainPress::$version, true );
			}
		}

		wp_enqueue_style( 'brainpress_admin_global', $style_global, array(), BrainPress::$version );
	}

	public static function allow_zip_extension( $extensions ) {

		if ( empty( $extensions ) ) {
			$extensions = array();
		}

		$extensions[] = 'zip';

		return $extensions;

	}

	/**
	 * Function return value for BrainPress options.
	 *
	 * @since 2.0.0
	 *
	 * @param bool|int $value Screen option value. Default false to skip.
	 * @param string $option The option name.
	 * @param integer $value The number of rows to use.
	 *
	 * @return mixed value or status.
	 */
	public static function set_screen_option( $status, $option, $value ) {
		if ( 'brainpress_courses_per_page' == $option ) {
			return $value;
		}
		if ( preg_match( '/^brainpress_/', $option ) ) {
			return $value;
		}
		return $status;
	}

	/**
	 * update post meta.
	 *
	 * This is a one-time-upgrade function that converts data in the post-meta
	 * table to keep it compatible with recent changes.
	 *
	 *	   ****************************************
	 * @todo  REMOVE IT IN INITIAL 2.0 RELEASE!!!!!
	 *	   ****************************************
	 *
	 * @since 2.0.0.
	 * DEPRACATED!!!
	 */
	public static function update_post_meta() {

		/**
		 * check and if it is done, then do not run
		 */
		$update_status = get_option( 'brainpress_update_course_date_status', 'need-patch' );
		if ( 'done' == $update_status ) {
			return;
		}

		$args = array(
			'post_type' => 'course',
			'post_status' => 'any',
			'meta_key' => 'course_start_date',
			'meta_compare' => 'NOT EXISTS',
			'fields' => 'ids',
			'posts_per_page' => 20,
		);

		$ids = get_posts( $args );

		/**
		 * whe all posts are updated, then stop doing it
		 */
		if ( empty( $ids ) ) {
			add_option( 'brainpress_update_course_date_status', 'done' );
			return;
		}

		foreach ( $ids as $course_id ) {
			$start_date = BrainPress_Data_Course::get_setting( $course_id, 'course_start_date', true );
			$start_date = intval( strtotime( $start_date ) );
			update_post_meta( $course_id, 'course_start_date', $start_date );
		}
	}

	/**
	 * add screen options for courses list
	 *
	 * @since 2.0.0
	 */
	public static function add_screen_options() {
		add_screen_option(
			'columns',
			array(
				'default' => '',
				'label' => _x( 'Spalten','Kurse pro Seite (Bildschirmoptionen)', 'brainpress' ),
				'option' => 'brainpress_courses_columns',
			)
		);
		add_screen_option(
			'per_page',
			array(
				'default' => 20,
				'label' => _x( 'Anzahl der Kurse pro Seite:','Kurse pro Seite (Bildschirmoptionen)', 'brainpress' ),
				'option' => 'brainpress_courses_per_page',
			)
		);
	}

	/**
	 * get columns names
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @param string $option Option if we need only keys.
	 * @return array Array of columns.
	 */
	private static function courses_get_columns( $option = '' ) {
		$columns = array(
			'ID' => __( 'ID', 'brainpress' ),
			'categories' => __( 'Kategorien', 'brainpress' ),
			'date_start' => __( 'Anfangsdatum', 'brainpress' ),
			'date_end' => __( 'Endtermin', 'brainpress' ),
			'date_enrollment_start' => __( 'Einschreibungsbeginn', 'brainpress' ),
			'date_enrollment_end' => __( 'Einschreibungsende', 'brainpress' ),
			'units' => __( 'Einheiten', 'brainpress' ),
			'students' => __( 'Studenten', 'brainpress' ),
			'certificates' => __( 'Zertifiziert', 'brainpress' ),
			'status' => __( 'Status', 'brainpress' ),
			'actions' => __( 'Aktionen', 'brainpress' ),
		);
		if ( 'keys-only' == $option ) {
			$columns = array_keys( $columns );
		}
		return $columns;
	}

	/**
	 * Based on columns and user columns prepare hidden columns.
	 *
	 * @since 2.0.0
	 *
	 * @return array Array of columns.
	 */
	public static function get_hidden_columns() {
		$screen = get_current_screen();
		$hidden = get_hidden_columns( $screen );
		return $hidden;
	}

	/**
	 * Screen configuration html. For columns.
	 *
	 * @since 2.0.0
	 *
	 */
	public static function screen_settings( $content, $args ) {
		if ( 'toplevel_page_brainpress' == $args->base ) {
			$columns_names = self::courses_get_columns();
			$hidden = self::get_hidden_columns();
			$content .= '<fieldset class="metabox-prefs">';
			$content .= sprintf( '<legend>%s</legend>', __( 'Spalten', 'brainpress' ) );
			$content .= '<div class="metabox-prefs">';
			foreach ( $columns_names as $key => $name ) {
				$check = in_array( $key, $hidden ) ? 'off' : 'on';
				$content .= sprintf(
					'<label><input class="hide-column-tog" type="checkbox" value="%s" name="columns[%s]" %s /> %s</label>',
					$key,
					$key,
					checked( 'on', $check, false ),
					$name
				);
			}
			$content .= '</fieldset><br class="clear">';
		}
		return $content;
	}

	/**
	 * Get default hidden columns.
	 *
	 * @since 2.0.0
	 *
	 * @param array $hidden Array of hidden columns.
	 * @param WP_Screen $screen The current screen object.
	 */
	public static function default_hidden_columns( $hidden, $screen ) {

		if ( 'toplevel_page_brainpress' == $screen->id ) {
			$hidden = array(
				'certificates',
				'date_end',
				'date_enrollment_end',
				'date_enrollment_start',
				'date_start',
				'ID',
				'students',
			);
		}

		return $hidden;
	}
}
