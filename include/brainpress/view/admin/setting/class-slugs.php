<?php

require_once dirname( __FILE__ ) . '/class-settings.php';

class BrainPress_View_Admin_Setting_Slugs extends BrainPress_View_Admin_Setting_Setting {

	public static function init() {

		add_action( 'brainpress_settings_process_slugs', array( __CLASS__, 'process_form' ), 10, 2 );
		add_filter( 'brainpress_settings_render_tab_slugs', array( __CLASS__, 'return_content' ), 10, 3 );
		add_filter( 'brainpress_settings_tabs', array( __CLASS__, 'add_tabs' ) );

	}

	public static function add_tabs( $tabs ) {

		self::$slug = 'slugs';
		$tabs[ self::$slug ] = array(
			'title' => __( 'Slugs', 'brainpress' ),
			'description' => sprintf( __( 'Ein Slug sind ein paar Wörter, die einen Beitrag oder eine Seite beschreiben. Slugs sind normalerweise eine URL-freundliche Version des Post-Titels (der automatisch von ClassicPress generiert wurde), aber ein Slug kann beliebig sein. Slugs sollen mit %s verwendet werden, da sie den Inhalt der URL beschreiben. Post Slug ersetzt den Platzhalter %s in einer benutzerdefinierten Permalink-Struktur.', 'brainpress' ), '<a href="options-permalink.php">Permalinks</a>', '<strong>"%posttitle%"</strong>' ),
			'order' => 3,
		);

		return $tabs;

	}

	public static function return_content( $content, $slug, $tab ) {

		$my_course_prefix = __( 'mein-kurs', 'brainpress' );
		$my_course_prefix = sanitize_text_field( BrainPress_Core::get_setting( 'slugs/course', 'courses' ) ) . '/'. $my_course_prefix;

		$home_url = trailingslashit( esc_url( home_url() ) );
		$my_course_url = $home_url . trailingslashit( esc_html( $my_course_prefix ) );

		$content = '';

		$content .= self::page_start( $slug, $tab );
		$content .= self::table_start();

		$content .= self::row(
			__( 'Kurse Slug', 'brainpress' ),
			esc_html( $home_url ) . '&nbsp;<input type="text" name="brainpress_settings[slugs][course]" id="course_slug" value="' . esc_attr( BrainPress_Core::get_setting( 'slugs/course', 'courses' ) ).'" />&nbsp;/',
			esc_html__( 'Deine Kurs-URL sieht so aus: ', 'brainpress' ) . esc_html( $home_url ) . esc_html( BrainPress_Core::get_setting( 'slugs/course', 'courses' ) ) . esc_html__( '/mein-kurs/', 'brainpress' )
		);

		$content .= self::row(
			__( 'Kurskategorie Slug', 'brainpress' ),
			esc_html( $home_url . trailingslashit( esc_html( BrainPress_Core::get_setting( 'slugs/course', 'courses' ) ) ) ) . '&nbsp;<input type="text" name="brainpress_settings[slugs][category]" id="category_slug" value="' . esc_attr( BrainPress_Core::get_setting( 'slugs/category', 'course_category' ) ) . '" />&nbsp;/',
			esc_html__( 'Deine Kurs-URL sieht so aus: ', 'brainpress' ) . $home_url . esc_html( BrainPress_Core::get_setting( 'slugs/course', 'courses' ) . '/' . BrainPress_Core::get_setting( 'slugs/category', 'course_category' ) ) . esc_html__( '/your-category/', 'brainpress' )
		);

		$content .= self::row(
			__( 'Einheiten Slug', 'brainpress' ),
			$my_course_url . '&nbsp;<input type="text" name="brainpress_settings[slugs][units]" id="units_slug" value="' . esc_attr( BrainPress_Core::get_setting( 'slugs/units', 'units' ) ) . '" />&nbsp;/'
		);

		$content .= self::row(
			__( 'Kursbenachrichtigungen Slug', 'brainpress' ),
			$my_course_url . '&nbsp;<input type="text" name="brainpress_settings[slugs][notifications]" id="notifications_slug" value="' . esc_attr( BrainPress_Core::get_setting( 'slugs/notifications', 'notifications' ) ) . '" />&nbsp;/'
		);

		$content .= self::row(
			__( 'Kursdiskussionen Slug', 'brainpress' ),
			$my_course_url . '&nbsp;<input type="text" name="brainpress_settings[slugs][discussions]" id="discussions_slug" value="' . esc_attr( BrainPress_Core::get_setting( 'slugs/discussions', 'discussion' ) ) . '" />&nbsp;/'
		);

		$content .= self::row(
			__( 'Kurs Neue Diskussion Slug', 'brainpress' ),
			$my_course_url . trailingslashit( esc_attr( BrainPress_Core::get_setting( 'slugs/discussions', 'discussion' ) ) ) .'&nbsp;<input type="text" name="brainpress_settings[slugs][discussions_new]" id="discussions_new_slug" value="' . esc_attr( BrainPress_Core::get_setting( 'slugs/discussions_new', 'add_new_discussion' ) ) . '" />&nbsp;/'
		);

		$content .= self::row(
			__( 'Kurse Bewertungen Slug', 'brainpress' ),
			$my_course_url . '&nbsp;<input type="text" name="brainpress_settings[slugs][grades]" id="grades_slug" value="' . esc_attr( BrainPress_Core::get_setting( 'slugs/grades', 'grades' ) ) . '" />&nbsp;/'
		);

		$content .= self::row(
			__( 'Kurs Arbeitsmappe Slug', 'brainpress' ),
			trailingslashit( esc_url( home_url() ) ) . trailingslashit( esc_html( $my_course_prefix ) ) . '&nbsp;<input type="text" name="brainpress_settings[slugs][workbook]" id="workbook_slug" value="' . esc_attr( BrainPress_Core::get_setting( 'slugs/workbook', 'workbook' ) ) . '" />&nbsp;/'
		);

		$content .= self::row(
			__( 'Einschreibungsprozess Slug', 'brainpress' ),
			trailingslashit( esc_url( home_url() ) ) . '&nbsp;<input type="text" name="brainpress_settings[slugs][enrollment]" id="enrollment_slug" value="' . esc_attr( BrainPress_Core::get_setting( 'slugs/enrollment', 'enrollment_process' ) ) . '" />&nbsp;/'
		);

		$content .= self::row(
			__( 'Kursleiter Profil Slug', 'brainpress' ),
			trailingslashit( esc_url( home_url() ) ) . '&nbsp;<input type="text" name="brainpress_settings[slugs][instructor_profile]" id="instructor_profile_slug" value="' . esc_attr( BrainPress_Core::get_setting( 'slugs/instructor_profile', 'instructor' ) ) . '" />&nbsp;/'
		);

		$content .= self::table_end();
		return $content;

	}
}
