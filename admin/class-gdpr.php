<?php
/**
 * GDPR
 *
 * @since 2.2.0
 */
class BrainPress_Admin_GDPR {

	public function __construct() {
		global $wp_version;
		$is_less_496 = version_compare( $wp_version, '4.9.6', '<' );
		if ( $is_less_496 ) {
			return;
		}
		/**
		 * Add information to privacy policy page (only during creation).
		 */
		add_filter( 'wp_get_default_privacy_policy_content', array( $this, 'add_policy' ) );
		/**
		 * Adding the Personal Data Exporter
		 */
		add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'register_plugin_exporter' ), 10 );
		/**
		 * Adding the Personal Data Eraser
		 */
		add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'register_plugin_eraser' ), 10 );
	}

	/**
	 * Get plugin friendly name
	 */
	private function get_plugin_friendly_name() {
		$name = _x( 'BrainPress Plugin', 'Name des Plugins im Exporter für persönliche Daten.', 'brainpress' );
		return $name;
	}

	/**
	 * Register plugin exporter.
	 *
	 * @since 2.2.0
	 */
	public function register_plugin_exporter( $exporters ) {
		$exporters['brainpress'] = array(
			'exporter_friendly_name' => $this->get_plugin_friendly_name(),
			'callback' => array( $this, 'plugin_exporter' ),
		);
		return $exporters;
	}

	/**
	 * Export personal data.
	 *
	 * @since 2.2.0
	 */
	public function plugin_exporter( $email, $page = 1 ) {
		$user = get_user_by( 'email', $email );
		if ( ! is_a( $user, 'WP_User' ) ) {
			return;
		}
		$student_id = $user->ID;
		$export_items = array();
		$courses = BrainPress_Data_Student::get_course_enrollment_meta( $student_id );
		if ( count( $courses ) ) {
			foreach ( $courses as $course_id ) {
				$item = array(
					'group_id' => 'brainpress',
					'group_label' => $this->get_plugin_friendly_name(),
					'item_id' => 'brainpress-'.$course_id,
					'data' => array(
						array(
							'name' => __( 'Name', 'brainpress' ),
							'value' => get_the_title( $course_id ),
						),
						array(
							'name' => __( 'Datum der Einschreibung', 'brainpress' ),
							'value' => BrainPress_Data_Course::student_enrolled( $student_id, $course_id ),
						),
					),
				);
				$is_completed = BrainPress_Data_Student::is_course_complete( $student_id, $course_id );
				if ( $is_completed ) {
					$item['data'][] = array(
						'name' => __( 'Zertifikate', 'brainpress' ),
						'value' => BrainPress_Data_Certificate::get_encoded_url( $course_id, $student_id ),
					);
				}
				/**
				 * Export single course row.
				 *
				 * @since 2.2.0
				 *
				 * @param array $item Export data for course.
				 * @param string $email course email.
				 * @param object $course_id Single course ID.
				 */
				$export_items[] = apply_filters( 'brainpress_gdpr_export', $item, $email, $course_id );
			}
		}
		$export = array(
			'data' => $export_items,
			'done' => true,
		);
		return $export;
	}

	/**
	 * Register plugin eraser.
	 *
	 * @since 2.2.0
	 */
	public function register_plugin_eraser( $erasers ) {
		$erasers['brainpress'] = array(
			'eraser_friendly_name' => $this->get_plugin_friendly_name(),
			'callback'             => array( $this, 'plugin_eraser' ),
		);
		return $erasers;
	}

	/**
	 * Erase personal data.
	 *
	 * @since 2.2.0
	 */
	public function plugin_eraser( $email, $page = 1 ) {
		$messages = array();
		$user = get_user_by( 'email', $email );
		if ( ! is_a( $user, 'WP_User' ) ) {
			return array(
				'items_removed' => 0,
				'items_retained' => 0,
				'messages' => array(
					__( 'Diese E-Mail hat keine Kurse.', 'brainpress' ),
				),
				'done' => true,
			);
		}
		$student_id = $user->ID;
		$export_items = array();
		$courses = BrainPress_Data_Student::get_course_enrollment_meta( $student_id );
		BrainPress_Data_Student::remove_from_all_courses( $student_id );
		/**
		 * return
		 */
		return array(
			'items_removed' => count( $courses ),
			'items_retained' => BrainPress_Data_Student::get_course_enrollment_meta( $student_id ),
			'messages' => $messages,
			'done' => true,
		);
	}

	/**
	 * Add brainpress Policy to "Privace Policy" page during creation.
	 *
	 * @since 2.2.0
	 */
	public function add_policy( $content ) {
		$content .= '<h2>' . __( 'Plugin: BrainPress', 'brainpress' ) . '</h2>';
		$content .= $this->get_privacy_message();
		return $content;
	}

	/**
	 * Add privacy policy content for the privacy policy page.
	 *
	 * @since 3.4.0
	 */
	public function get_privacy_message() {
		$content = wp_kses_post( apply_filters( 'brainpress_privacy_policy_content', wpautop( __( '
Wir sammeln Informationen über Dich während der Anmeldung zu unseren Kursen oder beim Auschecken auf unserer Website.

<h3>Was wir sammeln und lagern</h3>

Während Du unsere Website besuchst, verfolgen wir Folgendes:

<ul>
    <li>Kurse, für die Du dich eingeschrieben hast.</li>
    <li>Kurs, Deine Antworten, Kommentare, Dateien.</li>
</ul>

Wenn Du bei uns einkaufst, wirst Du gebeten, Informationen wie Deinen Namen, Deine Rechnungsadresse, Lieferadresse, E-Mail-Adresse, Telefonnummer, Kreditkarten-/Zahlungsdetails und optionale Kontoinformationen wie Benutzername und Passwort anzugeben. Wir verwenden diese Informationen, um
<ul>
	<li>Senden von Informationen zu Deinem Konto und Deiner Bestellung.</li>
	<li>Reagieren auf Deine Anfragen, einschließlich Rückerstattungen und Beschwerden.</li>
	<li>Verarbeiten von Zahlungen und verhindern von Betrug.</li>
</ul>

Wenn Du ein Konto erstellst, speichern wir Deinen Namen, Adresse, E-Mail-Adresse und Telefonnummer, anhand derer die Kasse für zukünftige Bestellungen ausgefüllt wird.

Wir werden Bestellinformationen für XXX Jahre für Steuer- und Buchhaltungszwecke speichern. Dies umfasst Deinen Namen, E-Mail-Adresse sowie Rechnungs- und Lieferadressen.

Wir speichern auch Kommentare oder Bewertungen, wenn Du diese hinterlassen möchtest.

<h3>Wer in unserem Team hat Zugriff</h3>

Mitglieder unseres Teams haben Zugriff auf die Informationen, die Du uns zur Verfügung stellst. Administratoren, Ausbilder und Moderatoren können auf Folgendes zugreifen:
<ul>
	<li>Kundeninformationen wie Name, E-Mail-Adresse und Rechnungsstellung.</li>
</ul>

Unsere Teammitglieder haben Zugriff auf diese Informationen, um Bestellungen zu erfüllen, Rückerstattungen zu verarbeiten und Dich zu unterstützen.

<h3>Was wir mit anderen teilen</h3>

<h4>Zahlungen</h4>
Wir akzeptieren Zahlungen über PayPal. Bei der Verarbeitung von Zahlungen werden einige Ihrer Daten an PayPal weitergeleitet, einschließlich Informationen, die zur Verarbeitung oder Unterstützung der Zahlung erforderlich sind, z.B. die Kaufsumme und die Rechnungsinformationen.
Bitte sieh Dir die <a href="https://www.paypal.com/us/webapps/mpp/ua/privacy-full">PayPal Privacy Policy</a> für mehr Informationen an.
', 'brainpress' ) ) ) );
		return $content;
	}
}

