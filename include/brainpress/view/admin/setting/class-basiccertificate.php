<?php
/**
 * Admin view.
 *
 * @package BrainPress
 */

/**
 * Settings for Basic Certificate.
 */
class BrainPress_View_Admin_Setting_BasicCertificate {

	public static function init() {

		add_filter(
			'brainpress_settings_tabs',
			array( __CLASS__, 'add_tabs' )
		);
		add_action(
			'brainpress_settings_process_basic_certificate',
			array( __CLASS__, 'process_form' ),
			10, 2
		);
		add_filter(
			'brainpress_settings_render_tab_basic_certificate',
			array( __CLASS__, 'return_content' ),
			10, 3
		);
	}

	public static function add_tabs( $tabs ) {
		$tabs['basic_certificate'] = array(
			'title' => __( 'Basiszertifikat', 'brainpress' ),
			'description' => __( 'Richte die Einstellungen für die nach Abschluss des Kurses ausgestellten Zertifikate ein.', 'brainpress' ),
			'order' => 40,
		);

		return $tabs;
	}

	public static function return_content( $content, $slug, $tab ) {
		$is_enabled = BrainPress_Core::get_setting( 'basic_certificate/enabled', true );
		$is_enabled = cp_is_true( $is_enabled );
		$use_cp_default = BrainPress_Core::get_setting( 'basic_certificate/use_cp_default', false );
		$cert_background = BrainPress_Core::get_setting( 'basic_certificate/background_image' );
		$cert_logo = BrainPress_Core::get_setting( 'basic_certificate/logo_image' );
		$cert_logo_x = BrainPress_Core::get_setting( 'basic_certificate/logo/x' );
		$cert_logo_y = BrainPress_Core::get_setting( 'basic_certificate/logo/y' );
		$cert_logo_width = BrainPress_Core::get_setting( 'basic_certificate/logo/width' );
		$cert_margin_top = BrainPress_Core::get_setting( 'basic_certificate/margin/top' );
		$cert_margin_left = BrainPress_Core::get_setting( 'basic_certificate/margin/left' );
		$cert_margin_right = BrainPress_Core::get_setting( 'basic_certificate/margin/right' );
		$cert_orientation = BrainPress_Core::get_setting( 'basic_certificate/orientation', 'L' );
		$text_color = BrainPress_Core::get_setting( 'basic_certificate/text_color' );
		$allowed_extensions = BrainPress_Helper_Utility::get_image_extensions();

		ob_start();
		?>
		<input type="hidden" name="page" value="<?php echo esc_attr( $slug ); ?>" />
		<input type="hidden" name="tab" value="<?php echo esc_attr( $tab ); ?>" />
		<input type="hidden" name="action" value="updateoptions" />
		<?php wp_nonce_field( 'update-brainpress-options', '_wpnonce' ); ?>

		<!-- Enable Checkbox -->
		<h3 class="hndle" style="cursor:auto;">
			<span><?php esc_html_e( 'Zertifikatoptionen', 'brainpress' ); ?></span>
		</h3>
        <div class="inside">
<?php
		$certificate_link = add_query_arg(
			array(
				'action' => 'edit',
				'nonce' => wp_create_nonce( 'cp_certificate_preview' ),
				'course_id' => 0,
			),
			admin_url( 'post.php' )
		);
		printf(
			'<a href="%s" target="_blank" class="button button-certificate %s">%s</a>',
			esc_url( $certificate_link ),
			esc_attr( $is_enabled? '':'hidden' ),
			esc_html__( 'Vorschau', 'brainpress' )
		);

?>
			<table class="form-table compressed">
				<tbody id="items">
					<tr>
						<td><label>
							<input type="checkbox"
								<?php checked( $is_enabled ); ?>
								name="brainpress_settings[basic_certificate][enabled]"
								class="certificate_enabled"
								value="1" />
							<?php esc_html_e( 'Basiszertifikat aktivieren', 'brainpress' ); ?>
						</label></td>
					</tr>
                    <tr class="use-cp-default <?php echo $is_enabled? '':'hidden'; ?>">
						<td><label>
							<input type="checkbox"
								<?php checked( cp_is_true( $use_cp_default ) ); ?>
								name="brainpress_settings[basic_certificate][use_cp_default]"
								class="certificate_default"
								value="1" />
							<?php esc_html_e( 'Verwende das Standard-BrainPress-Zertifikat', 'brainpress' ); ?>
						</label></td>
					</tr>
				</tbody>
			</table>
		</div>

		<!-- Certificate Layout -->
		<div class="certificate-details hidden">
		<h3 class="hndle" style="cursor:auto;">
			<span><?php esc_html_e( 'Zertifikatlayout', 'brainpress' ); ?></span>
		</h3>
		<p class="description">
			<?php esc_html_e( 'Verwende den folgenden Editor, um das Layout Deines Zertifikats zu erstellen.', 'brainpress' ); ?>
		</p>
		<p class="description">
			<?php
				$fields = apply_filters( 'brainpress_basic_certificate_vars',
					array(
						'FIRST_NAME' => '',
						'LAST_NAME' => '',
						'COURSE_NAME' => '',
						'COMPLETION_DATE' => '',
						'CERTIFICATE_NUMBER' => '',
						'UNIT_LIST' => '',
					),
					null
				);
				$field_keys = array_keys( $fields );
				esc_html_e( 'Diese Codes werden durch tatsächliche Daten ersetzt: ', 'brainpress' );
				echo implode( ', ', $field_keys );
				?>
		</p>
		<div class="inside">
			<table class="form-table compressed">
			<tbody id="items">
			<tr>
				<td colspan="2">
				<?php
				$editor_name = 'brainpress_settings[basic_certificate][content]';
				$editor_id = 'brainpress_settings_basic_certificate_content';
				$editor_content = BrainPress_Core::get_setting(
					'basic_certificate/content',
					self::default_certificate_content()
				);
				$editor_content = stripslashes( $editor_content );

				$args = array(
					'textarea_name' => $editor_name,
					'textarea_rows' => 10,
					'wpautop' => true,
					'quicktags' => true,
					'media_buttons' => true,
				);
				BrainPress_Helper_Editor::get_wp_editor( $editor_id, $editor_name, $editor_content, $args, true );
				?>
				</td>
			</tr>

			<tr>
				<th><?php esc_html_e( 'Hintergrundbild', 'brainpress' ); ?></th>
				<td>
				<div class="certificate_background_image_holder">
					<input class="image_url certificate_background_url"
						type="text"
						size="36"
						name="brainpress_settings[basic_certificate][background_image]"
						value="<?php echo esc_attr( $cert_background ); ?>"
						placeholder="<?php esc_attr_e( 'Füge eine Bild-URL hinzu oder suche nach einem Bild', 'brainpress' ); ?>" />
					<input class="certificate_background_button button-secondary"
						type="button"
						value="<?php esc_attr_e( 'Durchsuche', 'brainpress' ); ?>" />
					<div class="invalid_extension_message">
						<?php
						printf(
							esc_html__( 'Die Erweiterung der Datei ist ungültig. Bitte verwende eine der folgenden Möglichkeiten: %s', 'brainpress' ),
							implode( ', ', $allowed_extensions )
						);
						?>
					</div>
				</div>
				</td>
			</tr>

			<tr>
				<th><?php esc_html_e( 'Logo', 'brainpress' ); ?></th>
				<td>
				<div class="certificate_logo_image_holder">
					<input class="image_url certificate_logo_url"
						type="text"
						size="36"
						name="brainpress_settings[basic_certificate][logo_image]"
						value="<?php echo esc_attr( $cert_logo ); ?>"
						placeholder="<?php esc_attr_e( 'Füge eine Bild-URL hinzu oder suche nach einem Bild', 'brainpress' ); ?>" />
					<input class="certificate_logo_button button-secondary"
						type="button"
						value="<?php esc_attr_e( 'Durchsuche', 'brainpress' ); ?>" />
					<div class="invalid_extension_message">
						<?php
						printf(
							esc_html__( 'Die Erweiterung der Datei ist ungültig. Bitte verwende eine der folgenden Möglichkeiten: %s', 'brainpress' ),
							implode( ', ', $allowed_extensions )
						);
						?>
					</div>
				</div>
				</td>
			</tr>

			<tr>
				<th>
					<?php esc_html_e( 'Logo Position', 'brainpress' ); ?><br />
				</th>
				<td>
					<span><?php esc_html_e( 'X', 'brainpress' ); ?></span>
					<input type="number"
						   class="logo_x small-text"
						   name="brainpress_settings[basic_certificate][logo][x]"
						   value="<?php echo esc_attr( $cert_logo_x ); ?>" />

					<span><?php esc_html_e( 'Y', 'brainpress' ); ?></span>
					<input type="number"
						   class="logo_y small-text"
						   name="brainpress_settings[basic_certificate][logo][y]"
						   value="<?php echo esc_attr( $cert_logo_y ); ?>" />

					<span><?php esc_html_e( 'Breite', 'brainpress' ); ?></span>
					<input type="number"
						   class="logo_width small-text"
						   name="brainpress_settings[basic_certificate][logo][width]"
						   value="<?php echo esc_attr( $cert_logo_width ); ?>" />
				</td>
			</tr>

			<tr>
				<th>
					<?php esc_html_e( 'Inhaltsrand', 'brainpress' ); ?><br />
				</th>
				<td>
					<span><?php esc_html_e( 'Oben', 'brainpress' ); ?></span>
					<input type="number"
						class="margin_top small-text"
						name="brainpress_settings[basic_certificate][margin][top]"
						value="<?php echo esc_attr( $cert_margin_top ); ?>" />

					<span><?php esc_html_e( 'Links', 'brainpress' ); ?></span>
					<input type="number"
						class="margin_left small-text"
						name="brainpress_settings[basic_certificate][margin][left]"
						value="<?php echo esc_attr( $cert_margin_left ); ?>" />

					<span><?php esc_html_e( 'Rechts', 'brainpress' ); ?></span>
					<input type="number"
						class="margin_right small-text"
						name="brainpress_settings[basic_certificate][margin][right]"
						value="<?php echo esc_attr( $cert_margin_right ); ?>" />
				</td>
			</tr>

			<tr>
				<th>
					<?php esc_html_e( 'Seitenausrichtung', 'brainpress' ); ?><br />
				</th>
				<td>
					<select name="brainpress_settings[basic_certificate][orientation]"
						style="width: max-width: 200px;"
						id="cert_field_orientation">
						<option value="L" <?php selected( $cert_orientation, 'L' ); ?>>
							<?php esc_html_e( 'Landscape', 'brainpress' ); ?>
						</option>
						<option value="P" <?php selected( $cert_orientation, 'P' ); ?>>
							<?php esc_html_e( 'Portrait', 'brainpress' ); ?>
						</option>
					</select>
				</td>
			</tr>

			<tr>
				<th>
					<?php esc_html_e( 'Textfarbe', 'brainpress' ); ?><br />
				</th>
				<td style="padding: 15px 0;">
					<input
						type="text"
						name="brainpress_settings[basic_certificate][text_color]"
						id="cert_field_text_color"
						class="certificate-color-picker"
						value="<?php echo esc_attr($text_color); ?>" />
				</td>
				<?php
					wp_enqueue_script( 'wp-color-picker' );
					wp_enqueue_style( 'wp-color-picker' );
				?>
			</tr>

			</tbody>
			</table>
		</div>
		</div>
		<?php
		$content = ob_get_clean();

		return $content;
	}

	public static function default_certificate_content() {
		$msg = __(
			'<h2>%1$s %2$s</h2>
			hat erfolgreich den den Kurs 

			<h3>%3$s</h3>
			
			abgeschlossen

			<h4>Datum: %4$s</h4>
			<small>Zertifikatsnummer.: %5$s</small>', 'brainpress'
		);

		$default_certification_content = sprintf(
			$msg,
			'FIRST_NAME',
			'LAST_NAME',
			'COURSE_NAME',
			'COMPLETION_DATE',
			'CERTIFICATE_NUMBER',
			'UNIT_LIST'
		);

		return $default_certification_content;
	}

	public static function default_email_subject() {
		return sprintf(
			__( '[%s] Herzliche Glückwünsche. Du hast deinen Kurs bestanden.', 'brainpress' ),
			get_option( 'blogname' )
		);
	}

	public static function default_email_content() {
		$msg = __(
			'Hi %1$s,

			Herzliche Glückwünsche! Sie haben den Kurs %2$s abgeschlossen 

			Im Anhang findest Du Dein Abschlusszertifikat.'
			, 'brainpress'
		);

		/**
		 * download instead attach
		 */
		$msg = __(
			'Hi %1$s,

Herzliche Glückwünsche! Sie haben den Kurs %2$s abgeschlossen

Bitte %3$sLade Dein Abschlusszertifikat herunter%4$s.

Die besten Wünsche,
The %5$s Team', 'brainpress');

		$default_basic_certificate_email = sprintf(
			$msg,
			'FIRST_NAME',
			'COURSE_NAME',
			'<a href="CERTIFICATE_URL">',
			'</a>',
			'WEBSITE_ADDRESS'
		);

		return get_option(
			'brainpress_basic_certificate_email_body',
			$default_basic_certificate_email
		);
	}

	public static function process_form( $page, $tab ) {
		if ( ! isset( $_POST['action'] ) ) { return; }
		if ( 'updateoptions' != $_POST['action'] ) { return; }
		if ( 'basic_certificate' != $tab ) { return; }
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'update-brainpress-options' ) ) { return; }

		$settings = BrainPress_Core::get_setting( false ); // false: Get all settings.
		$post_settings = (array) $_POST['brainpress_settings'];

		// Sanitize $post_settings, especially to fix up unchecked checkboxes.
		if ( isset( $post_settings['basic_certificate']['enabled'] ) ) {
			$post_settings['basic_certificate']['enabled'] = true;
		} else {
			$post_settings['basic_certificate']['enabled'] = false;
		}

		/**
		 * default
		 */
		$use_cp_default = isset( $post_settings['basic_certificate']['use_cp_default'] );
		$post_settings['basic_certificate']['use_cp_default'] = $use_cp_default;

		$post_settings = BrainPress_Helper_Utility::sanitize_recursive( $post_settings );

		// Don't replace settings if there is nothing to replace.
		if ( ! empty( $post_settings ) ) {
			BrainPress_Core::update_setting(
				false, // False will replace all settings.
				BrainPress_Core::merge_settings( $settings, $post_settings )
			);
		}
	}

	public static function remove_basic_certificate_email( $defaults ) {
		if ( isset( $defaults['basic_certificate'] ) ) {
			unset( $defaults['basic_certificate'] );
		}

		return $defaults;
	}
}
