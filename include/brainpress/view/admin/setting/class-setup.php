<?php

class BrainPress_View_Admin_Setting_Setup {

	public static function init() {
		add_filter(
			'brainpress_settings_tabs',
			array( __CLASS__, 'add_tabs' )
		);
		add_action(
			'brainpress_settings_process_setup',
			array( __CLASS__, 'process_form' ), 10, 2
		);
		add_filter(
			'brainpress_settings_render_tab_setup',
			array( __CLASS__, 'return_content' ),
			10, 3
		);

		if ( isset( $_GET['tab'] ) && 'setup' == $_GET['tab'] ) {
			add_filter(
				'brainpress_settings_tabs_content',
				array( __CLASS__, 'remove_tabs' ),
				10, 2
			);
			add_filter(
				'brainpress_settings_page_main',
				array( __CLASS__, 'return_content' )
			);

			// TODO: This is premium only. move to premium folder!
			add_action(
				'brainpress_settings_page_pre_render',
				array( __CLASS__, 'remove_dashboard_notification' )
			);
		}
	}

	public static function add_tabs( $tabs ) {
		$tabs['setup'] = array(
			'title' => __( 'HANDBUCH', 'brainpress' ),
			'description' => __( 'Dies ist die Beschreibung dessen, was Du auf dieser Seite tun kannst.', 'brainpress' ),
			'order' => 70,
			'class' => 'setup_tab',
		);

		return $tabs;
	}

	public static function return_content( $content ) {
		ob_start();
?>
<div class="wrap about-wrap cp-wrap">
	<h1><?php _e( 'Willkommen zu', 'brainpress' ); ?> <?php echo BrainPress::$name; ?></h1>

	<div class="about-text">
<?php
		printf( __( '%s hat ein paar Dinge getan, um dich auf den Weg zu bringen.', 'brainpress' ), BrainPress::$name );
?>
		<br/>
<?php
		_e( 'Es wurden einige dynamische Seiten mit den Bezeichnungen "Kurse" und "Dashboard" erstellt und Deiner Navigation hinzugefügt.', 'brainpress' );
?>
		<br/>
<?php
		printf( __( 'Wenn diese auf Deiner Webseite und Deinem Theme nicht sichtbar sind, musst Du möglicherweise Deine %s überprüfen.', 'brainpress' ), '<a href="' . admin_url( 'nav-menus.php' ) . '">' . __( 'Menü Einstellungen', 'brainpress' ) . '</a>' );
?>
		<br/>
<?php
		printf( __( '%s hat auch PSeCommerce mit dabei - Falls Du Kurse verkaufen möchtest, dann installiere es einfach über das BrainPress Dashboard.', 'brainpress' ), BrainPress::$name );
?>
		<br/>
		<?php _e( 'Für diejenigen unter Euch, die Ihre fantastischen Kurse verkaufen möchten, musst Du ein Zahlungsgateway aktivieren und einrichten. Aber dazu später mehr.', 'brainpress' ); ?>
	</div>

	<h1><?php _e( 'Lass uns anfangen', 'brainpress' ); ?></h1>

	<div class="changelog">
		<h3><?php _e( 'Schritt 1. Erstelle einen Kurs', 'brainpress' ); ?></h3>

		<div class="about-text">
			<ul>
				<li><?php _e( 'Kurstitel und Beschreibung hinzufügen', 'brainpress' ); ?></li>
				<li><?php _e( 'Kursleiter zuweisen', 'brainpress' ); ?></li>
				<li><?php _e( 'Konfiguriere die Anwesenheits- und Zugriffseinstellungen', 'brainpress' ); ?></li>
				<li><?php _e( 'Richte Zahlungsgateways für bezahlte Kurse ein', 'brainpress' ); ?></li>
			</ul>

		</div>
		<br/>
		<img alt="" src="<?php echo esc_attr_e( BrainPress::$url . 'asset/img/quick-setup/step-1.jpg' ); ?>" class="image-66">
	</div>

	<div class="changelog">
		<h3><?php _e( 'Schritt 2. Kursinhalt hinzufügen', 'brainpress' ); ?></h3>

		<div class="about-text">
<?php
		_e( 'Die Kurse sind nach Einheiten gegliedert. Einheiten bestehen aus Modulen, die auf einer einzelnen Seite oder über mehrere Seiten dargestellt werden können. Module umfassen', 'brainpress' );
?>
			<ul>
				<li><?php _e( 'Text, Video & Audio', 'brainpress' ); ?></li>
				<li><?php _e( 'Datei hochladen und herunterladen ', 'brainpress' ); ?></li>
				<li><?php _e( 'Multiple- und Single-Choice-Fragen', 'brainpress' ); ?></li>
				<li><?php _e( 'Testantwortfelder', 'brainpress' ); ?></li>
			</ul>

		</div>
		<img alt="" src="<?php echo esc_attr_e( BrainPress::$url . 'asset/img/quick-setup/step-2.jpg' ); ?>" class="image-66">

	</div>

	<div class="changelog">
		<h3><?php _e( 'Schritt 3. Melde die Studenten an', 'brainpress' ); ?></h3>

		<div class="about-text">
<?php
		_e( 'Konfiguriere die Studentenregistrierung und wähle eine der beiden Optionen:', 'brainpress' );
?>
			<ul>
				<li><?php _e( 'Füge Studenten mit oder ohne Passcode-Einschränkung manuell hinzu', 'brainpress' ); ?></li>
				<li><?php _e( 'Melde die Studenten nach der Registrierung und/oder Zahlung automatisch an', 'brainpress' ); ?></li>
			</ul>

		</div>

	</div>

	<div class="changelog">
		<h3><?php _e( 'Schritt 4. Veröffentliche Deinen Kurs!', 'brainpress' ); ?></h3>

		<div class="about-text">
<?php
		_e( 'Es gibt viele andere Funktionen in BrainPress, aber dies sind die Grundlagen, um Dich zum Laufen zu bringen. Jetzt ist es Zeit, den Kurs zu veröffentlichen und Deinen Studenten beim Lernen zuzusehen', 'brainpress' );
?>
			<br/><br/>

		</div>
		<img alt="" src="<?php esc_attr_e( BrainPress::$url . 'asset/img/quick-setup/step-3.jpg' ); ?>" class="image-66">

	</div>

	<div class="changelog">
		<h3><?php _e( 'Schritt 5. Kursmanagement', 'brainpress' ); ?></h3>

		<div class="about-text">
			<ul>
				<li><?php _e( 'Verwalte Kursleiter und Studenten', 'brainpress' ); ?></li>
				<li><?php _e( 'Verwalten der Benotung der eingereichten Arbeiten der Studenten', 'brainpress' ); ?></li>
				<li><?php _e( 'Generiere ein einheitliches Kurs-/standortweites Reporting', 'brainpress' ); ?></li>
			</ul>
		</div>

<?php
if ( current_user_can( 'manage_options' ) && ! get_option( 'permalink_structure' ) ) {
	// toplevel_page_courses
	$screen = get_current_screen();

	$show_warning = false;

	if ( 'toplevel_page_courses' == $screen->id && isset( $_GET['quick_setup'] ) ) {
		$show_warning = true;
	}

	if ( $show_warning ) {
?>
		<div class="permalinks-error">
			<h4><?php _e( 'Für die Verwendung von BrainPress sind hübsche Permalinks erforderlich.', 'brainpress' ); ?></h4>

			<p><?php _e( 'Klicke auf die Schaltfläche unten, um Deine Permalinks einzurichten.', 'brainpress' ); ?></p>
			<a href="<?php echo admin_url( 'options-permalink.php' ); ?>" class="button button-units save-unit-button setup-permalinks-button"><?php _e( 'Permalinks einrichten', 'brainpress' ); ?></a>
		</div>
<?php
	}
} else {
	$url = admin_url('post-new.php?post_type=' . BrainPress_Data_Course::get_post_type_name());
?>
	<a href="<?php echo esc_url( $url ); ?>" class="button button-units save-unit-button start-course-button"><?php _e( 'Erstelle jetzt Deinen eigenen Kurs &rarr;', 'brainpress' ); ?></a>
<?php
}
?>
	</div>
</div>
<?php
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

	public static function remove_tabs( $wrapper, $content ) {
		$wrapper = $content;
		return $wrapper;
	}

	public static function remove_dashboard_notification() {
		if ( isset( $_GET['tab'] ) && 'setup' === $_GET['tab'] ) {
			global $wpmudev_notices;
			$wpmudev_notices = array();
		}
	}


	public static function process_form() {
	}
}
