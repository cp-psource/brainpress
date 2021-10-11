<div class="step-title step-6">
	<?php printf( __( 'Schritt 6 &ndash; Einschreibung %s', 'brainpress' ), $title2 ); ?>
	<div class="status <?php echo $setup_class; ?>"></div>
</div>

<div class="cp-box-content step-content step-6">
	<input type="hidden" name="meta_setup_step_6" value="saved" />

	<div class="wide">
		<label><?php _e( 'Einschreibungsbeschränkungen', 'brainpress' ); ?></label>
		<p class="description"><?php _e( 'Wähle die Einschränkungen für den Zugriff auf und die Einschreibung zu diesem Kurs aus.', 'brainpress' ); ?></p>
		<?php echo BrainPress_Helper_UI::select( 'meta_enrollment_type', $enrollment_types, $enrollment_type, 'chosen-select medium' ); ?>
	</div>

	<div class="wide enrollment-type-options prerequisite<?php echo $prerequisite_class; ?>">
		<label><?php _e( 'Vorausgesetzte Kurse', 'brainpress' ); ?></label>
		<p class="description"><?php _e( 'Wähle die Kurse aus, die ein Student absolvieren muss, bevor er sich für diesen Kurs einschreiben kann', 'brainpress' ); ?></p>
		<select name="meta_enrollment_prerequisite" class="medium chosen-select chosen-select-course <?php echo $class_extra; ?>" multiple="true" data-placeholder=" ">

			<?php if ( ! empty( $courses ) ) : foreach ( $courses as $course ) : ?>
				<option value="<?php echo $course->ID; ?>" <?php selected( true, in_array( $course->ID, $saved_settings ) ); ?>><?php echo $course->post_title; ?></option>
			<?php endforeach; endif; ?>

		</select>
	</div>

	<div class="wide enrollment-type-options passcode <?php echo $passcode_class; ?>">
		<label><?php _e( 'Kurs Passcode', 'brainpress' ); ?></label>
		<p class="description"><?php _e( 'Gib den Passcode ein, der für den Zugriff auf diesen Kurs erforderlich ist', 'brainpress' ); ?></p>
		<input type="text" name="meta_enrollment_passcode" value="<?php echo esc_attr( $enrollment_passcode ); ?>" />
	</div>

	<?php if ( false === $disable_payment ) :
		$one = array(
				'meta_key' => 'payment_paid_course',
				'title' => __( 'Kurszahlung', 'brainpress' ),
				'description' => __( 'Zahlungsmöglichkeiten für Deinen Kurs. Zusätzliche Plugins sind erforderlich und die Einstellungen variieren je nach Plugin.', 'brainpress' ),
				'label' => __( 'Dies ist ein kostenpflichtiger Kurs', 'brainpress' ),
				'default' => false,
			);
		echo '<hr class="separator" />';
		echo BrainPress_Helper_UI::course_edit_checkbox( $one, $course_id );
	endif;
	?>

	<?php
	// Show install|payment messages when applicable
	if ( false === $payment_supported && false === $disable_payment ) :
		echo $payment_message;
	endif;
	?>
	<div class="is_paid_toggle <?php echo $payment_paid_course ? '' : 'hidden'; ?>">
		<?php
		/**
		 * Add additional fields if 'This is a paid course' is selected.
		 *
		 * Field names must begin with meta_ to allow it to be automatically added to the course settings
		 *
		 * * This is the ideal filter to use for integrating payment plugins
		 */
		echo apply_filters( 'brainpress_course_setup_step_6_paid', '', $course_id );
		?>
	</div>

	<?php
	/**
	 * Trigger to add additional fields in step 6.
	 **/
	echo apply_filters( 'brainpress_course_setup_step_6', '', $course_id );

	// Show buttons
	echo BrainPress_View_Admin_Course_Edit::get_buttons( $course_id, 6 );
	?>
</div>