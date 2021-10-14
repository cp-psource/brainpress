<?php

class BrainPress_View_Admin_Course_Student {

	public static function render() {
		/**
		 * Studenten Liste
		 */
		$course_id = (int) $_GET['id'];
		$list_course = new BrainPress_Helper_Table_CourseStudent();

		$list_course->set_course( $course_id );
		$list_course->set_add_new( true );
		$list_course->prepare_items();

		$content = '';
		ob_start();
		$list_course->search_box( __( 'Suche', 'brainpress' ), 'student' );
		$list_course->views();
		$list_course->display();
		$content .= ob_get_clean();

		/**
		 * Student einladen
		 */
		if ( BrainPress_Data_Capabilities::can_invite_students( $course_id ) ) {
			// Listen der zuvor eingeladenen Studenten anzeigen
			$invited_students = BrainPress_Data_Course::get_setting( $list_course->get_course_id(), 'invited_students', array() );
			$invited_students = array_filter( (array) $invited_students );
			$student_invite_nonce = wp_create_nonce( 'brainpress_remove_invite' );

			if ( ! empty( $invited_students ) ) {
				$content .= '<div class="brainpress_course_invite_student_wrapper invited-students">';
				$content .= '<h2>'. esc_html__( 'Eingeladene Studenten', 'brainpress' ) . '</h2>';
				$content .= '<p class="description">' . esc_html__( 'Liste der eingeladenen Studenten.', 'brainpress' ) . '</p>';
				$content .= '<table class="wp-list-table widefat fixed striped">';
				$content .= '<thead><tr><th>' . __( 'Vorname', 'brainpress' ) . '</th>';
				$content .= '<th>'. __( 'Nachname', 'brainpress' ) . '</th><th>' . __( 'E-Mail', 'brainpress' ) . '</th><th></th></tr></thead>';
				foreach ( $invited_students as $student_email => $student_data ) {
					$content .= '<tr class="invited-list">';
					$content .= '<td>' . $student_data['first_name'] . '</td>';
					$content .= '<td>'. $student_data['last_name'] . '</td>';
					$content .= '<td>'. $student_data['email'] . '</td>';
					$content .= '<td class="actions column-actions">';
					$content .= sprintf(
						'<a href="%s" title="%s" class="resend-invite" data-firstname="%s" data-lastname="%s" data-email="%s"><i class="fa fa-send"></i></a> ',
						'',
						esc_attr( __( 'Einladung erneut versenden', 'brainpress' ) ),
						esc_attr( $student_data['first_name'] ),
						esc_attr( $student_data['last_name'] ),
						esc_attr( $student_data['email'] )
					);
					$content .= sprintf(
						'<a href="%s" title="%s" data-email="%s" data-nonce="%s" class="remove-invite"><i class="fa fa-times-circle remove-btn"></i></a>',
						'',
						esc_attr( __( 'Einladung entfernen', 'brainpress' ) ),
						esc_attr( $student_email ),
						esc_attr( $student_invite_nonce )
					);
					$content .= '</td></tr>';
				}
				$content .= '</table>';
				$content .= '</div><br />';
			}

			$nonce = wp_create_nonce( 'invite_student' );
			$content .= '<hr /><div class="brainpress_course_invite_student_wrapper">';
			$content .= '<h2>' . esc_html__( 'Student einladen', 'brainpress' ) .'</h2>';
			$content .= '<label class="invite-firstname"><span>' . esc_html__( 'Vorname', 'brainpress' ) . '</span><input type="text" name="invite-firstname"></label>';
			$content .= '<label class="invite-lastname"><span>' . esc_html__( 'Nachname', 'brainpress' ) . '</span><input type="text" name="invite-lastname"></label>';
			$content .= '<label class="invite-email"><span>' . esc_html__( 'E-Mail', 'brainpress' ) . '</span><input type="text" name="invite-email"></label>';
			$content .= '<div class="invite-submit button button-primary" name="invite-submit" data-nonce="' . $nonce . '">' . esc_html__( 'Einladen', 'brainpress' ) . '</div>';
			$content .= '</div>';
		}

		$content .= '<hr /><div class="brainpress_course_email_enroled_students_wrapper">';
		$content .= '<h2>' . esc_html__( 'Sende eine E-Mail an eingeschriebene Studenten', 'brainpress' ) .'</h2>';
		if ( count( $list_course->items ) ) {
			$nonce = wp_create_nonce( 'send_email_to_enroled_students' );
			$content .= '<table class="form-table" id="send-email-to-enroled-students">';
			$content .= '<tbody>';
			/**
			 * status
			 */
			$content .= '<tr class="brainpress-email-sending hidden">';
			$content .= sprintf(
				'<th scope="row">%s</th>',
				__( 'Status', 'brainpress' )
			);
			$content .= sprintf(
				'<td><i class="fa fa-spinner fa-spin invite-progress"></i> %s</td>',
				__( 'Schicke Nachricht.', 'brainpress' )
			);
			$content .= '</tr>';

			/**
			 * Send to
			 **/
			$content .= '<tr class="brainpress-email-field brainpress-email-field-to">';
			$content .= sprintf( '<th class="row">%s</th>', __( 'Senden an', 'brainpress' ) );
			$content .= '<td><select name="send_to" class="widefat">';
			$send_options = array(
				'all' => __( 'Alle Studenten', 'brainpress' ),
				'all_with_submission' => __( 'Alle Studierenden mit Einreichungen', 'brainpress' ),
			);
			$units = BrainPress_Data_Course::get_units( $course_id );

			if ( ! empty( $units ) ) {
				foreach ( $units as $unit ) {
					$send_options[ $unit->ID ] = __( sprintf( 'Alle Studenten mit Einreichungen von &rarr; %S', $unit->post_title ), 'brainpress' );
				}
			}

			foreach ( $send_options as $send_to => $send_label ) {
				$content .= sprintf( '<option value="%s">%s</option>', $send_to, $send_label );
			}
			$content .= '</select>';
			$content .= '</td></tr>';

			/**
			 * message subject
			 */
			$content .= '<tr class="brainpress-email-field brainpress-email-field-subject">';
			$content .= '<th scope="row"><label class="brainpress-email-subject"><span>' . esc_html__( 'E-Mail Betreff', 'brainpress' ) . '</span></label></th>';
			$content .= sprintf(
				'<td><input type="text" name="email-subject" class="large-text" id="brainpress-email-subject" /></td>',
				__( 'E-Mail-Betreff eingeben', 'brainpress' )
			);
			$content .= '</tr>';

			/**
			 * message body
			 */
			$content .= '<tr class="brainpress-email-field brainpress-email-field-body">';
			$content .= '<th scope="row"><label class="brainpress-email-body"><span>' . esc_html__( 'Nachrichtentext', 'brainpress' ) . '</span></label></th>';
			$content .= '<td>';
			$content .= sprintf(
				'<p class="description">%s</p>',
				__( 'Diese Codes werden durch tatsächliche Daten ersetzt: STUDENT_FIRST_NAME, STUDENT_LAST_NAME, STUDENT_LOGIN, BLOG_NAME, WEBSITE_NAME, WEBSITE_ADDRESS, LOGIN_ADDRESS, COURSE_ADDRESS, COURSE_EXCERPT, COURSE_NAME, COURSE_OVERVIEW, COURSES_ADDRESS', 'brainpress' )
			);
			$editor_id = 'email-body';
			$args = array(
				'media_buttons' => false,
				'tinymce' => false,
			);
			ob_start();
			wp_editor( '', $editor_id, $args );
			$content .= ob_get_clean();
			$content .= '</td>';
			$content .= '</tr>';
			$content .= '</tbody>';
			$content .= '</table>';
			$content .= sprintf(
				'<div class="send-submit button button-primary" name="send-submit" data-nonce="%s">%s</div>',
				esc_attr( $nonce ),
				esc_html__( 'Senden', 'brainpress' )
			);

		} else {
			$content .= sprintf(
				'<p>%s</p>',
				__( 'Du kannst keine E-Mail senden, es gibt keine eingeschriebenen Studenten in diesem Kurs.', 'brainpress' )
			);
		}
		$content .= '</div>';

		return $content;
	}

	/**
	 * The action only fires if the current user is editing their own profile.
	 *
	 * @since 2.0.0
	 *
	 * @param WP_User $profileuser The current WP_User object.
	 */
	public static function render_student_courses( $student ) {
		if ( ! isset( $_GET['courses'] ) || 'show' != $_GET['courses'] ) {
			return;
		}
		printf( '<h2 id="courses">%s</h2>', __( 'Kurse', 'brainpress' ) );
		$enrolled_courses = BrainPress_Data_Student::get_enrolled_courses_ids( $student->ID );
		if ( empty( $enrolled_courses ) ) {
			echo wpautop( __( 'Keine eingeschriebenen Kurse.', 'brainpress' ) );
			return;
		}
		echo '<table class="wp-list-table widefat fixed striped">';
?>
<thead>
	<tr>
		<th scope="col" class="column-slug"><span><?php _e( 'Titel', 'brainpress' ); ?></span></th>
		<th scope="col"><span><?php _e( 'Excerpt', 'brainpress' ); ?></span></th>
		<th scope="col" class="column-rating"><span class="dashicons dashicons-calendar-alt"></span> <span><?php _e( 'Eingeschrieben', 'brainpress' ); ?></span></th>
		<th scope="col" class="column-rating"><span class="dashicons dashicons-calendar-alt"></span> <span><?php _e( 'Start', 'brainpress' ); ?></span></th>
		<th scope="col" class="column-rating"><span class="dashicons dashicons-calendar-alt"></span> <span><?php _e( 'Ende', 'brainpress' ); ?></span></th>
		<th scope="col" class="column-rating"><span class="dashicons dashicons-clock"></span> <span><?php _e( 'Dauer', 'brainpress' ); ?></span></th>
	</tr>
</thead>
<?php
$date_format = get_option( 'date_format' );
foreach ( $enrolled_courses as $course_id ) {
	$course = get_post( $course_id );
	if ( empty( $course ) ) {
		continue;
	}
	$val_open_ended = BrainPress_Data_Course::get_setting( $course_id, 'course_open_ended' );
	$is_open_end_course = ('on' == $val_open_ended);
	?>
	<tr class="student-course">
	<td class="title">
	<strong class="edit"><a href="<?php echo admin_url( 'admin.php?page=course_details&course_id=' . $course_id ); ?>"><?php echo $course->post_title; ?></a></strong>
	<div class="row-actions">
		<span class="edit"><a href="<?php echo  admin_url( 'admin.php?page=course_details&course_id=' . $course_id ); ?>" target="_blank"><?php _e( 'Bearbeiten', 'brainpress' ); ?></a> | </span>
		<span class="view"><a href="<?php echo get_permalink( $course_id ); ?>" target="_blank"><?php _e( 'Ansehen', 'brainpress' ); ?></a></span>
	</td>
	<td><?php echo $course->post_excerpt; ?></td>
	<td><?php echo date_i18n( $date_format, strtotime( get_user_meta( $student->id, sprintf( 'enrolled_course_date_%d', $course_id ), true ) ) ); ?></td>
	<td><?php echo date_i18n( $date_format, strtotime( BrainPress_Data_Course::get_setting( $course_id, 'course_start_date', true ) ) ); ?></td>
	<td><?php echo $is_open_end_course? __( 'Offenes Ende', 'brainpress' ) : date_i18n( $date_format, strtotime( BrainPress_Data_Course::get_setting( $course_id, 'course_end_date', true ) ) ); ?></td>
	<td><?php
	if ( $is_open_end_course ) {
		_e( '&infin; Tage', 'brainpress' );
	} else {
		$start = strtotime( BrainPress_Data_Course::get_setting( $course_id, 'course_start_date', true ) );
		$end = strtotime( BrainPress_Data_Course::get_setting( $course_id, 'course_end_date', true ) );
		$diff = abs( $end - $start );
		$days = $diff / DAY_IN_SECONDS;
		$days = intval( $days );
		printf( _n( '%s Tag', '%s Tage', $days, 'brainpress' ), $days );
	}
	?></td>
	</tr>
	<?php
}
		echo '</table>';
	}
}
