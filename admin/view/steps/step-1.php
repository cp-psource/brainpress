<?php
/**
 * Course Edit - Step 1
 **/
?>
<div class="step-title step-1">
	<?php _e( 'Schritt 1 &ndash; Kursübersicht', 'brainpress' ); ?>
	<div class="status <?php echo $setup_class; ?>"></div>
</div>

<div class="cp-box-content step-content step-1">
	<input type="hidden" name="meta_setup_step_1" value="saved" />
	<input type="hidden" name="course_id" value="<?php echo $course_id; ?>" />

	<div class="wide">
		<label for="course_name" class="required first"><?php _e( 'Kurstitel', 'brainpress' ); ?></label>
		<input class="wide" type="text" name="course_name" id="course_name" value="<?php echo esc_attr( $course_name ); ?>" />
	</div>
	<?php
	/**
	 * Trigger after course title is printed.
	 **/
	echo apply_filters( 'brainpress_course_setup_step_1_after_title', '', $course_id );
	?>

	<div class="wide">
		<label for="courseExcerpt" class="required drop-line"><?php _e( 'Kurzbeschreibung', 'brainpress' ); ?></label>
		<?php echo BrainPress_Helper_Editor::get_wp_editor( 'courseExcerpt', 'course_excerpt', $editor_content, array( 'teeny' => true ) ); ?>
	</div>
	<?php
	/**
	 * Trigger after course summary
	 **/
	echo apply_filters( 'brainpress_course_setup_step_1_after_excerpt', '', $course_id );
	?>

	<?php
	// Feature Image
	echo BrainPress_Helper_UI::browse_media_field(
		'meta_listing_image',
		'meta_listing_image',
		array(
			'placeholder' => __( 'Füge eine Bild-URL hinzu oder suche nach einem Bild', 'brainpress' ),
			'title' => __( 'Ausgewähltes Bild', 'brainpress' ),
			'value' => BrainPress_Data_Course::get_listing_image( $course_id ),
		)
	);
	?>

	<div class="wide">
		<label for="meta_course_language"><?php _e( 'Sprache', 'brainpress' ); ?></label>
		<input class="medium" type="text" name="meta_course_language" id="meta_course_language" value="<?php echo esc_attr( $language ); ?>" />
	</div>
	<?php
	/**
	 * Trigger after printing step 1
	 **/
	echo apply_filters( 'brainpress_course_setup_step_1', '', $course_id );
	?>

	<?php
	// Print buttons
	echo BrainPress_View_Admin_Course_Edit::get_buttons( $course_id, 1, array( 'previous' => false ) );
	?>
</div>
