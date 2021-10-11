<?php

class BrainPress_View_Admin_Setting_Email {

	public static function init() {
		add_filter(
			'brainpress_settings_tabs',
			array( __CLASS__, 'add_tabs' )
		);
		add_action(
			'brainpress_settings_process_email',
			array( __CLASS__, 'process_form' ),
			10, 2
		);
		add_filter(
			'brainpress_settings_render_tab_email',
			array( __CLASS__, 'return_content' ),
			10, 3
		);
	}

	public static function add_tabs( $tabs ) {
		$tabs['email'] = array(
			'title' => __( 'E-Mail Einstellungen', 'brainpress' ),
			'description' => __( 'Richte die E-Mail-Vorlagen ein, die an Benutzer gesendet werden sollen.', 'brainpress' ),
			'order' => 10,
		);

		return $tabs;
	}

	public static function return_content( $content, $slug, $tab ) {
		$content = '
			<input type="hidden" name="page" value="' . esc_attr( $slug ) .'"/>
			<input type="hidden" name="tab" value="' . esc_attr( $tab ) .'"/>
			<input type="hidden" name="action" value="updateoptions"/>
		' . wp_nonce_field( 'update-brainpress-options', '_wpnonce', true, false );

		$email_sections = BrainPress_Helper_Utility::sort_on_key(
			BrainPress_Helper_Setting_Email::get_settings_sections(),
			'order'
		);

		$default_settings = BrainPress_Helper_Setting_Email::get_defaults();

		$content .= '<div class="cp-content">';
		foreach ( $email_sections as $key => $section ) {
			$email_enabled = (boolean) BrainPress_Core::get_setting('email/' . $key . '/enabled', $default_settings[ $key ]['enabled']);
			$content .= '<div class="email-template cp-content-box collapsed">';
			$content .= '<h3 class="hndle">' . esc_html( $section['title'] ) . '</h3>';
			$content .= '<div class="inside">';
			if ( ! empty( $section['description'] ) ) {
				$content .= '<p class="description">' . esc_html( $section['description'] ) . '</p>';
			}
			$content .= '
					<table class="form-table compressed email-fields">
						<tbody id="items">';

			$content .= '
							<tr>
								<th>' . esc_html__( 'Aktiviert', 'brainpress' ) . '</th>
								<td>
									<input type="hidden" name="brainpress_settings[email][' . $key . '][enabled]" value="0" />
									<input type="checkbox" class="widefat" name="brainpress_settings[email][' . $key . '][enabled]" value="1" ' . checked($email_enabled, true, false) . ' />
								</td>
							</tr>
			';

			$content .= '
							<tr>
								<th>' . esc_html__( 'Von Name', 'brainpress' ) . '</th>
								<td><input type="text" class="widefat" name="brainpress_settings[email][' . $key . '][from]" value="' . esc_attr( BrainPress_Core::get_setting( 'email/' . $key . '/from', $default_settings[ $key ]['from'] ) ) . '"/></td>
							</tr>
			';
			$content .= '
							<tr>
								<th>' . esc_html__( 'Von Email', 'brainpress' ) . '</th>
								<td><input type="text" class="widefat" name="brainpress_settings[email][' . $key . '][email]" value="' . esc_attr( BrainPress_Core::get_setting( 'email/' . $key . '/email', $default_settings[ $key ]['email'] ) ) . '"/></td>
							</tr>
			';
			$content .= '
							<tr>
								<th>' . esc_html__( 'Betreff', 'brainpress' ) . '</th>
								<td><input type="text" class="widefat" name="brainpress_settings[email][' . $key . '][subject]" value="' . esc_attr( BrainPress_Core::get_setting( 'email/' . $key . '/subject', $default_settings[ $key ]['subject'] ) ) . '"/></td>
							</tr>
			';
			$content .= '
							<tr>
								<th>
								' . esc_html__( 'Email Inhalt', 'brainpress' ) . '</th>
								<td>
								<p class="description">' . esc_html( $section['content_help_text'] ) . '</p>';

			$editor_name = 'brainpress_settings[email][' . $key . '][content]';
			$editor_id = 'brainpress_settings_email_' . $key . '_content';
			$editor_content = stripslashes( BrainPress_Core::get_setting( 'email/' . $key . '/content', $default_settings[ $key ]['content'] ) );

			$args = array(
				'textarea_name' => $editor_name,
				'textarea_rows' => 10,
				'wpautop' => true,
				'teeny' => true,
				'media_buttons' => false,
					'quicktags' => true,
			);
			$args = apply_filters( 'brainpress_element_editor_args', $args, $editor_name, $editor_id );
			$content .= BrainPress_Helper_Editor::get_wp_editor( $editor_id, $editor_name, $editor_content, $args );

			$content .= '</td></tr>';
			$content .= '
						</tbody>
					</table>
				</div>';
			$content .= '</div>';
		}
		/**
		 * Add this hook for now until layout is fixed.
		 **/
//		$content .= apply_filters( 'brainpress_email_settings_sections', $email_sections );
		$content .= '</div>';

		return $content;
	}

	public static function process_form( $page, $tab ) {
		if ( empty( $_POST['_wpnonce'] ) ) { return; }
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'update-brainpress-options' ) ) { return; }
		if ( empty( $_POST['action'] ) ) { return; }
		if ( 'updateoptions' != $_POST['action'] ) { return; }
		if ( 'email' != $tab ) { return; }

		$settings = BrainPress_Core::get_setting( false );
		$post_settings = (array) $_POST['brainpress_settings'];
		$post_settings = BrainPress_Helper_Utility::sanitize_recursive( $post_settings );
		$post_settings = stripslashes_deep( $post_settings );

		// Don't replace settings if there is nothing to replace.
		if ( ! empty( $post_settings ) ) {
			BrainPress_Core::update_setting(
				false, // false .. replace all settings.
				BrainPress_Core::merge_settings( $settings, $post_settings )
			);
		}
	}
}
