<div class="step-title step-7">
	<?php _e( 'Schritt 7 &ndash; Kursabschluss', 'brainpress' ); ?>
	<div class="status <?php echo $setup_class; ?>"></div>
</div>

<div class="cp-box-content step-content step-7">
	<input type="hidden" name="meta_setup_step_7" value="saved" />

	<div class="wide minimum-grade">
		<label class="required" for="meta_minimum_grade_required"><?php _e( 'Mindestbewertung erforderlich', 'brainpress' ); ?></label>
		<input type="number" id="meta_minimum_grade_required" name="meta_minimum_grade_required" value="<?php echo (int) $minimum_grade_required; ?>" min="0" max="100" class="text-small" />
		<p class="description"><?php _e( 'Die Mindestbewertung, die erforderlich ist, um den Abschluss des Kurses zu markieren und Kurszertifikate zu senden.', 'brainpress' ); ?></p>
	</div>

	<!-- Course Pre Completion Page -->
	<div class="wide page-pre-completion">
		<label><?php _e( 'Vorab-Abschlussseite-Seite', 'brainpress' ); ?></label>
		<p class="description"><?php _e( 'Verwende die Felder unten, um eine benutzerdefinierte Vorab-Abschlussseite anzuzeigen, nachdem der Student den Kurs abgeschlossen hat, aber eine endgültige Bewertung durch die Kursleiter erforderlich ist.', 'brainpress' ); ?></p>

		<label for="meta_pre_completion_title" class="required"><?php _e( 'Seitentitel', 'brainpress' ); ?></label>
		<input type="text" class="wide" name="meta_pre_completion_title" value="<?php echo esc_attr( $precompletion['title'] ); ?>" />
		<label for="meta_pre_completion_content" class="required"><?php _e( 'Seiteninhalt', 'brainpress' ); ?></label>
		<?php
		echo $token_message;
		echo BrainPress_Helper_Editor::get_wp_editor( 'pre-completion-content', 'meta_pre_completion_content', $precompletion['content'] );
		?>
	</div>

	<!-- Course Completion -->
	<div class="wide page-completion">
		<label><?php _e( 'Seite zum Abschluss des Kurses', 'brainpress' ); ?></label>
		<p class="description"><?php _e( 'Verwende die Felder unten, um nach erfolgreichem Abschluss des Kurses eine benutzerdefinierte Seite anzuzeigen.', 'brainpress' ); ?></p>
		<label for="meta_course_completion_title" class="required"><?php _e( 'Seitentitel', 'brainpress' ); ?></label>
		<input type="text" class="widefat" name="meta_course_completion_title" value="<?php echo esc_attr( $completion['title'] ); ?>" />

		<label for="meta_course_completion_content" class="required"><?php _e( 'Seiteninhalt', 'brainpress' ); ?></label>
		<?php
			echo $token_message;
			echo BrainPress_Helper_Editor::get_wp_editor( 'course-completion-editor-content', 'meta_course_completion_content', $completion['content'] );
		?>
	</div>

	<!-- Course Faield Page -->
	<div class="wide page-failed">
		<label><?php _e( 'Gescheitert Seite', 'brainpress' ); ?></label>
		<p class="description"><?php _e( 'Verwende die Felder unten, um die Gescheitert Seite anzuzeigen, wenn ein Student einen Kurs abgeschlossen hat, aber die erforderliche Mindestbewertung nicht erreicht hat.', 'brainpress' ); ?></p>
		<label for="meta_course_failed_title" class="required"><?php _e( 'Seitentitel', 'brainpress' ); ?></label>
		<input type="text" class="widefat" name="meta_course_failed_title" value="<?php echo $failed['title']; ?>" />

		<label for="meta_course_field_content" class="required"><?php _e( 'Seiteninhalt', 'brainpress' ); ?></label>
		<?php
			echo $token_message;
			echo BrainPress_Helper_Editor::get_wp_editor( 'course-failed-content', 'meta_course_failed_content', $failed['content'] );
		?>
	</div>

	<!-- Course Certificate -->
	<div class="wide course-certificate">
		<br />
		<h3><?php echo _e( 'Benutzerdefiniertes Zertifikat', 'brainpress' ); ?></h3>
		<a href="<?php echo esc_url( $certificate['preview_link'] ); ?>" target="_blank" class="button button-default btn-cert <?php echo false === $certificate['enabled'] ? 'hidden' : ''; ?>" style="float:right;margin-top:-35px;">
			<?php echo _e( 'Vorschau', 'brainpress' ); ?>
		</a>

		<?php
		$one = array(
			'meta_key' => 'basic_certificate',
			'label' => __( 'Verwende für diesen Kurs ein benutzerdefiniertes Zertifikat.', 'brainpress' ),
			'default' => false,
		);
		echo BrainPress_Helper_UI::course_edit_checkbox( $one, $course_id );
		?>

		<div class="options <?php echo $certificate['enabled'] ? '' : 'hidden'; ?>">
			<label for="meta_basic_certificate_layout"><?php _e( 'Inhalt des Zertifikats', 'brainpress' ); ?></label>
			<p class="description" style="float:left;"><?php echo $certificate['token_message']; ?></p>
			<?php echo BrainPress_Helper_Editor::get_wp_editor( 'basic-certificate-layout', 'meta_basic_certificate_layout', $certificate['content'] ); ?>

			<table class="wide">
				<tr>
					<td style="width:20%;"><label><?php _e( 'Hintergrundbild', 'brainpress' ); ?></label></td>
					<td><?php
						echo BrainPress_Helper_UI::browse_media_field(
							'meta_certificate_background',
							'meta_certificate_background',
							array(
								'placeholder' => __( 'Hintergrundbild auswählen', 'brainpress' ),
								'type' => 'image',
								'value' => $certificate['background'],
							)
						);
					?></td>
				</tr>
				<tr>
					<td style="width:20%;"><label><?php _e( 'Logo', 'brainpress' ); ?></label></td>
					<td><?php
						echo BrainPress_Helper_UI::browse_media_field(
							'meta_certificate_logo',
							'meta_certificate_logo',
							array(
								'placeholder' => __( 'Wähle ein Logo', 'brainpress' ),
								'type' => 'image',
								'value' => $certificate['logo'],
							)
						);
						?></td>
				</tr>
				<tr>
					<td><label><?php _e( 'Logo Position', 'brainpress' ); ?></label></td>
					<td>
						<?php _e( 'X', 'brainpress' ); ?>:
						<input type="number" class="small-text" name="meta_logo_position[x]" value="<?php echo esc_attr( $certificate['logo_position']['x'] ); ?>" />
						<?php _e( 'Y', 'brainpress' ); ?>:
						<input type="number" class="small-text" name="meta_logo_position[y]" value="<?php echo esc_attr( $certificate['logo_position']['y'] ); ?>" />
						<?php _e( 'Breite', 'brainpress' ); ?>:
						<input type="number" class="small-text" name="meta_logo_position[width]" value="<?php echo esc_attr( $certificate['logo_position']['width'] ); ?>" />
					</td>
				</tr>
				<tr>
					<td><label><?php _e( 'Inhaltsrand', 'brainpress' ); ?></label></td>
					<td>
						<?php _e( 'Oben', 'brainpress' ); ?>:
						<input type="number" class="small-text" name="meta_cert_margin[top]" value="<?php echo esc_attr( $certificate['margin']['top'] ); ?>" />
						<?php _e( 'Links', 'brainpress' ); ?>:
						<input type="number" class="small-text" name="meta_cert_margin[left]" value="<?php echo esc_attr( $certificate['margin']['left'] ); ?>" />
						<?php _e( 'Rechts', 'brainpress' ); ?>:
						<input type="number" class="small-text" name="meta_cert_margin[right]" value="<?php echo esc_attr( $certificate['margin']['right'] ); ?>" />
					</td>
				</tr>
				<tr>
					<td><label><?php _e( 'Seitenausrichtung', 'brainpress' ); ?></label></td>
					<td>
						<label style="float:left;margin-right:25px;">
							<input type="radio" name="meta_page_orientation" value="L" <?php checked( 'L', $certificate['orientation'] ); ?> /> <?php _e( 'Landscape', 'brainpress' ); ?>
						</label>
						<label style="float:left;">
							<input type="radio" name="meta_page_orientation" value="P" <?php checked( 'P', $certificate['orientation'] ); ?>/> <?php _e( 'Portrait', 'brainpress' ); ?>
						</label>
					</td>
				</tr>
				<tr>
					<td><label><?php _e( 'Textfarbe', 'brainpress' ); ?></label></td>
					<td>
						<label for="meta_cert_text_color">
						</label>
						<input
							type="text"
							name="meta_cert_text_color"
							id="meta_cert_text_color"
							class="certificate-color-picker"
							value="<?php echo esc_attr($certificate['text_color']); ?>"/>
					</td>
					<?php
						wp_enqueue_script( 'wp-color-picker' );
						wp_enqueue_style( 'wp-color-picker' );
					?>
				</tr>
			</table>
		</div>
	</div>

	<?php
	/**
	 * Trigger to add additional fields in step 6.
	 **/
	echo apply_filters( 'brainpress_course_setup_step_7', '', $course_id );

	// Show button
	echo BrainPress_View_Admin_Course_Edit::get_buttons( $course_id, 7, array( 'next' => false ) );
	?>
</div>
