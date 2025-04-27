<?php

class BrainPress_View_Admin_Student_Workbook {

	public static function profile() {
		$student_id = (int) $_GET['student_id'];
		$student = get_userdata( $student_id );
?>
            <table cellspacing="0">
                <tr>
                    <td width="5%" valign="top">
                        <?php echo get_avatar( $student_id, 80 ); ?>
                    </td>
                    <td valign="top">
                        <div>
                            <span class="info_caption"><?php esc_html_e( 'Studenten ID', 'brainpress' ); ?>:</span>
                            <span class="info"><?php echo $student_id; ?></span>
                        </div>
                        <div>
                            <span class="info_caption"><?php esc_html_e( 'Vorname', 'brainpress' ); ?>:</span>
                            <span class="info"><?php echo $student->first_name; ?></span>
                        </div>
                        <div>
                            <span class="info_caption"><?php esc_html_e( 'Nachname', 'brainpress' ); ?>:</span>
                            <span class="info"><?php echo $student->last_name; ?></span>
                        </div>
                    </td>
                    <td valign="top">
                        <div>
                            <span class="info_caption"><?php esc_html_e( 'Kurse', 'brainpress' ); ?>:</span>
                            <span class="info">
<?php
		$courses = BrainPress_Data_Student::get_enrolled_courses_ids( $student_id );
		echo count( $courses );
?>
                        </span>
                        </div>
                        <?php if ( BrainPress_Data_Capabilities::can_create_student() ) : ?>
                        <div>
                            <span class="info_caption"><?php esc_html_e( 'Bearbeiten', 'brainpress' ); ?></span>
                            <span class="info">
<?php
		$edit_link = get_edit_user_link( $student_id );
		printf( '<a href="%s"><i class="fa fa-pencil"></i></a>', $edit_link );
?>
                            </span>
                        </div>
                        <?php endif; ?>
                        <div>
                            <span class="info_caption"><?php esc_html_e( 'Profil', 'brainpress' ); ?></span>
                            <span class="info">
<?php
		$profile_link = add_query_arg(
			array( 'view' => 'profile', 'student_id' => $student_id )
		);
		printf( '<a href="%s"><i class="fa fa-user"></i></a>', $profile_link );
?>
                            </span>
                        </div>
                    </td>
                </tr>
            </table>
            <hr />
<?php
	}

	public static function display() {
		$student_id = (int) $_GET['student_id'];
		$student = get_userdata( $student_id );
?>
        <div class="wrap nocoursesub assessment student-workbook cp-wrap">
            <h2><?php esc_html_e( 'Studenten Arbeitsmappe', 'brainpress' ); ?></h2>
            <hr />
<?php
		self::profile();

		$courses = BrainPress_Data_Instructor::get_accessable_courses( wp_get_current_user(), true );
		$first = array_shift( $courses );
		$selected_course = ! empty( $_GET['course_id'] ) ? (int) $_GET['course_id'] : $first->ID;
		$student_progress = BrainPress_Data_Student::get_completion_data( $student_id, $selected_course );

if ( 0 == count( $courses ) ) :
?>
		<div class="zero-courses">
			<?php echo $student->user_login; esc_html_e( ' habe mich noch nicht für Kurse eingeschrieben.', 'brainpress' ); ?>
		</div>
	<?php else : ?>
                <div class="tablenav">
                    <span class="info_caption"><?php esc_html_e( 'Kurs wählen', 'brainpress' ); ?></span>
<?php
			echo BrainPress_Helper_UI::get_course_dropdown(
				'course-list',
				'course-list',
				$courses,
				array(
					'class' => 'medium',
					'value' => $selected_course,
				)
			);
?>
                </div>
<?php
		$units = BrainPress_Data_Course::get_units_with_modules( $selected_course, array( 'publish', 'draft' ) );
		$active_unit_id = ! empty( $_GET['unit_id'] ) ? (int) $_GET['unit_id'] : null;
		$active_unit = null;
		$excluded_modules = array( 'input-textarea', 'input-text' );

if ( ! empty( $units ) ) :
?>
		<div class="units-tab-container">
			<span class="info_caption"><?php esc_html_e( 'Einheit wählen', 'brainpress' ); ?></span>
			<div class="units-tab">
<?php
	$tab = 1;
foreach ( $units as $unit_id => $unit ) :
	$unit_url = add_query_arg(
		array(
			'course_id' => $selected_course,
			'unit_id' => $unit_id,
		)
	);

	if ( ! $active_unit_id ) :
		if ( 1 == $tab ) :
			$active_unit = $unit;
			$active_unit_id = $unit_id;
	endif;
elseif ( $active_unit_id == $unit_id ) :
	$active_unit = $unit;
endif;
?>
					<a href="<?php echo $unit_url; ?>" class="unit-tab"><?php echo $tab; ?></a>
<?php
$tab++;
endforeach;
?>
			</div>
		</div>
		<hr />
<?php
endif;
?>
                <h3><?php echo $active_unit['unit']->post_title; ?></h3>
                <table class="widefat" id="modules-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th><?php esc_html_e( 'Aktivität', 'brainpress' ); ?></th>
                            <th><?php esc_html_e( 'Einreichung', 'brainpress' ); ?></th>
                            <th><?php esc_html_e( 'Antwort', 'brainpress' ); ?></th>
                            <th><?php esc_html_e( 'Bewertung', 'brainpress' ); ?></th>
                            <th><?php esc_html_e( 'Feedback', 'brainpress' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
<?php
$pages = $active_unit['pages'];
if ( $pages ) {
	foreach ( $pages as $page_number => $page ) {
?>
                                <tr>
                                    <th colspan="6">
                                        <?php echo $page['title']; ?>
                                    </th>
                                </tr>
<?php
		$modules = $page['modules'];

foreach ( $modules as $module_id => $module ) {

	$attributes = BrainPress_Data_Module::attributes( $module_id );
	$module_type = $attributes['module_type'];

	$response = BrainPress_Data_Student::get_response(
		$student_id,
		$selected_course,
		$active_unit_id,
		$module_id,
		false,
		$student_progress
	);
	/**
			 * Grade
			 */
	$grade = BrainPress_Data_Student::get_grade(
		$student_id,
		$selected_course,
		$active_unit_id,
		$module_id,
		false,
		false,
		$student_progress
	);
	$require_instructor_assessment = ! empty( $attributes['instructor_assessable'] ) && cp_is_true( $attributes['instructor_assessable'] );
	if ( $require_instructor_assessment || in_array( $module_type, $excluded_modules ) ) {
		$graded_by = BrainPress_Helper_Utility::get_array_val( $grade, 'graded_by' );
		if ( 'auto' === $graded_by ) {
			// Set 0 as grade if it is auto-graded
			$grade = 0;
		}
	}
	$feedback = BrainPress_Data_Student::get_feedback(
		$student_id,
		$selected_course,
		$active_unit_id,
		$module_id,
		false,
		false,
		$student_progress
	);
	$view_link = '';

	if ( $response ) {
		$view_link = add_query_arg(
			array(
				'page' => 'brainpress_assessments',
				'course_id' => $selected_course,
				'unit_id' => $active_unit_id,
				'module_id' => $module_id,
				'student_id' => $student_id,
			),
			admin_url( 'admin.php' )
		);
		$view_link = sprintf( '<a href="%s&view_answer">%s</a>', $view_link, __( 'Ansehen', 'brainpress' ) );
	}
?>
								<tr>
									<td width="1%"></td>
									<td><?php echo $module->post_title; ?></td>
									<td>
<?php
if ( $response && ! empty( $response['date'] ) ) {
	$date_format = get_option( 'date_format' );
	echo date_i18n( $date_format, strtotime( $response['date'] ) );
}
?>
									</td>
									<td><?php echo $view_link; ?></td>
									<td>
<?php
	echo (-1 == $grade['grade'] ? __( '--', 'brainpress' ) : sprintf( '%d%%', $grade['grade'] ) );
?>
									</td>
									<td id="instructor-feedback">
<?php
	$first_last = BrainPress_Helper_Utility::get_user_name( (int) $feedback['feedback_by'] );
	echo ! empty( $feedback['feedback'] ) ? '<div class="feedback"><div class="comment">' . $feedback['feedback'] . '</div><div class="instructor"> – <em>' . esc_html( $first_last ) . '</em></div></div>' : '';
?>
									</td>
								</tr>
<?php
}
	}
}
?>
                    </tbody>
                </table>
<?php
endif;
?>
        </div><!-- end .wrap -->
<?php
	}
}
