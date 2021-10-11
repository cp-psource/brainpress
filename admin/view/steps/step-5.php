<?php
/**
 * Course Edit Step - 5
 **/
?>
<div class="step-title step-5">
	<?php _e( 'Sschritt 5 &ndash; Klassen, Diskussion & Arbeitsmappe', 'brainpress' ); ?>
	<div class="status <?php echo $setup_class; ?>"></div>
</div>

<div class="cp-box-content step-content step-5">
	<input type="hidden" name="meta_setup_step_5" value="saved" />

	<div class="wide class-size">
		<label><?php _e( 'Klassengröße', 'brainpress' ); ?></label>
		<p class="description"><?php _e( 'Verwende diese Einstellung, um ein Limit für alle Klassen festzulegen. Deaktiviere das Kontrollkästchen für unbegrenzte Klassengröße(n)..', 'brainpress' ); ?></p>
		<label class="narrow col">
			<input type="checkbox" name="meta_class_limited" <?php checked( true, $class_limited ); ?> />
			<span><?php _e( 'Begrenze die Klassengröße', 'brainpress' ); ?></span>
		</label>

		<label class="num-students narrow col <?php echo ( $class_limited ? '' : 'disabled' ); ?>">
			<?php _e( 'Anzahl der Studenten', 'brainpress' ); ?>
			<input type="text" class="spinners" name="meta_class_size" value="<?php echo $class_size; ?>" <?php echo ( $class_limited ? '' : 'disabled="disabled"' ); ?> />
		</label>
	</div>

	<?php
	$checkboxes = array(
		array(
			'meta_key' => 'allow_discussion',
			'title' => __( 'Kursdiskussion', 'brainpress' ),
			'description' => __( 'Wenn diese Option aktiviert ist, können die Studenten Fragen stellen und Antworten auf Kursebene erhalten. Ein Menüpunkt "Diskussion" wird hinzugefügt, damit der Student ALLE Diskussionen sehen kann, die von allen Klassenmitgliedern und Kursleitern stattfinden.', 'brainpress' ),
			'label' => __( 'Kursdiskussion zulassen', 'brainpress' ),
			'default' => false,
		),
		array(
			'meta_key' => 'allow_workbook',
			'title' => __( 'Studentenarbeitsmappe', 'brainpress' ),
			'description' => __( 'Wenn diese Option aktiviert ist, können die Studenten ihre Fortschritte und Bewertungen sehen.', 'brainpress' ),
			'label' => __( 'Studentenarbeitsmappe anzeigen', 'brainpress' ),
			'default' => false,
		),
		array(
			'meta_key' => 'allow_grades',
			'title' => __( 'Bewertungen der Studenten', 'brainpress' ),
			'description' => __( 'Wenn diese Option aktiviert ist, können die Studenten ihre Bewertungen sehen.', 'brainpress' ),
			'label' => __( 'Zeige Bewertungen der Studenten an', 'brainpress' ),
			'default' => false,
		),
	);

	foreach ( $checkboxes as $one ) {
		echo BrainPress_Helper_UI::course_edit_checkbox( $one, $course_id );
	}

	/**
	 * Trigger after printing fields at step 5.
	 *
	 * The dynamic portion of this hook is to allow additional course meta fields.
	 **/
	echo apply_filters( 'brainpress_course_setup_step_5', '', $course_id );

	/**
	 * Print button **/
	echo BrainPress_View_Admin_Course_Edit::get_buttons( $course_id, 5 );
	?>
</div>