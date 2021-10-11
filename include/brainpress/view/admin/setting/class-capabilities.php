<?php
/**
 * Admin view.
 *
 * @package BrainPress
 */

/**
 * Capabilities for Instructors.
 */
class BrainPress_View_Admin_Setting_Capabilities {

	public static function init() {
		add_filter(
			'brainpress_settings_tabs',
			array( __CLASS__, 'add_tabs' )
		);
		add_action(
			'brainpress_settings_process_capabilities',
			array( __CLASS__, 'process_form' ),
			10, 2
		);
		add_filter(
			'brainpress_settings_render_tab_capabilities',
			array( __CLASS__, 'return_content' ),
			10, 3
		);
	}

	public static function add_tabs( $tabs ) {
		if ( current_user_can( 'manage_options' ) ) {
			/*
			 * Instructors can be allowed to access the Settings submenu.
			 * But the "Instructor Capabilities" tab is only available to
			 * WordPress admins, so instructors cannot edit their own caps...
			 */
			$tabs['capabilities'] = array(
				'title' => __( 'Kursleiterfähigkeiten', 'brainpress' ),
				'description' => sprintf(
					'%s %s',
					__( 'Hier kannst Du entscheiden, was Deine Kursleiter auf Deiner Seite tun können. Dies sind spezielle Funktionen, die nur für BrainPress relevant sind.', 'brainpress' ),
					__( 'HINWEIS: Aus Sicherheitsgründen ist diese Seite nur für WordPress-Administratoren verfügbar!', 'brainpress' )
				),
				'order' => 30,
			);
		}

		return $tabs;
	}

	public static function return_content( $content, $slug, $tab ) {
		$instructor_capabilities = BrainPress_Data_Capabilities::get_instructor_capabilities();
		$boxes = self::_capability_boxes();

		ob_start();
		?>
		<input type="hidden" name="page" value="' . esc_attr( $slug ) . '"/>
		<input type="hidden" name="tab" value="' . esc_attr( $tab ) . '"/>
		<input type="hidden" name="action" value="updateoptions"/>
		<?php wp_nonce_field( 'update-brainpress-options', '_wpnonce' ); ?>

		<div class="capability-list">

		<?php foreach ( $boxes as $group => $data ) : ?>
			<div class="cp-content-box <?php echo esc_attr( $group ); ?>">
			<h3 class="hndle">
				<span><?php echo esc_html( $data['title'] ); ?></span>
			</h3>
			<div class="inside">
				<table class="form-table compressed">
					<tbody id="items">

						<?php foreach ( $data['items'] as $key => $value ) : ?>
							<?php $checked = ! empty( $instructor_capabilities[ $key ] ); ?>
							<?php $name = 'brainpress_settings[instructor][capabilities][' . $key . ']'; ?>

							<tr class="<?php echo esc_attr( $key ); ?>">
								<td>
									<label>
										<input type="checkbox"
											<?php checked( $checked ); ?>
											name="<?php echo esc_attr( $name ); ?>"
											value="1" />
										<?php echo esc_html( $value ); ?>
									</label>
								</td>
							</tr>
						<?php endforeach; ?>

					</tbody>
				</table>
			</div>
			</div>
		<?php endforeach; ?>

		</div>
		<?php

		$content = ob_get_clean();

		return $content;
	}

	private static function _capability_boxes() {
		$options = array(
			'general' => array(
				'title' => __( 'Allgemein', 'brainpress' ),
				'items' => self::_instructor_capabilities_general(),
			),
			'course' => array(
				'title' => __( 'Kurse', 'brainpress' ),
				'items' => self::_instructor_capabilities_courses(),
			),
			'course-category' => array(
				'title' => __( 'Kurskategorien', 'brainpress' ),
				'items' => self::_instructor_capabilities_course_categories(),
			),
			'course-unit' => array(
				'title' => __( 'Einheiten', 'brainpress' ),
				'items' => self::_instructor_capabilities_units(),
			),
			'instructor' => array(
				'title' => __( 'Kursleiter', 'brainpress' ),
				'items' => self::_instructor_capabilities_instructors(),
			),
			'facilitator' => array(
				'title' => __( 'Moderatoren', 'brainpress' ),
				'items' => self::_facilitator_capabilities(),
			),
			'student' => array(
				'title' => __( 'Studenten', 'brainpress' ),
				'items' => self::_instructor_capabilities_students(),
			),
			'notification' => array(
				'title' => __( 'Benachrichtigungen', 'brainpress' ),
				'items' => self::_instructor_capabilities_notifications(),
			),
			'discussion' => array(
				'title' => __( 'Diskussionen', 'brainpress' ),
				'items' => self::_instructor_capabilities_discussions(),
			),
		);
		/**
		 * Add this capabilities only when PSeCommerce is acctive.
		 */
		$is_psecommerce_active = apply_filters( 'brainpress_is_psecommerce_active', false );
		if ( $is_psecommerce_active ) {
			$options['wordpress'] = array(
				'title' => __( 'Gewähre Standard-WordPress-Funktionen', 'brainpress' ),
				'items' => self::_instructor_capabilities_posts_and_pages(),
			);
		}
		return $options;
	}

	private static function _instructor_capabilities_general() {
		return array(
			'brainpress_dashboard_cap' => __( 'Sieht das Hauptmenü von BrainPress', 'brainpress' ),
			'brainpress_courses_cap' => __( 'Greift auf die Untermenüs Kurse zu', 'brainpress' ),
			// 'brainpress_instructors_cap' => __( 'Access the Intructors submenu', 'brainpress' ),
			'brainpress_students_cap' => __( 'Ruft das Untermenü Studenten auf', 'brainpress' ),
			'brainpress_assessment_cap' => __( 'Greift auf das Untermenü Bewertung zu', 'brainpress' ),
			'brainpress_reports_cap' => __( 'Greift auf das Untermenü Berichte zu', 'brainpress' ),
			'brainpress_notifications_cap' => __( 'Greift auf das Untermenü Benachrichtigungen zu', 'brainpress' ),
			'brainpress_discussions_cap' => __( 'Ruft das Untermenü Forum auf', 'brainpress' ),
			'brainpress_settings_cap' => __( 'Ruft das Untermenü Einstellungen auf', 'brainpress' ),
		);
	}

	private static function _instructor_capabilities_courses() {
		return array(
			'brainpress_create_course_cap' => __( 'Erstellt neue Kurse', 'brainpress' ),
			'brainpress_view_others_course_cap' => __( 'Sieht Kurse anderer Kursleiter ein', 'brainpress' ),
			'brainpress_update_my_course_cap' => __( 'Eigene Kurse aktualisieren', 'brainpress' ),
			'brainpress_update_course_cap' => __( 'Aktualisiert einen beliebigen Kurs', 'brainpress' ),
			// 'brainpress_update_all_courses_cap' => __( 'Update ANY course', 'brainpress' ),
			'brainpress_delete_my_course_cap' => __( 'Löscht eigene Kurse', 'brainpress' ),
			'brainpress_delete_course_cap' => __( 'Löscht einen zugewiesenen Kurs', 'brainpress' ),
			// 'brainpress_delete_all_courses_cap' => __( 'Delete ANY course', 'brainpress' ),
			'brainpress_change_my_course_status_cap' => __( 'Status der eigenen Kurse ändern', 'brainpress' ),
			'brainpress_change_course_status_cap' => __( 'Ändert den Status eines zugewiesenen Kurses', 'brainpress' ),
			// 'brainpress_change_all_courses_status_cap' => __( 'Change status of ALL course', 'brainpress' ),
		);
	}

	private static function _instructor_capabilities_course_categories() {
		return array(
			'brainpress_course_categories_manage_terms_cap' => __( 'Anzeigen und Erstellen von Kategorien', 'brainpress' ),
			'brainpress_course_categories_edit_terms_cap' => __( 'Bearbeitet eine beliebige Kategorie', 'brainpress' ),
			'brainpress_course_categories_delete_terms_cap' => __( 'Löscht eine beliebige Kategorie', 'brainpress' ),
		);
	}

	private static function _facilitator_capabilities() {
		return array(
			'brainpress_assign_my_course_facilitator_cap' => __( 'Weist Moderator dem eigenen Kurs zu', 'brainpress' ),
			'brainpress_assign_facilitator_cap' => __( 'Weist jedem Kurs einen Moderator zu', 'brainpress' ),
		);
	}

	private static function _instructor_capabilities_units() {
		return array(
			'brainpress_create_course_unit_cap' => __( 'Erstellt neue Kurseinheiten', 'brainpress' ),
			'brainpress_view_all_units_cap' => __( 'Einheiten in jedem Kurs anzeigen (auch von anderen Kursleitern)', 'brainpress' ),
			'brainpress_update_my_course_unit_cap' => __( 'Eigene Einheiten aktualisieren', 'brainpress' ),
			'brainpress_update_course_unit_cap' => __( 'Aktualisiert alle Einheiten innerhalb der zugewiesenen Kurse', 'brainpress' ),
			// 'brainpress_update_all_courses_unit_cap' => __( 'Update units of ALL courses', 'brainpress' ),
			'brainpress_delete_my_course_units_cap' => __( 'Eigene Einheiten löschen', 'brainpress' ),
			'brainpress_delete_course_units_cap' => __( 'Löscht alle Einheiten innerhalb der zugewiesenen Kurse', 'brainpress' ),
			// 'brainpress_delete_all_courses_units_cap' => __( 'Delete units of ALL courses', 'brainpress' ),
			'brainpress_change_my_course_unit_status_cap' => __( 'Status der eigenen Einheiten ändern', 'brainpress' ),
			'brainpress_change_course_unit_status_cap' => __( 'Ändert den Status einer Einheit innerhalb der zugewiesenen Kurse', 'brainpress' ),
			// 'brainpress_change_all_courses_unit_status_cap' => __( 'Change status of any unit of ALL courses', 'brainpress' ),
		);
	}

	private static function _instructor_capabilities_instructors() {
		return array(
			'brainpress_assign_and_assign_instructor_my_course_cap' => __( 'Weist anderen Kursleitern eigene Kurse zu', 'brainpress' ),
			'brainpress_assign_and_assign_instructor_course_cap' => __( 'Weist jedem Kurs andere Kursleiter zu', 'brainpress' ),
		);
	}

	private static function _instructor_capabilities_students() {
		return array(
			'brainpress_invite_my_students_cap' => __( 'Ladet die Studenten zu eigenen Kursen ein', 'brainpress' ),
			'brainpress_invite_students_cap' => __( 'Ladet die Studenten zu jedem Kurs ein', 'brainpress' ),
			'brainpress_withdraw_my_students_cap' => __( 'Zieht Studenten aus eigenen Kursen zurück', 'brainpress' ),
			'brainpress_withdraw_students_cap' => __( 'Zieht Studenten aus jedem Kurs zurück', 'brainpress' ),
			'brainpress_add_move_my_students_cap' => __( 'Fügt Studenten zu eigenen Kursen hinzu', 'brainpress' ),
			'brainpress_add_move_students_cap' => __( 'Fügt jedem Kurs Studenten hinzu', 'brainpress' ),
			'brainpress_add_move_my_assigned_students_cap' => __( 'Fügt Studenten zu zugewiesenen Kursen hinzu', 'brainpress' ),
			// 'brainpress_change_my_students_group_class_cap' => __( 'Change students group within own courses', 'brainpress' ),
			// 'brainpress_change_students_group_class_cap' => __( 'Change students group in any course', 'brainpress' ),
			//'brainpress_send_bulk_my_students_email_cap' => __( 'Send bulk email to students of own courses', 'brainpress' ),
			//'brainpress_send_bulk_students_email_cap' => __( 'Send bulk email to all students', 'brainpress' ),
			//'brainpress_add_new_students_cap' => __( 'Create new users with student role to the blog', 'brainpress' ),
			//'brainpress_delete_students_cap' => __( 'Delete students (deletes ALL associated course records)', 'brainpress' ),
		);
	}

	private static function _instructor_capabilities_notifications() {
		return array(
			'brainpress_create_my_notification_cap' => __( 'Erstellt neue Benachrichtigungen für eigene Kurse', 'brainpress' ),
			'brainpress_create_my_assigned_notification_cap' => __( 'Erstellt neue Benachrichtigungen für zugewiesene Kurse', 'brainpress' ),
			'brainpress_update_my_notification_cap' => __( 'Aktualisiert eigene veröffentlichte Benachrichtigung', 'brainpress' ),
			'brainpress_update_notification_cap' => __( 'Aktualisiert jede Benachrichtigung', 'brainpress' ),
			'brainpress_delete_my_notification_cap' => __( 'Löscht eigene Benachrichtigungen', 'brainpress' ),
			'brainpress_delete_notification_cap' => __( 'Löscht jede Benachrichtigung', 'brainpress' ),
			'brainpress_change_my_notification_status_cap' => __( 'Ändert den Status eigener Benachrichtigungen', 'brainpress' ),
			'brainpress_change_notification_status_cap' => __( 'Ändert den Status jeder Benachrichtigung', 'brainpress' ),
		);
	}

	private static function _instructor_capabilities_discussions() {
		return array(
			'brainpress_create_my_discussion_cap' => __( 'Erstellt neue Diskussionen für eigene Kurse', 'brainpress' ),
			'brainpress_create_my_assigned_discussion_cap' => __( 'Erstellt neue Diskussionen für zugewiesene Kurse', 'brainpress' ),
			'brainpress_update_my_discussion_cap' => __( 'Aktualisiert seine eigenen veröffentlichten Diskussionen', 'brainpress' ),
			'brainpress_update_discussion_cap' => __( 'Aktualisiert jede Diskussion', 'brainpress' ),
			'brainpress_delete_my_discussion_cap' => __( 'Löscht eigene Diskussionen', 'brainpress' ),
			'brainpress_delete_discussion_cap' => __( 'Löscht jede Diskussion', 'brainpress' ),
			'brainpress_change_my_discussion_status_cap' => __( 'Status der eigenen Diskussionen ändern', 'brainpress' ),
			'brainpress_change_discussion_status_cap' => __( 'Ändert den Status jeder Diskussion', 'brainpress' ),
		);
	}

	private static function _instructor_capabilities_posts_and_pages() {
		return array(
			'edit_pages' => __( 'Seiten bearbeiten (erforderlich für PSeCommerce)', 'brainpress' ),
			'edit_published_pages' => __( 'Veröffentlichte Seiten bearbeiten', 'brainpress' ),
			'edit_posts' => __( 'Beiträge bearbeiten', 'brainpress' ),
			'publish_pages' => __( 'Seiten veröffentlichen', 'brainpress' ),
			'publish_posts' => __( 'Beiträge veröffentlichen', 'brainpress' ),
		);
	}


	public static function process_form( $page, $tab ) {
		if ( ! isset( $_POST['action'] ) ) { return; }
		if ( 'updateoptions' != $_POST['action'] ) { return; }
		if ( 'capabilities' != $tab ) { return; }
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'update-brainpress-options' ) ) { return; }

		$settings = BrainPress_Core::get_setting( false ); // false: Get all settings.
		$post_settings = (array) $_POST['brainpress_settings'];

		// Sanitize $post_settings, especially to fix up unchecked checkboxes.
		$caps = array_keys( BrainPress_Data_Capabilities::$capabilities['instructor'] );
		$set_caps = array_keys( $post_settings['instructor']['capabilities'] );

		foreach ( $caps as $cap ) {
			$is_set = in_array( $cap, $set_caps );
			$post_settings['instructor']['capabilities'][ $cap ] = $is_set;
		}

		// Don't replace settings if there is nothing to replace.
		if ( ! empty( $post_settings ) ) {
			BrainPress_Core::update_setting(
				false, // False will replace all settings.
				BrainPress_Core::merge_settings( $settings, $post_settings )
			);
		}
	}
}
