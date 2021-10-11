<?php
/**
 * Course Edit Step - 4
 **/
?>
<div class="step-title step-4">
	<?php _e( 'Schritt 4 &ndash; Kurstermine', 'brainpress' ); ?>
	<div class="status <?php echo $setup_class; ?>"></div>
</div>

<div class="cp-box-content step-content step-4">
	<input type="hidden" name="meta_setup_step_4" value="saved" />

	<div class="wide course-dates">
		<label><?php _e( 'Kursverfügbarkeit', 'brainpress' ); ?></label>
		<p class="description"><?php _e( 'Dies sind die Daten, an denen der Kurs den Studenten zur Verfügung steht', 'brainpress' ); ?></p>
		<label class="checkbox medium">
			<input type="checkbox" name="meta_course_open_ended" <?php checked( true, $open_ended_course ); ?> />
			<span><?php _e( 'Dieser Kurs hat kein Enddatum', 'brainpress' ); ?></span>
		</label>
		<div class="date-range">
			<div class="start-date">
				<label for="meta_course_start_date" class="start-date-label required"><?php _e( 'Kursstart', 'brainpress' ); ?></label>

				<div class="date">
					<input type="text" class="dateinput timeinput" name="meta_course_start_date" value="<?php echo $course_start_date; ?>" /><i class="calendar"></i>
				</div>
			</div>
			<div class="end-date <?php echo ( $open_ended_course ? 'disabled' : '' ); ?>">
				<label for="meta_course_end_date" class="end-date-label required"><?php _e( 'Kursende', 'brainpress' ); ?></label>
				<div class="date">
					<input type="text" class="dateinput" name="meta_course_end_date" value="<?php echo $course_end_date; ?>" <?php echo ( $open_ended_course ? 'disabled="disabled"' : '' ); ?> />
				</div>
			</div>
		</div>
	</div>

	<div class="wide enrollment-dates">
		<label><?php _e( 'Einschreibungstermine', 'brainpress' ); ?></label>
		<p class="description"><?php _e( 'Dies sind die Daten, an denen sich Studenten für einen Kurs einschreiben können.', 'brainpress' ); ?></p>
		<label class="checkbox medium">
			<input type="checkbox" name="meta_enrollment_open_ended" <?php checked( true, $enrollment_open_ended ); ?> />
			<span><?php _e( 'Studenten können sich jederzeit einschreiben', 'brainpress' ); ?></span>
		</label>
		<div class="date-range enrollment">
			<div class="start-date <?php echo ( $enrollment_open_ended ? 'disabled' : '' ); ?>">
				<label for="meta_enrollment_start_date" class="start-date-label required"><?php _e( 'Einschreibungsstart', 'brainpress' ); ?></label>

				<div class="date">
					<input type="text" class="dateinput" name="meta_enrollment_start_date" value="<?php echo esc_attr( $enrollment_start_date ); ?>" /><i class="calendar"></i>
				</div>
			</div>
			<div class="end-date <?php echo ( $enrollment_open_ended ? 'disabled' : '' ); ?>">
				<label for="meta_enrollment_end_date" class="end-date-label required"><?php _e( 'Einschreibungsende', 'brainpress' ); ?></label>
				<div class="date">
					<input type="text" class="dateinput" name="meta_enrollment_end_date" value="<?php echo esc_attr( $enrollment_end_date ); ?>" <?php echo ( $enrollment_open_ended ? 'disabled="disabled"' : '' ); ?> />
				</div>
			</div>
		</div>
	</div>

	<?php
	/**
	 * Trigger after printing step 4 fields.
	 **/
	echo apply_filters( 'brainpress_course_setup_step_4', '', $course_id );

	// Print buttons
	echo BrainPress_View_Admin_Course_Edit::get_buttons( $course_id, 4 );
	?>
	<br />
</div>