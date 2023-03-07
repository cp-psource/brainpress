<?php

class BrainPress_View_Admin_Setting_Shortcodes {

	public static function init() {
		add_filter(
			'brainpress_settings_tabs',
			array( __CLASS__, 'add_tabs' )
		);
		add_action(
			'brainpress_settings_process_shortcodes',
			array( __CLASS__, 'process_form' ),
			10, 2
		);
		add_filter(
			'brainpress_settings_render_tab_shortcodes',
			array( __CLASS__, 'return_content' ),
			10, 3
		);
	}

	public static function add_tabs( $tabs ) {
		$tabs['shortcodes'] = array(
			'title' => __( 'Shortcodes', 'brainpress' ),
			'description' => __( 'Mit Shortcodes kannst Du dynamische Inhalte in Posts und Seiten Deiner Webseite einfügen. Gib sie einfach ein oder füge sie in Deinen Beitrags- oder Seiteninhalt ein, wo sie angezeigt werden sollen. Optionale Attribute können in einem Format wie <em>[shortcode attr1="value" attr2="value"]</em> hinzugefügt werden.', 'brainpress' ),
			'order' => 50,
			'buttons' => 'none',
		);

		return $tabs;
	}

	public static function return_content( $content, $slug, $tab ) {
		$content = 'shortcodes!';
		$boxes = self::_boxes();

		ob_start();
		?>
		<div class="shortcodes-list">
			<?php foreach ( $boxes as $group => $data ) : ?>
            <div class="cp-content-box <?php echo esc_attr( $group ); ?>" id="shortcode-<?php echo esc_attr( $group ); ?>">
				<h3 class="hndle">
					<span><?php echo esc_html( $data['title'] ); ?></span>
				</h3>
				<div class="inside"><?php echo $data['content']; ?></div>
			</div>
			<?php endforeach; ?>
		</div>
		<?php

		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

	public static function process_form() {
	}

	private static function _boxes() {
		$boxes = array(
			'course_instructors' => array(
				'title' => __( 'Kursleiter Liste', 'brainpress' ),
				'content' => self::_box_course_instructors(),
			),
			'course_instructor_avatar' => array(
				'title' => __( 'Kursleiter Avatar', 'brainpress' ),
				'content' => self::_box_course_instructor_avatar(),
			),
			'instructor_profile_url' => array(
				'title' => __( 'Kursleiter Profil URL', 'brainpress' ),
				'content' => self::_box_instructor_profile_url(),
			),
			'course' => array(
				'title' => __( 'Kurse', 'brainpress' ),
				'content' => self::_box_course(),
			),
			'course_details' => array(
				'title' => __( 'Kursdetails', 'brainpress' ),
				'content' => self::_box_course_details(),
			),
			'course_title' => array(
				'title' => __( 'Kursname', 'brainpress' ),
				'content' => self::_box_course_title(),
			),
			'course_summary' => array(
				'title' => __( 'Kursübersicht', 'brainpress' ),
				'content' => self::_box_course_summary(),
			),
			'course_description' => array(
				'title' => __( 'Kursbeschreibung', 'brainpress' ),
				'content' => self::_box_course_description(),
			),
			'course_start' => array(
				'title' => __( 'Kursbeginn', 'brainpress' ),
				'content' => self::_box_course_start_date(),
			),
			'course_end' => array(
				'title' => __( 'Kursenddatum', 'brainpress' ),
				'content' => self::_box_course_end_date(),
			),
			'course_dates' => array(
				'title' => __( 'Kurstermine', 'brainpress' ),
				'content' => self::_box_course_dates(),
			),
			'course_enrollment_start' => array(
				'title' => __( 'Start der Kursanmeldung', 'brainpress' ),
				'content' => self::_box_course_enrollment_start(),
			),
			'course_enrollment_end' => array(
				'title' => __( 'Ende der Kursanmeldung', 'brainpress' ),
				'content' => self::_box_course_enrollment_end(),
			),
			'course_enrollment_dates' => array(
				'title' => __( 'Einschreibungstermine', 'brainpress' ),
				'content' => self::_box_course_enrollment_dates(),
			),
			'course_enrollment_type' => array(
				'title' => __( 'Art der Kurseinschreibung', 'brainpress' ),
				'content' => self::_box_course_enrollment_type(),
			),
			'course_class_size' => array(
				'title' => __( 'Kursklassengröße', 'brainpress' ),
				'content' => self::_box_course_class_size(),
			),
			'course_cost' => array(
				'title' => __( 'Kurskosten', 'brainpress' ),
				'content' => self::_box_course_cost(),
			),
			'course_time_estimation' => array(
				'title' => __( 'Schätzung der Kursdauer', 'brainpress' ),
				'content' => self::_box_course_time_estimation(),
			),
			'course_language' => array(
				'title' => __( 'Kurssprache', 'brainpress' ),
				'content' => self::_box_course_language(),
			),
			'course_list_image' => array(
				'title' => __( 'Kurslistenbild', 'brainpress' ),
				'content' => self::_box_course_list_image(),
			),
			'course_featured_video' => array(
				'title' => __( 'Kurs Empfohlenes Video', 'brainpress' ),
				'content' => self::_box_course_featured_video(),
			),
			'course_media' => array(
				'title' => __( 'Kursmedien', 'brainpress' ),
				'content' => self::_box_course_media(),
			),
			'course_join_button' => array(
				'title' => __( 'Kurs beitreten Schaltfläche', 'brainpress' ),
				'content' => self::_box_course_join_button(),
			),
			'course_action_links' => array(
				'title' => __( 'Links zu Kursaktionen', 'brainpress' ),
				'content' => self::_box_course_action_links(),
			),
			'course_calendar' => array(
				'title' => __( 'Kurskalender', 'brainpress' ),
				'content' => self::_box_course_calendar(),
			),
			'course_list' => array(
				'title' => __( 'Kursliste', 'brainpress' ),
				'content' => self::_box_course_list(),
			),
			'course_featured' => array(
				'title' => __( 'Empfohlener Kurs', 'brainpress' ),
				'content' => self::_box_course_featured(),
			),
			'course_structure' => array(
				'title' => __( 'Kursstruktur', 'brainpress' ),
				'content' => self::_box_course_structure(),
			),
			'course_signup' => array(
				'title' => __( 'Kurseinschreibungs-/Anmeldeseite', 'brainpress' ),
				'content' => self::_box_course_signup(),
			),
			'courses_student_dashboard' => array(
				'title' => __( 'Studenten Dashboard Template', 'brainpress' ),
				'content' => self::_box_courses_student_dashboard(),
			),
			'courses_student_settings' => array(
				'title' => __( 'Studenten Einstellungen Template', 'brainpress' ),
				'content' => self::_box_courses_student_settings(),
			),
		);
		ksort( $boxes );
		return $boxes;
	}

	/**
	 * Produce help box for course_instructors.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_instructors() {
		$data = array(
			'shortcode' => 'course_instructors',
			'content' => __( 'Zeigt eine Liste oder Anzahl der Instruktoren an (Gravatar, Name und Link zur Profilseite).', 'brainpress' ),
			'parameters' => array(
				'optional' => array(
					'course_id' => array(
						'content' => __( 'ID der Kursleiter werden zugewiesen (erforderlich, wenn sie außerhalb einer Schleife verwendet werden)', 'brainpress' ),
					),
					'style' => array(
						'content' => __( 'So werde Kursleiter angezeigt.', 'brainpress' ),
						'options' => array( 'block', 'default', 'list', 'list-flat', 'count' ),
						'options_description' => __( 'count - zählt Instruktoren für den Kurs.', 'brainpress' ),
					),
					'label' => array(
						'content' => __( 'Beschriftung, die für die Ausgabe angezeigt werden soll.', 'brainpress' ),
					),
					'label_plural' => array(
						'content' => __( 'Plural von mehr als einem Kursleiter.', 'brainpress' ),
						'default' => __( 'IKursleiter', 'brainpress' ),
					),
					'label_delimeter' => array(
						'content' => __( 'Symbol nach dem Etikett.', 'brainpress' ),
						'default' => ':',
					),
					'label_tag' => array(
						'content' => __( 'HTML-Tag zum Umschließen des Etiketts (ohne Klammern, z.B. <em>h3</em>).', 'brainpress' ),
						'default' => __( 'empty', 'brainpress' ),
					),
					'link_text' => array(
						'content' => __( 'Text zum Klicken, um auf vollständige Profile zu verlinken.', 'brainpress' ),
						'default' => __( 'Vollständiges Profil anzeigen', 'brainpress' ),
					),
					'show_label' => array(
						'content' => __( 'Zeige das Etikett.', 'brainpress' ),
						'options' => array( 'yes', 'no' ),
					),
					'summary_length' => array(
						'content' => __( 'Länge der Kursleiter-Biografie, zum anzeigen, wann Stil "Block" ist.', 'brainpress' ),
						'default' => __( 50, 'brainpress' ),
					),
					'list_separator' => array(
						'content' => __( 'Symbol zum Trennen von Kursleitern, wenn der Stil "Liste" oder "Liste flach" ist..', 'brainpress' ),
						'default' => ',',
					),
					'avatar_size' => array(
						'content' => __( 'Pixelgröße der Avatare beim Anzeigen im Blockmodus.', 'brainpress' ),
						'default' => __( 80, 'brainpress' ),
					),
					'default_avatar' => array(
						'content' => __( 'URL zu einem Standardbild, wenn der Benutzeravatar nicht gefunden werden kann.', 'brainpress' ),
					),
					'show_divider' => array(
						'content' => __( 'Setze eine Trennlinie zwischen den Kursleiterprofilen, wenn der Stil "Block" ist..', 'brainpress' ),
					),
					'link_all' => array(
						'content' => __( 'Mache das gesamte Instruktorprofil zu einem Link zum vollständigen Profil.', 'brainpress' ),
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_instructors]',
				'[course_instructors course_id="5"]',
				'[course_instructors style="list"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_instructor_avatar.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_instructor_avatar() {
		$data = array(
			'shortcode' => 'course_instructor_avatar',
			'content' => __( 'Zeigt den Avatar eines Kursleiiters an.', 'brainpress' ),
			'parameters' => array(
				'required' => array(
					'instructor_id' => array(
						'content' => __( 'Die Benutzer-ID des Kursleiters.', 'brainpress' ),
					),
				),
				'optional' => array(
					'force_display' => array(
						'content' => __( 'Ob immer das Standardbild angezeigt werden soll, niemals der Gravatar.', 'brainpress' ),
					),
					'thumb_size' => array(
						'content' => __( 'Größe des Avatar-Miniaturbilds.', 'brainpress' ),
						'default' => 80,
					),
					'class' => array(
						'content' => __( 'CSS-Klasse für den Avatar.', 'brainpress' ),
						'default' => 'small-circle-profile-image',
					),
				),
			),
			'examples' => array(
				'[course_instructor_avatar instructor_id="1"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for instructor_profile_url.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_instructor_profile_url() {
		$data = array(
			'shortcode' => 'instructor_profile_url',
			'content' => __( 'Gibt die URL zum Kursleiterprofil zurück.', 'brainpress' ),
			'parameters' => array(
				'required' => array(
					'instructor_id' => array(
						'content' => __( 'Die Benutzer-ID des Kursleiters.', 'brainpress' ),
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[instructor_profile_url instructor_id="1"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course() {
		$data = array(
			'shortcode' => 'course',
			'content' => __( 'Mit diesem Shortcode kannst Du Details zu Deinem Kurs anzeigen.', 'brainpress' ),
		   'note' => __( 'Dieselben Informationen können mithilfe der folgenden spezifischen Kurs-Shortcodes abgerufen werden.', 'brainpress' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'Wenn außerhalb der ClassicPress-Schleife.', 'brainpress' ),
					),
					'show' => array(
						'content' => __( 'Alle Felder, die Du anzeigen möchtest.', 'brainpress' ),
						'default' => 'summary',
						'options' => array( 'title', ' summary', ' description', ' start', ' end', ' dates', ' enrollment_start', ' enrollment_end', ' enrollment_dates', ' enrollment_type', ' class_size', ' cost', ' language', ' instructors', ' image', ' video', ' media', ' button', ' action_links', ' calendar', ' thumbnail' ),
					),
				),
				'optional' => array(
					'show_title' => array(
						'content' => __( 'Erforderlich, wenn das Feld "Titel" angezeigt wird.', 'brainpress' ),
						'defulat' => 'no',
						'options' => array( 'yes', 'no' ),
					),
					'date_format' => array(
						'content' => __( 'Datumsformat im PHP-Stil.', 'brainpress' ),
						'defulat' => 'WP',
					),
					'label_tag' => array(
						'content' => __( 'HTML-Tag (ohne Klammern) für die einzelnen Beschriftungen.', 'brainpress' ),
						'defulat' => 'strong',
					),
					'label_delimeter' => array(
						'content' => __( 'Symbol, das nach dem Etikett verwendet werden soll.', 'brainpress' ),
						'defulat' => ':',
					),
				),
			),
			'examples' => array(
				'[course show="title,summary,cost,button" course_id="5"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course details.
	 *
	 * @since 2.0.2
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_details() {
		$data = array(
			'shortcode' => 'course_details',
			'content' => __( 'Dieser Shortcode ist ein Alias für den [course] Shortcode. Weitere Informationen findest Du im Abschnitt [course] Shortcode.', 'brainpress' ),
			'examples' => array(
				'[course show="title,summary,cost,button" course_id="5"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_title.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_title() {
		$data = array(
			'shortcode' => 'course_title',
			'content' => __( 'Zeigt den Kurstitel an.', 'brainpress' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'Wenn außerhalb der ClassicPress-Schleife.', 'brainpress' ),
					),
				),
				'optional' => array(
					'title_tag' => array(
						'content' => __( 'Das HTML-Tag (ohne Klammern), das für den Titel verwendet werden soll.', 'brainpress' ),
						'default' => 'h3',
					),
					'link' => array(
						'content' => __( '.', 'brainpress' ),
						'default' => 'empty',
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_title course_id="4"]',
				'[course_title]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_summary.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_summary() {
		$data = array(
			'shortcode' => 'course_summary',
			'content' => __( 'Zeigt die Kurszusammenfassung/den Auszug an.', 'brainpress' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'Wenn außerhalb der ClassicPress-Schleife.', 'brainpress' ),
					),
				),
			),
			'examples' => array(
				'[course_summary course_id="4"]',
				'[course_summary]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_description.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_description() {
		$data = array(
			'shortcode' => 'course_description',
			'content' => __( 'Zeigt die längere Kursbeschreibung an (Beitragsinhalt).', 'brainpress' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'Wenn außerhalb der ClassicPress-Schleife.', 'brainpress' ),
					),
				),
				'optional' => array(
					'label' => array(
						'content' => __( 'Vor der Beschreibung wird ein zusätzliches Etikett angezeigt.', 'brainpress' ),
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_description course_id="4"]<br />[course_description]'
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_description.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_start_date() {
		$data = array(
			'shortcode' => 'course_start',
			'content' => __( 'Zeigt das Startdatum des Kurses an.', 'brainpress' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'Wenn außerhalb der ClassicPress-Schleife.', 'brainpress' ),
					),
				),
				'optional' => array(
					'date_format' => array(
						'content' => __( 'Datumsformat im PHP-Stil.', 'brainpress' ),
						'default' => 'wp',
					),
					'label' => array(
						'content' => __( 'Beschriftung, die für die Ausgabe angezeigt werden soll. Setze die Beschriftung auf "", um die Beschriftung vollständig auszublenden.', 'brainpress' ),
					),
					'label_tag' => array(
						'content' => __( 'HTML-Tag (ohne Klammern) für die einzelnen Beschriftungen.', 'brainpress' ),
						'default' => __( 'strong', 'brainpress' ),
					),
					'label_delimeter' => array(
						'content' => __( 'Symbol, das nach dem Etikett verwendet werden soll.', 'brainpress' ),
						'default' => ':',
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_start]',
				'[course_start label="Großartigkeit beginnt am" label_tag="h3"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_end.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_end_date() {
		$data = array(
			'shortcode' => 'course_end',
			'content' => __( 'Zeigt das Enddatum des Kurses an.', 'brainpress' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'Wenn außerhalb der ClassicPress-Schleife.', 'brainpress' ),
					),
				),
				'optional' => array(
					'date_format' => array(
						'content' => __( 'Datumsformat im PHP-Stil.', 'brainpress' ),
						'default' => 'wp',
						'description' => __( '<a href="https://codex.wordpress.org/Formatting_Date_and_Time">Dokumentation zur Datums- und Uhrzeitformatierung</a>.' ),
					),
					'label' => array(
						'content' => __( 'Beschriftung, die für die Ausgabe angezeigt werden soll. Setze die Beschriftung auf "", um die Beschriftung vollständig auszublenden.', 'brainpress' ),
					),
					'label_tag' => array(
						'content' => __( 'HTML-Tag (ohne Klammern) für die einzelnen Beschriftungen.', 'brainpress' ),
						'default' => __( 'strong', 'brainpress' ),
					),
					'label_delimeter' => array(
						'content' => __( 'Symbol, das nach dem Etikett verwendet werden soll.', 'brainpress' ),
						'default' => ':',
					),
					'no_date_text' => array(
						'content' => __( 'Text, der angezeigt wird, wenn der Kurs kein Enddatum hat.', 'brainpress' ),
						'default' => __( 'Kein Enddatum', 'brainpress' ),
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_end]',
				'[course_end label="Das Ende." label_tag="h3" course_id="5"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_dates.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_dates() {
		$data = array(
			'shortcode' => 'course_dates',
			'content' => __( 'Zeigt den Beginn und das Ende des Kurses an. Typischerweise als [course_start] - [course_end].', 'brainpress' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'Wenn außerhalb der ClassicPress-Schleife.', 'brainpress' ),
					),
				),
				'optional' => array(
					'date_format' => array(
						'content' => __( 'Datumsformat im PHP-Stil.', 'brainpress' ),
						'default' => 'wp',
					),
					'label' => array(
						'content' => __( 'Beschriftung, die für die Ausgabe angezeigt werden soll. Setze die Beschriftung auf "", um die Beschriftung vollständig auszublenden.', 'brainpress' ),
					),
					'label_tag' => array(
						'content' => __( 'HTML-Tag (ohne Klammern) für die einzelnen Beschriftungen.', 'brainpress' ),
						'default' => 'strong',
					),
					'label_delimeter' => array(
						'content' => __( 'Symbol, das nach dem Etikett verwendet werden soll.', 'brainpress' ),
						'default' => ':',
					),
					'no_date_text' => array(
						'content' => __( 'Text, der angezeigt wird, wenn der Kurs kein Enddatum hat.', 'brainpress' ),
						'default' => __( 'Kein Enddatum', 'brainpress' ),
					),
					'alt_display_text' => array(
						'content' => __( 'Alternative Anzeige, wenn kein Enddatum vorhanden ist.', 'brainpress' ),
						'default' => __( 'Unbefristet', 'brainpress' ),
					),
					'show_alt_display' => array(
						'content' => __( 'Wenn auf "yes" gesetzt, verwendest Du den alt_display_text. Wenn auf "no" gesetzt, verwendest Du "no_date_text"..', 'brainpress' ),
						'default' => __( 'no', 'brainpress' ),
						'options' => array( 'yes', 'no' ),
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_dates course_id="42"]',
				'[course_dates course_id="42" show_alt_display="yes" alt_display_text="Jederzeit lernen!"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_enrollment_start.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_enrollment_start() {
		$data = array(
			'shortcode' => 'course_enrollment_start',
			'content' => __( 'Zeigt das Startdatum der Kursanmeldung an.', 'brainpress' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'Wenn außerhalb der ClassicPress-Schleife.', 'brainpress' ),
					),
				),
				'optional' => array(
					'date_format' => array(
						'content' => __( 'Datumsformat im PHP-Stil.', 'brainpress' ),
						'default' => 'wp',
					),
					'label' => array(
						'content' => __( 'Beschriftung, die für die Ausgabe angezeigt werden soll. Setze die Beschriftung auf "", um die Beschriftung vollständig auszublenden.', 'brainpress' ),
					),
					'label_tag' => array(
						'content' => __( 'HTML-Tag (ohne Klammern) für die einzelnen Beschriftungen.', 'brainpress' ),
						'default' => __( 'strong', 'brainpress' ),
					),
					'label_delimeter' => array(
						'content' => __( 'Symbol, das nach dem Etikett verwendet werden soll.', 'brainpress' ),
						'default' => ':',
					),
					'no_date_text' => array(
						'content' => __( 'Text, der angezeigt wird, wenn für den Kurs kein Startdatum für die Einschreibung festgelegt wurde.', 'brainpress' ),
						'default' => __( 'Jederzeit einschreiben', 'brainpress' ),
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_enrollment_start]',
				'[course_enrollment_start label="Einschreiben von" label_tag="em"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_enrollment_end.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_enrollment_end() {
		$data = array(
			'shortcode' => 'course_enrollment_end',
			'content' => __( 'Zeigt das Enddatum der Kurseinschreibung an.', 'brainpress' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'Wenn außerhalb der ClassicPress-Schleife.', 'brainpress' ),
					),
				),
				'optional' => array(
					'date_format' => array(
						'content' => __( 'Datumsformat im PHP-Stil.', 'brainpress' ),
						'default' => __( 'ClassicPress-Einstellung.', 'brainpress' ),
					),
					'label' => array(
						'content' => __( 'Beschriftung, die für die Ausgabe angezeigt werden soll. Setze die Beschriftung auf "", um die Beschriftung vollständig auszublenden.', 'brainpress' ),
					),
					'label_tag' => array(
						'content' => __( 'HTML-Tag (ohne Klammern) für die einzelnen Beschriftungen.', 'brainpress' ),
						'default' => 'strong',
					),
					'label_delimeter' => array(
						'content' => __( 'Symbol, das nach dem Etikett verwendet werden soll.', 'brainpress' ),
						'default' => ':',
					),
					'no_date_text' => array(
						'content' => __( 'Text, der angezeigt werden soll, wenn kein Registrierungsenddatum vorhanden ist.', 'brainpress' ),
						'default' => __( 'Jederzeit einschreiben', 'brainpress' ),
					),
					'show_all_dates' => array(
						'content' => __( 'Wenn "yes", wird der no_date_text angezeigt, auch wenn kein Datum vorhanden ist. Wenn "no", wird nichts angezeigt.', 'brainpress' ),
						'default' => __( 'no', 'brainpress' ),
						'options' => array( 'yes', 'no' ),
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_end]',
				'[course_end label="Ende" label_delimeter="-"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_enrollment_dates.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_enrollment_dates() {
		$data = array(
			'shortcode' => 'course_enrollment_dates',
			'content' => __( 'Zeigt den Start- und Endzeitraum für die Kursanmeldung an. Typischerweise als [course_enrollment_start] - [course_enrollment_end].', 'brainpress' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'Wenn außerhalb der ClassicPress-Schleife.', 'brainpress' ),
					),
				),
				'optional' => array(
					'alt_display_text' => array(
						'content' => __( 'Alternative Anzeige, wenn kein Start- oder Enddatum für die Registrierung vorhanden ist.', 'brainpress' ),
						'default' => __( 'Unbegrenzt', 'brainpress' ),
					),
					'date_format' => array(
						'content' => __( 'Datumsformat im PHP-Stil.', 'brainpress' ),
						'default' => 'wp',
					),
					'label_delimeter' => array(
						'content' => __( 'Symbol, das nach dem Etikett verwendet werden soll.', 'brainpress' ),
						'default' => 'wp',
					),
					'label_enrolled' => array(
						'content' => __( 'Beschriftung für das Einschreibedatum.', 'brainpress' ),
						'default' => __( 'Du hast dich eingeschrieben: ', 'brainpress' ),
					),
					'label' => array(
						'content' => __( 'Beschriftung, die für die Ausgabe angezeigt werden soll. Setze die Beschriftung auf "", um die Beschriftung vollständig auszublenden.', 'brainpress' ),
					),
					'label_tag' => array(
						'content' => __( 'HTML-Tag (ohne Klammern) für die einzelnen Beschriftungen.', 'brainpress' ),
						'default' => __( 'strong', 'brainpress' ),
					),
					'no_date_text' => array(
						'content' => __( 'Text, der angezeigt werden soll, wenn kein Start- oder Enddatum für die Registrierung vorhanden ist.', 'brainpress' ),
						'default' => __( 'Jederzeit einschreiben', 'brainpress' ),
					),
					'show_alt_display' => array(
						'content' => __( 'Wenn auf "yes" gesetzt, verwende den alt_display_text. Wenn auf "no" gesetzt, verwende "no_date_text"..', 'brainpress' ),
						'default' => __( 'no', 'brainpress' ),
						'options' => array( 'yes', 'no' ),
					),
					'show_enrolled_display' => array(
						'content' => __( 'Startetikett der Registrierung anzeigen.', 'brainpress' ),
						'default' => __( 'yes', 'brainpress' ),
						'options' => array( 'yes', 'no' ),
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_enrollment_dates]',
				'[course_enrollment_dates no_date_text="Keine bessere Zeit als jetzt!"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_enrollment_type.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_enrollment_type() {
		$data = array(
			'shortcode' => 'course_enrollment_type',
			'content' => __( 'Zeigt die Art der Registrierung an (manuell, Voraussetzung, Passcode oder sonst jemand).', 'brainpress' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'Wenn außerhalb der ClassicPress-Schleife.', 'brainpress' ),
					),
				),
				'optional' => array(
					'anyone_text' => array(
						'content' => __( 'Text, der angezeigt wird, wenn sich jemand anmelden kann.', 'brainpress' ),
						'default' => __( 'Jeder', 'brainpress' ),
					),
					'label_delimeter' => array(
						'content' => __( 'Symbol, das nach dem Etikett verwendet werden soll.', 'brainpress' ),
						'default' => ':',
					),
					'label' => array(
						'content' => __( 'Beschriftung, die für die Ausgabe angezeigt werden soll. Setze die Beschriftung auf "", um die Beschriftung vollständig auszublenden.', 'brainpress' ),
					),
					'label_tag' => array(
						'content' => __( 'HTML-Tag (ohne Klammern) für die einzelnen Beschriftungen.', 'brainpress' ),
						'default' => 'strong',
					),
					'manual_text' => array(
						'content' => __( 'Text, der für manuelle Registrierungen angezeigt werden soll.', 'brainpress' ),
						'default' => __( 'Die Studenten werden von Kursleitern hinzugefügt.', 'brainpress' ),
					),
					'passcode_text' => array(
						'content' => __( 'Text, der angezeigt wird, wenn ein Passcode erforderlich ist.', 'brainpress' ),
						'default' => __( 'Für die Anmeldung ist ein Passcode erforderlich.', 'brainpress' ),
					),
					'prerequisite_text' => array(
						'content' => __( 'Text, der angezeigt werden soll, wenn eine Voraussetzung vorliegt. Verwende %s als Platzhalter für den vorausgesetzten Kurstitel.', 'brainpress' ),
						'default' => __( 'Die Studenten müssen zuerst "%s" erfüllen.', 'brainpress' ),
					),
					'registered_text' => array(
						'content' => __( 'Text, der für registrierte Benutzer angezeigt werden soll.', 'brainpress' ),
						'default' => __( 'Registrierte Benutzer.', 'brainpress' ),
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_enrollment_type]',
				'[course_enrollment_type course_id="42"]',
				'[course_enrollment_type passcode_text="Wie lautet das magische Wort?"',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_class_size.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_class_size() {
		$data = array(
			'shortcode' => 'course_class_size',
			'content' => __( 'Zeigt die Größe der Kursklasse, die Limits und die verbleibenden Plätze an.', 'brainpress' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'Wenn außerhalb der ClassicPress-Schleife.', 'brainpress' ),
					),
				),
				'optional' => array(
					'label_delimeter' => array(
						'content' => __( 'Symbol, das nach dem Etikett verwendet werden soll.', 'brainpress' ),
						'default' => ':',
					),
					'label' => array(
						'content' => __( 'Beschriftung, die für die Ausgabe angezeigt werden soll. Setze die Beschriftung auf "", um die Beschriftung vollständig auszublenden.', 'brainpress' ),
					),
					'label_tag' => array(
						'content' => __( 'HTML-Tag (ohne Klammern) für die einzelnen Beschriftungen.', 'brainpress' ),
						'default' => 'strong',
					),
					'no_limit_text' => array(
						'content' => __( 'Text, der für unbegrenzte Klassengrößen angezeigt werden soll.', 'brainpress' ),
						'default' => __( 'Unbegrenzt', 'brainpress' ),
					),
					'remaining_text' => array(
						'content' => __( 'Text, der für die verbleibenden Stellen angezeigt werden soll. Verwende %d für die verbleibende Anzahl.', 'brainpress' ),
						'default' => __( '(%d Plätze übrig)', 'brainpress' ),
					),
					'show_no_limit' => array(
						'content' => __( 'Wenn "yes", wird der no_limit_text angezeigt. Wenn "no", wird für unbegrenzte Kurse nichts angezeigt.', 'brainpress' ),
						'default' => 'no',
						'options' => array( 'yes', 'no' ),
					),
					'show_remaining' => array(
						'content' => __( 'Wenn "yes", wird der verbleibende Platz angezeigt. Wenn "no", werden keine verbleibenden Plätze angezeigt.', 'brainpress' ),
						'default' => 'yes',
						'options' => array( 'yes', 'no' ),
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_class_size]',
				'[course_class_size course_id="42" no_limit_text="Je mehr, desto besser"]',
				'[course_class_size remaining_text="Nur noch %d Plätze übrig!"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_cost.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_cost() {
		$data = array(
			'shortcode' => 'course_cost',
			'content' => __( 'Shows the pricing for the course or free for unpaid courses.', 'brainpress' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'Wenn außerhalb der ClassicPress-Schleife.', 'brainpress' ),
					),
				),
				'optional' => array(
					'label_delimeter' => array(
						'content' => __( 'Symbol, das nach dem Etikett verwendet werden soll.', 'brainpress' ),
						'default' => ':',
					),
					'label' => array(
						'content' => __( 'Beschriftung, die für die Ausgabe angezeigt werden soll. Setze die Beschriftung auf "", um die Beschriftung vollständig auszublenden.', 'brainpress' ),
					),
					'label_tag' => array(
						'content' => __( 'HTML-Tag (ohne Klammern) für die einzelnen Beschriftungen.', 'brainpress' ),
						'default' => 'strong',
					),
					'no_cost_text' => array(
						'content' => __( 'Text, der für unbezahlte Kurse angezeigt werden soll.', 'brainpress' ),
						'default' => __( 'FREI', 'brainpress' ),
					),
					'show_icon' => array(
						'content' => __( 'Füge eine zusätzliche Spanne mit der Klasse "product_price" um no_cost_text hinzu.', 'brainpress' ),
						'default' => __( 'no', 'brainpress' ),
						'options' => array( 'yes', 'no' ),
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_cost]',
				'[course_cost no_cost_text="'. __( 'Kostenlos wie Wikipedia.', 'brainpress' ) .'"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_time_estimation.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_time_estimation() {
		$data = array(
			'shortcode' => 'course_time_estimation',
			'content' => __( 'Zeigt die Gesamtzeitschätzung basierend auf der Berechnung der Einheitselemente an.', 'brainpress' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'Wenn außerhalb der ClassicPress-Schleife.', 'brainpress' ),
					),
				),
				'optional' => array(
					'label_delimeter' => array(
						'content' => __( 'Symbol, das nach dem Etikett verwendet werden soll.', 'brainpress' ),
						'default' => ':',
					),
					'label' => array(
						'content' => __( 'Beschriftung, die für die Ausgabe angezeigt werden soll. Setze die Beschriftung auf "", um die Beschriftung vollständig auszublenden.', 'brainpress' ),
					),
					'label_tag' => array(
						'content' => __( 'HTML-Tag (ohne Klammern) für die einzelnen Beschriftungen.', 'brainpress' ),
						'default' => 'strong',
					),
					'wrapper' => array(
						'content' => __( 'Wickel in ein div-Tag (yes|no).', 'brainpress' ),
						'options' => array( 'yes', 'no' ),
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_time_estimation course_id="42" wrapper="yes"]',
				'[course_time_estimation course_id="42"]',
				'[course_time_estimation wrapper="yes"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_language.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_language() {
		$data = array(
			'shortcode' => 'course_language',
			'content' => __( 'Zeigt die Sprache des Kurses an (falls festgelegt).', 'brainpress' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'Wenn außerhalb der ClassicPress-Schleife.', 'brainpress' ),
					),
				),
				'optional' => array(
					'label' => array(
						'content' => __( 'Beschriftung, die für die Ausgabe angezeigt werden soll. Setze die Beschriftung auf "", um die Beschriftung vollständig auszublenden.', 'brainpress' ),
					),
					'label_tag' => array(
						'content' => __( 'HTML-Tag (ohne Klammern) für die einzelnen Beschriftungen.', 'brainpress' ),
						'default' => 'strong',
					),
					'label_delimeter' => array(
						'content' => __( 'Symbol, das nach dem Etikett verwendet werden soll.', 'brainpress' ),
						'default' => ':',
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_language]',
				'[course_language label="Delivered in"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_list_image.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_list_image() {
		$data = array(
			'shortcode' => 'course_list_image',
			'content' => __( 'Zeigt das Bild der Kursliste an. (Siehe [course_media]).', 'brainpress' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'Wenn außerhalb der ClassicPress-Schleife.', 'brainpress' ),
					),
				),
				'optional' => array(
					'width' => array(
						'content' => __( 'Bildbreite.', 'brainpress' ),
						'default' => __( 'Originalbreite', 'brainpress' ),
					),
					'height' => array(
						'content' => __( 'Bildhöhe.', 'brainpress' ),
						'default' => __( 'Originalhöhe', 'brainpress' ),
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_list_image]',
				'[course_list_image width="100" height="100"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_featured_video.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_featured_video() {
		$data = array(
			'shortcode' => 'course_featured_video',
			'content' => __( 'Bette einen Videoplayer in das Video des Kurses ein. (Siehe [course_media]).', 'brainpress' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'Wenn außerhalb der ClassicPress-Schleife.', 'brainpress' ),
					),
				),
				'optional' => array(
					'width' => array(
						'content' => __( 'Breite des Videoplayers.', 'brainpress' ),
						'default' => __( 'Standard-Playerbreite', 'brainpress' ),
					),
					'height' => array(
						'content' => __( 'Höhe des Videoplayers.', 'brainpress' ),
						'default' => __( 'Standardgröße des Players', 'brainpress' ),
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_featured_video]',
				'[course_featured_video width="320" height="240"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_media.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_media() {
		$data = array(
			'shortcode' => 'course_media',
			'content' => __( 'Zeigt entweder das Listenbild oder das vorgestellte Video an (mit der anderen Option als möglichem Fallback).', 'brainpress' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'Wenn außerhalb der ClassicPress-Schleife.', 'brainpress' ),
					),
				),
				'optional' => array(
					'width' => array(
						'content' => __( 'Medienbreite.', 'brainpress' ),
						'default' => __( 'BrainPress Einstellungen.', 'brainpress' ),
					),
					'height' => array(
						'content' => __( 'Höhe der Medien.', 'brainpress' ),
						'default' => __( 'BrainPress Einstellungen.', 'brainpress' ),
					),
					'list_page' => array(
						'content' => __( 'Verwende "yes", um die BrainPress-Einstellungen für "Kurslisten" zu verwenden. Verwende "no", um die BrainPress-Einstellungen für "Kursdetailseite" zu verwenden..', 'brainpress' ),
						'default' => __( 'no', 'brainpress' ),
						'options' => array( 'yes', 'no' ),
					),
					'priority' => array(
						'content' => __( 'Verwende "image", um nur Listenbilder anzuzeigen. Wenn nicht verfügbar, versuche das vorgestellte Video zu verwenden.  Verwenden Sie "video", um zu versuchen, das vorgestellte Video zuerst anzuzeigen. Wenn nicht verfügbar, versuche das Listenbild zu verwenden.', 'brainpress' ),
						'default' => __( 'BrainPress Einstellungen', 'brainpress' ),
						'options' => array( 'image', 'video', 'default' ),
					),
					'type' => array(
						'content' => __( 'Verwende "image", um nur Listenbilder anzuzeigen, wenn diese verfügbar sind. Verwende "video", um das Video nur anzuzeigen, wenn es verfügbar ist. Verwende "thumbnail", um das Miniaturbild des Kurses anzuzeigen (Verknüpfung für Typ = "Bild" und Priorität = "Bild").. Use "default" to enable priority mode (see priority attribute).', 'brainpress' ),
						'default' => __( 'BrainPress Einstellungen', 'brainpress' ),
						'options' => array( 'image', 'video', 'thumbnail', 'default' ),
					),
					'wrapper' => array(
						'content' => __( 'In ein Etikett einwickeln.', 'brainpress' ),
						'default' => __( 'leere Zeichenfolge, aber wenn Höhe oder Breite definiert ist, ist der Wrapper ein "div" -Tag.', 'brainpress' ),
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_media]',
				'[course_media list_page="yes"]',
				'[course_media type="video"]',
				'[course_media priority="image"]',
				'[course_media type="thumbnail"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_join_button.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_join_button() {
		$data = array(
			'shortcode' => 'course_join_button',
			'content' => __( 'Zeigt die Schaltfläche Einschreiben/Anmelden/Registrieren für den Kurs an. Was angezeigt wird, hängt von den Kurseinstellungen und dem Status/der Registrierung des Benutzers ab.<br />In den Attributen findest Du mögliche Schaltflächenbeschriftungen.', 'brainpress' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'Wenn außerhalb der ClassicPress-Schleife.', 'brainpress' ),
					),
				),
				'optional' => array(
					'access_text' => array(
						'content' => __( 'Text, der angezeigt wird, wenn der Benutzer registriert und lernbereit ist.', 'brainpress' ),
						'default' => __( 'Beginne zu lernen', 'brainpress' ),
					),
					'continue_learning_text' => array(
						'content' => __( 'Text, der angezeigt wird, wenn der Kurs fortgesetzt werden kann.', 'brainpress' ),
						'default' => __( 'Weiterlernen', 'brainpress' ),
					),
					'course_expired_text' => array(
						'content' => __( 'Text, der angezeigt wird, wenn der Kurs abgelaufen ist.', 'brainpress' ),
						'default' => __( 'Nicht verfügbar', 'brainpress' ),
					),
					'course_full_text' => array(
						'content' => __( 'Text, der angezeigt wird, wenn der Kurs voll ist.', 'brainpress' ),
						'default' => __( 'Kurs voll', 'brainpress' ),
					),
					'details_text' => array(
						'content' => __( 'Text für die Schaltfläche, die zur vollständigen Kursseite führt.', 'brainpress' ),
						'default' => __( 'Kursdetails', 'brainpress' ),
					),
					'enrollment_closed_text' => array(
						'content' => __( 'Text, der angezeigt wird, wenn die Einschreibung noch nicht begonnen hat.', 'brainpress' ),
						'default' => __( 'Einschreibungen geschlossen', 'brainpress' ),
					),
					'enrollment_finished_text' => array(
						'content' => __( 'Text, der angezeigt wird, wenn die Registrierung abgeschlossen ist (abgelaufen).', 'brainpress' ),
						'default' => __( 'Einschreibungen abgeschlossen', 'brainpress' ),
					),
					'enroll_text' => array(
						'content' => __( 'Text, der angezeigt wird, wenn der Kurs für die Anmeldung bereit ist.', 'brainpress' ),
						'default' => __( 'Schreibe dich jetzt ein', 'brainpress' ),
					),
					'instructor_text' => array(
						'content' => __( 'Text, der angezeigt wird, wenn der aktuelle Benutzer ein Kursleiter ist.', 'brainpress' ),
						'default' => __( 'Zugang zum Kurs', 'brainpress' ),
					),
					'list_page' => array(
						'content' => __( 'Schaltfläche "Anzeigen" zu den Kursdetails.', 'brainpress' ),
						'default' => 'false',
					),
					'not_started_text' => array(
						'content' => __( 'Text, der angezeigt wird, wenn ein Schüler eingeschrieben ist, der Kurs jedoch noch nicht begonnen hat.', 'brainpress' ),
						'default' => __( 'Nicht verfügbar', 'brainpress' ),
					),
					'passcode_text' => array(
						'content' => __( 'Text, der angezeigt werden soll, wenn für den Kurs ein Passwort erforderlich ist.', 'brainpress' ),
						'default' => __( 'Passcode erforderlich', 'brainpress' ),
					),
					'prerequisite_text' => array(
						'content' => __( 'Text, der angezeigt werden soll, wenn der Kurs eine Voraussetzung hat.', 'brainpress' ),
						'default' => __( 'Voraussetzung Erforderlich', 'brainpress' ),
					),
					'signup_text' => array(
						'content' => __( 'Text, der angezeigt wird, wenn der Kurs für die Einschreibung bereit ist, der Benutzer jedoch nicht angemeldet ist (Besucher).', 'brainpress' ),
						'default' => __( 'Einschreiben!', 'brainpress' ),
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_join_button]',
				'[course_join_button course_id="11" course_expired_text="'. __( 'Du hast die meiste Zeit verpasst!', 'brainpress' ).'"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_action_links.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_action_links() {
		$data = array(
			'shortcode' => 'course_action_links',
			'content' => __( 'Zeigt die Links "Kursdetails" und "Zurückziehen" den Studenten an.', 'brainpress' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'Wenn außerhalb der ClassicPress-Schleife.', 'brainpress' ),
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_action_links]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_calendar.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_calendar() {
		$data = array(
			'shortcode' => 'course_calendar',
			'content' => __( 'Zeigt den Kurskalender an (die Grenzen sind durch das Start- und Enddatum des Kurses begrenzt). Es wird immer versucht, das heutige Datum zuerst in einem Kalender anzuzeigen.', 'brainpress' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'Wenn außerhalb der ClassicPress-Schleife.', 'brainpress' ),
					),
				),
				'optional' => array(
					'date_indicator' => array(
						'content' => __( 'Klassenzeichenfolge, die dem Tag-Halter der Kalendertabelle hinzugefügt wurde.', 'brainpress' ),
						'default' => __( 'indicator_light_block', 'brainpress' ),
					),
					'month' => array(
						'content' => __( 'Monat, der als Nummer angezeigt werden soll (z.B. 03 für März).', 'brainpress' ),
						'default' => __( 'Heutiges Datum', 'brainpress' ),
					),
					'next' => array(
						'content' => __( 'Text, der für den Link zum nächsten Monat angezeigt werden soll.', 'brainpress' ),
						'default' => __( 'Nächste &raquo;', 'brainpress' ),
					),
					'pre' => array(
						'content' => __( 'Text to display for previous month link.', 'brainpress' ),
						'default' => __( '&laquo; Bisherige', 'brainpress' ),
					),
					'year' => array(
						'content' => __( 'Jahr, das als 4-stellige Zahl angezeigt werden soll (z. B. 2020).', 'brainpress' ),
						'default' => __( 'Heutiges Datum', 'brainpress' ),
					),
				),
			),
			'examples' => array(
				'[course_calendar]',
				'[course_calendar pre="< Bisherige" next="Nächste >"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_list.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_list() {
		$data = array(
			'shortcode' => 'course_list',
			'content' => __( 'Zeigt eine Liste der Kurse an. Kann für alle Kurse gelten oder von Kursleitern oder Studenten eingeschränkt werden (nur der eine oder andere, wenn beide angegeben sind, werden nur Studenten verwendet).', 'brainpress' ),
			'parameters' => array(
				'optional' => array(
					'categories' => array(
						'content' => __( 'Eine durch Kommas getrennte Kategorie wird angezeigt, um Kurse aus bestimmten Kurskategorien anzuzeigen.', 'brainpress' ),
						'default' => 'empty',
					),
					'completed_label' => array(
						'content' => __( 'Etikett für die Liste der abgeschlossenen Kurse.', 'brainpress' ),
						'default' => __( 'Abgeschlossene Kurse', 'brainpress' ),
					),
					'context' => array(
						'content' => __( 'Kontext für die Kursliste. Mögliche Werte:', 'brainpress' ),
						'default' => 'all',
						'options' => array( 'enrolled', 'future', 'incomplete', 'completed', 'past', 'manage', 'facilitator', 'all' ),
					),
					'current_label' => array(
						'content' => __( 'Label für aktuelle Kurse.', 'brainpress' ),
						'default' => __( 'Aktuelle Kurse', 'brainpress' ),
					),
					'dashboard' => array(
						'content' => __( 'Wenn true oder "yes" ist, wechsele den Kontext zu "dashboard"..', 'brainpress' ),
						'default' => 'empty',
					),
					'facilitator_label' => array(
						'content' => __( 'Beschriftung vor Kursliste für "Moderator" -Kontext.', 'brainpress' ),
						'default' => __( 'Moderierte Kurse', 'brainpress' ),
					),
					'facilitator' => array(
						'content' => __( 'Wenn dies zutrifft oder "yes, wechsele den Inhalt zu "Moderator"..', 'brainpress' ),
						'default' => 'empty',
					),
					'future_label' => array(
						'content' => __( 'Label für zukünftige Kurse.', 'brainpress' ),
						'default' => __( 'Beginnt bald', 'brainpress' ),
					),
					'incomplete_label' => array(
						'content' => __( 'Etikett für unvollständige Kurse.', 'brainpress' ),
						'default' => __( 'Unvollständige Kurse', 'brainpress' ),
					),
					'instructor_msg' => array(
						'content' => __( 'Die Meldung wird auf der Einführerseite angezeigt, wenn dem Kursleiter keine Kurse zugewiesen sind.', 'brainpress' ),
						'default' => __( 'Dem Kursleiter sind noch keine Kurse zugewiesen.', 'brainpress' ),
					),
					'instructor' => array(
						'content' => __( 'Die Kursleiter-ID, um Kurse für einen bestimmten Ausbilder aufzulisten. Kann auch mehrere Kursleiter mit Kommas angeben. (z. B. Ausbilder = "1,2,3").', 'brainpress' ),
						'default' => 'empty',
						'description' => __( 'Wenn sowohl Student als auch Kursleiter angegeben sind, wird nur der Student verwendet.', 'brainpress' ),
					),
					'limit' => array(
						'content' => __( 'Begrenze die Anzahl der Kurse. Verwende -1, um alle anzuzeigen.', 'brainpress' ),
						'default' => __( '-1', 'brainpress' ),
					),
					'manage_label' => array(
						'content' => __( 'Beschriftung vor verwaltbaren Kursen.', 'brainpress' ),
						'default' => __( 'Kurse verwalten', 'brainpress' ),
					),
					'order' => array(
						'content' => __( 'Bestelle die Kurse. "ASC" für aufsteigende Reihenfolge. "DESC" für absteigende Reihenfolge.', 'brainpress' ),
						'default' => __( 'ASC', 'brainpress' ),
						'options' => array( 'ASC', 'DESC' ),
					),
					'orderby' => array(
						'content' => __( 'Bestelle die Kurse nach Kurstermin oder Kurstitel.', 'brainpress' ),
						'default' => __( 'meta', 'brainpress' ),
                        'options' => array( 'meta', 'title' ),
                        'description' => __('Es funktioniert nur mit dem Standard "context".', 'brainpress' ),
					),
					'past_label' => array(
						'content' => __( 'Beschriftung vor früheren Kursen.', 'brainpress' ),
						'default' => __( 'Vergangene Kurse', 'brainpress' ),
					),
					'show_labels' => array(
						'content' => __( 'Beschriftungen anzeigen.', 'brainpress' ),
						'default' => 'false',
					),
					'status' => array(
						'content' => __( 'Der Status der anzuzeigenden Kurse (verwendet den ClassicPress-Status).', 'brainpress' ),
						'default' => __( 'veröffentlicht', 'brainpress' ),
					),
					'student_msg' => array(
						'content' => __( 'Die Meldung wird angezeigt, wenn der Student nicht für einen Kurs eingeschrieben ist.', 'brainpress' ),
						'default' => sprintf(
							__( 'Du bist in keinem Kurs eingeschrieben. %s', 'brainpress' ),
							htmlentities(
								sprintf(
									__( '<a href="%s">Siehe verfügbare Kurse.</a>', 'brainpress' ),
									esc_attr( '/'.BrainPress_Core::get_setting( 'slugs/course', 'courses' ) )
								)
							)
						),
					),
					'student' => array(
						'content' => __( 'Die Studenten-ID, um Kurse für einen bestimmten Studenten aufzulisten. Kann auch mehrere Schüler mit Kommas angeben. (z.B. Student = "1,2,3").', 'brainpress' ),
						'default' => 'empty',
						'description' => __( 'Wenn sowohl Student als auch Kursleiter angegeben sind, wird nur der Student verwendet.', 'brainpress' ),
						'suggested_label' => array(
							'content' => __( 'Beschriftung vor den vorgeschlagenen Kursen.', 'brainpress' ),
							'default' => __( 'Vorgeschlagene Kurse', 'brainpress' ),
						),
						'suggested_msg' => array(
							'content' => __( 'Die Nachricht wird angezeigt, wenn der Student nicht für einen Kurs eingeschrieben ist, wir haben jedoch einige empfohlene Kurse.', 'brainpress' ),
							'default' => sprintf(
								__( 'Du bist in keinem Kurs eingeschrieben.<br />Hier sind einige, die Dir gefallen könnten, oder %s' ),
								htmlentities( __( ' <a href="%s">alle verfügbaren Kurse anzeigen.</a>', 'brainpress' ) )
							),
						),
					),
					'show_withdraw_link' => array(
						'content' => __( 'Das Anzeigen eines Rückzugslinks ist zulässig, funktioniert jedoch nur, wenn der Benutzer ein Student ist und der Status auf "unvollständig" gesetzt ist..', 'brainpress' ),
						'default' => 'false',
					),
				),
			),
			'examples' => array(
				'[course_list]',
				'[course_list instructor="2"]',
				'[course_list student="3"]',
				'[course_list instructor="2,4,5"]',
				'[course_list show="dates,cost" limit="5"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_featured.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_featured() {
		$data = array(
			'shortcode' => 'course_featured',
			'content' => __( 'Zeigt einen vorgestellten Kurs.', 'brainpress' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'Wenn außerhalb der ClassicPress-Schleife.', 'brainpress' ),
					),
				),
				'optional' => array(
					'featured_title' => array(
						'content' => __( 'Der Titel, der für den vorgestellten Kurs angezeigt werden soll.', 'brainpress' ),
						'default' => __( 'Empfohlener Kurs', 'brainpress' ),
					),
					'button_title' => array(
						'content' => __( 'Text, der auf der Aktionsschaltfläche angezeigt werden soll.', 'brainpress' ),
						'default' => __( 'Finde mehr heraus.', 'brainpress' ),
					),
					'media_type' => array(
						'content' => __( 'Medientyp für den vorgestellten Kurs. Siehe [course_media].', 'brainpress' ),
						'default' => 'default',
						'options' => array( 'image', 'video', 'thumbnail', 'default' ),
					),
					'media_priority' => array(
						'content' => __( 'Medienpriorität für den vorgestellten Kurs. Siehe [course_media].', 'brainpress' ),
						'default' => __( 'video', 'brainpress' ),
						'options' => array( 'image', 'video', 'default' ),
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_featured course_id="42"]',
				'[course_featured course_id="11" featured_title="Das Beste was wir haben!"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_structure.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_structure() {
		$data = array(
			'shortcode' => 'course_structure',
			'content' => __( 'Zeigt eine Baumansicht der Kursstruktur an.', 'brainpress' ),
			'parameters' => array(
				'required' => array(
					'course_id' => array(
						'content' => __( 'Wenn außerhalb der ClassicPress-Schleife.', 'brainpress' ),
					),
				),
				'optional' => array(
					'deep' => array(
						'content' => __( 'Alle Kursmodule anzeigen.', 'brainpress' ),
						'default' => 'false',
					),
					'free_class' => array(
						'content' => __( 'Zusätzliche CSS-Klassen zum Gestalten von kostenlosen Vorschauelementen.', 'brainpress' ),
						'default' => __( 'free', 'brainpress' ),
					),
					'free_show' => array(
						'content' => __( 'Zeige Gratiskurse.', 'brainpress' ),
						'default' => 'true',
					),
					'free_text' => array(
						'content' => __( 'Text, der bei Gratiskursen in der Vorschau angezeigt werden soll.', 'brainpress' ),
						'default' => __( 'Vorschau', 'brainpress' ),
					),
					'label_delimeter' => array(
						'content' => __( 'Symbol, das nach dem Etikett verwendet werden soll.', 'brainpress' ),
						'default' => ':',
					),
					'label' => array(
						'content' => __( 'Beschriftung, die für die Ausgabe angezeigt werden soll.', 'brainpress' ),
						'default' => __( 'Kursstruktur', 'brainpress' ),
					),
					'label_tag' => array(
						'content' => __( 'HTML-Tag (ohne Klammern) für die einzelnen Beschriftungen.', 'brainpress' ),
						'default' => 'strong',
					),
					'show_divider' => array(
						'content' => __( 'Zeige die Trennlinie zwischen den Hauptelementen im Baum an, "yes" oder "no"..', 'brainpress' ),
						'default' => __( 'yes', 'brainpress' ),
						'options' => array( 'yes', 'no' ),
					),
					'show_label' => array(
						'content' => __( 'Beschriftungstext als Baumüberschrift anzeigen, "yes" oder "no".', 'brainpress' ),
						'default' => __( 'no', 'brainpress' ),
						'options' => array( 'yes', 'no' ),
					),
					'show_title' => array(
						'content' => __( 'Kurstitel in Struktur anzeigen, "yes" oder "no".', 'brainpress' ),
						'default' => __( '"no"', 'brainpress' ),
						'options' => array( 'yes', 'no' ),
					),
				),
			),
			'add_class_to_optional' => true,
			'examples' => array(
				'[course_structure]',
				'[course_structure course_id="42" free_text="'.__( 'FREI!', 'brainpress' ).'" show_title="no"]',
				'[course_structure show_title="no" label="'.__( 'Lehrplan', 'brainpress' ).'"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for course_signup.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_course_signup() {
		$data = array(
			'shortcode' => 'course_signup',
			'content' => __( 'Zeigt eine benutzerdefinierte Anmelde- oder Anmeldeseite für die Registrierung und Anmeldung von Front-End-Benutzern an.', 'brainpress' ),
		   'note' => __( 'Dies ist bereits Teil von BrainPress und kann in den BrainPress-Einstellungen festgelegt werden. Links zu Standardseiten findest Du unter Darstellung> Menüs> BrainPress.', 'brainpress' ),
			'parameters' => array(
				'optional' => array(
					'failed_login_class' => array(
						'content' => __( 'CSS-Klasse für ungültige Anmeldung.', 'brainpress' ),
						'default' => 'red',
					),
					'failed_login_text' => array(
						'content' => __( 'Text, der angezeigt werden soll, wenn sich der Benutzer nicht authentifiziert.', 'brainpress' ),
						'default' => __( 'Ungültiger Login.', 'brainpress' ),
					),
					'login_tag' => array(
						'content' => __( 'Titel-Tag-Wrapper.', 'brainpress' ),
						'default' => 'h3',
					),
					'login_title' => array(
						'content' => __( 'Titel, der für den Anmeldeabschnitt verwendet werden soll.', 'brainpress' ),
						'default' => __( 'Login', 'brainpress' ),
					),
					'login_url' => array(
						'content' => __( 'URL, zu der umgeleitet werden soll, wenn Du auf "Hast Du bereits ein Konto?" klickst.".', 'brainpress' ),
						'default' => __( 'Plugin Standards.', 'brainpress' ),
					),
					'logout_url' => array(
						'content' => __( 'URL, zu der umgeleitet werden soll, wenn sich der Benutzer abmeldet.', 'brainpress' ),
						'default' => __( 'Plugin Standards.', 'brainpress' ),
					),
					'page' => array(
						'content' => __( 'Wenn der Seitenparameter nicht festgelegt ist, versucht BrainPress, die Variable "page" aus $ _REQUEST zu verwenden.', 'brainpress' ),
						'default' => 'empty',
					),
					'signup_tag' => array(
						'content' => __( 'Titel-Tag-Wrapper.', 'brainpress' ),
						'default' => 'h3',
					),
					'signup_title' => array(
						'content' => __( 'Titel für den Anmeldeabschnitt.', 'brainpress' ),
						'default' => __( 'Einschreiben', 'brainpress' ),
					),
					'signup_url' => array(
						'content' => __( 'URL zum Weiterleiten, wenn Du auf "Du hast noch kein Konto? Gehe zu Anmelden!" Klickst."', 'brainpress' ),
						'default' => 'empty',
					),
				),
			),
			'examples' => array(
				'[course_signup]',
				'[course_signup signup_title="&lt;h1&gt;'.__( 'Jetzt einschreiben', 'brainpress' ).'&lt;/h1&gt;"]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for courses_student_dashboard.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_courses_student_dashboard() {
		$data = array(
			'shortcode' => 'courses_student_dashboard',
			'content' => __( 'Lädt die Schüler-Dashboard-Vorlage.', 'brainpress' ),
			'examples' => array(
				'[courses_student_dashboard]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce help box for courses_student_settings.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function _box_courses_student_settings() {
		$data = array(
			'shortcode' => 'courses_student_settings',
			'content' => __( 'Lädt die Vorlage für die Studenteneinstellungen.', 'brainpress' ),
			'examples' => array(
				'[courses_student_settings]',
			),
		);
		$content = self::_prepare_box( $data );
		return $content;
	}

	/**
	 * Produce Box.
	 *
	 * @since 2.0.2
	 * @access private
	 *
	 * @return string
	 */
	private static function _prepare_box( $data ) {
		$content = sprintf( '<span class="cp-shortcode-code">[%s]</span><br />', $data['shortcode'] );
		$content .= sprintf( '<p class="description">%s</p>', $data['content'] );
		if ( isset( $data['note'] ) ) {
			$content .= sprintf( __( '<p class="description"><strong>Hinweis</strong>: %s</p>', 'brainpress' ), $data['note'] );
		}
		if ( isset( $data['parameters'] ) ) {
			$kinds = array(
				'required' => __( 'Erforderliche Attribute:', 'brainpress' ),
				'optional' => __( 'Optionale Attribute:', 'brainpress' ),
			);
			if ( isset( $data['add_class_to_optional'] ) && $data['add_class_to_optional'] ) {
				if ( ! isset( $data['parameters'] ) ) {
					$data['parameters'] = array();
				}
				if ( ! isset( $data['parameters']['optional'] ) ) {
					$data['parameters']['optional'] = array();
				}
				$data['parameters']['optional']['class'] = array( 'content' => __( 'Zusätzliche CSS-Klassen zur weiteren Gestaltung.', 'brainpress' ) );
			}
			foreach ( $kinds as $kind => $kind_label ) {
				if ( isset( $data['parameters'][ $kind ] ) && is_array( $data['parameters'][ $kind ] ) && ! empty( $data['parameters'][ $kind ] ) ) {
					$content .= sprintf( '<div class="cp-shortcode-attributes cp-shortcode-attributes-%s">', esc_attr( $kind ) );
					$content .= sprintf( '<p class="cp-shortcode-subheading">%s</p>', esc_html( $kind_label ) );
					$content .= '<ul class="cp-shortcode-options">';
					$attributes = $data['parameters'][ $kind ];
					ksort( $attributes );
					foreach ( $attributes as $attr_name => $attr_data ) {
                        $content .= sprintf( '<li class="shortcode-%s">', esc_attr( $attr_name ) );
                        $content .= '<p>';
						$content .= sprintf( '<span>%s</span>', esc_html( $attr_name ) );
						if ( isset( $attr_data['content'] ) ) {
							$content .= ' &ndash; ';
							$content .= $attr_data['content'];
						}
						if ( isset( $attr_data['options'] ) ) {
							$content .= '<p class="options">';
							$options = '<em>'.implode( '</em>, <em>', $attr_data['options'] ).'</em>';
                            $content .= sprintf( __( 'Optionen: %s.', 'brainpress' ), $options );
                            $content .= '</p>';
							if ( isset( $attr_data['options_description'] ) && ! empty( $attr_data['options_description'] ) ) {
								$content .= sprintf( '<p class="description">%s</p>', esc_html( $attr_data['options_description'] ) );
							}
                        }
                        $content .= '</p>';
						if ( isset( $attr_data['default'] ) && ! empty( $attr_data['default'] ) ) {
							$content .= '<p class="default">';
							switch ( $attr_data['default'] ) {
								case ':':
									$content .= __( 'Standard ist Doppelpunkt (<em>:</em>)', 'brainpress' );
								break;
								case ',':
									$content .= __( 'Standard ist Koma (<em>,</em>)', 'brainpress' );
								break;
								case 'WP':
										$content .= sprintf( __( 'Standard: <em>%s</em>.', 'brainpress' ), __( 'ClassicPress Settings' ) );
								break;
								case 'empty':
										$content .= sprintf( __( 'Standard: <em>%s</em>.', 'brainpress' ), __( 'empty string' ) );
								break;
								default:
									if ( is_numeric( $attr_data['default'] ) ) {
										$content .= sprintf( __( 'Standard: <em>%s</em>.', 'brainpress' ), htmlentities( $attr_data['default'] ) );
									} else {
										$content .= sprintf( __( 'Standard: "<em>%s</em>"', 'brainpress' ), htmlentities( $attr_data['default'] ) );
									}
                            }
                            $content .= '</p>';
                        }
                            if ( isset( $attr_data['description'] ) ) {
                                $content .= sprintf( '<p class="description">%s</p>', $attr_data['description'] );
                            }
							$content .= '</li>';
					}
						$content .= '</ul>';
						$content .= '</div>';

				}
			}
		} else {
			$content .= wpautop( __( 'Dieser Shortcode hat keine Parameter.', 'brainpress' ) );
		}
		if ( isset( $data['examples'] ) && is_array( $data['examples'] ) && ! empty( $data['examples'] ) ) {
			$content .= '<div class="cp-shortcode-examples">';
			$content .= sprintf( '<p class="cp-shortcode-subheading">%s</p>', esc_attr__( 'Beispiele:', 'brainpress' ) );
			$content .= '<code>';
			$content .= join( $data['examples'], '<br />' );
			$content .= '</code>';
					$content .= '</div>';
		}
			return $content;
	}
}
