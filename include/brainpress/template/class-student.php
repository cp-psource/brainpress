<?php

class BrainPress_Template_Student {
	public static function process_enrollment() {
		if ( ! empty( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'enrollment_process' ) ) {
			$request = $_REQUEST;

			if ( empty( $request['student_id'] ) ) {
				// Assume the call is at front page
				$student_id = get_current_user_id();
			} else {
				$student_id = (int) $request['student_id'];
			}
			$course_id = (int) $request['course_id'];
			$json_data = array();

			if ( BrainPress_Data_Course::enroll_student( $student_id, $course_id ) ) {
				if ( empty( $request['cpnonce'] ) ) {
					$course_url = BrainPress_Data_Course::get_course_url( $course_id );
					$course_url .= BrainPress_Core::get_slug( 'units/' );
					// Send the student the unit overview
					wp_safe_redirect( $course_url );
				} else {
					$json_data['success'] = true;
					$json_data['course_id'] = $course_id;
					$json_data['student_id'] = $student_id;

					wp_send_json_success( $json_data ); exit;
				}
			} else {
				if ( empty( $request['cpnonce'] ) ) {
					if ( ! empty( $request['_wp_http_referer'] ) ) {
						$url = $request['_wp_http_referer'];
					} else {
						$url = remove_query_arg( 'dummy' );
					}
					wp_safe_redirect( $url ); exit;

				} else {
					$json_data['error_message'] = __( 'Einschreibung fehlgeschlagen', 'brainpress' );

					wp_send_json_error( $json_data );
				}
			}
		}
	}

	public static function dashboard() {
		if ( is_user_logged_in() ) {

			$student_id = get_current_user_id();
			$student_courses = BrainPress_Data_Student::get_enrolled_courses_ids( $student_id );

			$content = '
				<div class="student-dashboard-wrapper">';

			// Instructor Course List
			$show = 'dates,class_size';
			$course_list = do_shortcode( '[course_list instructor="' . get_current_user_id() . '" instructor_msg="" status="all" title_tag="h1" title_class="h1-title" list_wrapper_before="" show_divider="yes"  left_class="enroll-box-left" right_class="enroll-box-right" course_class="enroll-box" title_link="no" show="' . $show . '" show_title="no" admin_links="true" show_button="no" show_media="no"]' );

			if ( ! empty( $course_list ) ) {
				$content .= '
					<div class="dashboard-managed-courses-list">
						<h1 class="title managed-courses-title">' . esc_html__( 'Kurse die Du verwaltest:', 'brainpress' ) . '</h1>
						<div class="course-list course-list-managed course course-student-dashboard">' .
						$course_list . '
						</div>
					</div>
					<div class="clearfix"></div>
				';
			}

			$course_list = do_shortcode( '[course_list student="' . $student_id . '" student_msg="" course_status="incomplete" list_wrapper_before="" class="course course-student-dashboard" left_class="enroll-box-left" right_class="enroll-box-right" course_class="enroll-box" title_class="h1-title" title_link="no" show_media="no"]' );

			// Add some random courses.
			$show_random_courses = true;
			if ( empty( $course_list ) && $show_random_courses ) {
				// Random Courses
				$content .= '
					<div class="dashboard-random-courses-list">
						<h3 class="title suggested-courses">' . __( 'Du bist in keinem Kurs eingeschrieben.', 'brainpress' ) . '</h3>' .
						esc_html__( 'Hier sind einige, die Dir den Einstieg erleichtern sollen:', 'brainpress' ) . '
						<hr />
						<div class="dashboard-random-courses">' . do_shortcode( '[course_random number="3" featured_title="" media_type="image"]' ) . '</div>
					</div>
				';
			} else {
				// Course List
				$content .= '
					<div class="dashboard-current-courses-list">
						<h1 class="title enrolled-courses-title current-courses-title">' . __( 'Deine aktuellen Kurse:', 'brainpress' ) . '</h1>
						<div class="course-list course-list-current course course-student-dashboard">' .
						$course_list . '
						</div>
					</div>
					<div class="clearfix"></div>
				';
			}

			// Completed courses
			$course_list = do_shortcode( '[course_list student="' . $student_id . '" student_msg="" course_status="completed" list_wrapper_before="" title_link="no" title_tag="h1" title_class="h1-title" show_divider="yes" left_class="enroll-box-left" right_class="enroll-box-right"]' );

			if ( ! empty( $course_list ) ) {
				// Course List
				$content .= '
					<div class="dashboard-completed-courses-list">
						<h1 class="title completed-courses-title">' . __( 'Abgeschlossene Kurse:', 'brainpress' ) . '</h1>
						<div class="course-list course-list-completed course course-student-dashboard">' .
						$course_list . '
						</div>
					</div>
					<div class="clearfix"></div>
				';
			}

			$content .= '
				</div>
			';
			return $content;

		} else {
			$signup_redirect = apply_filters(
				'brainpress_signup_redirect_for_guest',
				! CP_IS_CAMPUS
			);

			if ( $signup_redirect ) {
				if ( BrainPress_Core::get_setting( 'general/use_custom_login' ) ) {
					$url = BrainPress_Core::get_slug( 'signup', true );
				} else {
					$url = wp_login_url();
				}

				wp_redirect( $url );
				exit;
			}
		}
	}

	public static function student_settings() {

		if ( is_user_logged_in() ) {

			$student_id = get_current_user_id();

			$form_message_class = '';
			$form_message = '';

			if ( isset( $_POST['student-settings-submit'] ) ) {

				if ( ! isset( $_POST['student_settings_nonce'] ) || ! wp_verify_nonce( $_POST['student_settings_nonce'], 'student_settings_save' )
				) {
					_e( "Änderungen können nicht gespeichert werden, da Nonce nicht überprüft hat.", 'brainpress' );
				} else {
					$student_data = array();
					$student_data['ID'] = get_current_user_id();
					$form_errors = 0;

					do_action( 'brainpress_before_settings_validation' );

					if ( $_POST['password'] ) {
						if ( $_POST['password'] == $_POST['password_confirmation'] ) {
							$student_data['user_pass'] = $_POST['password'];
						} else {
							$form_message = __( "Passwörter stimmen nicht überein", 'brainpress' );
							$form_message_class = 'red';
							$form_errors ++;
						}
					}

					$student_data['user_email'] = $_POST['email'];
					$student_data['first_name'] = $_POST['first_name'];
					$student_data['last_name'] = $_POST['last_name'];

					if ( ! is_email( $_POST['email'] ) ) {
						$form_message = __( 'Email Adresse ist nicht gültig.', 'brainpress' );
						$form_message_class = 'red';
						$form_errors ++;
					}

					if ( ! $form_errors ) {
						if ( BrainPress_Data_Student::update_student_data( $student_id, $student_data ) ) {
							$form_message = __( 'Das Profil wurde erfolgreich aktualisiert.', 'brainpress' );
							$form_message_class = 'regular';
						} else {
							$form_message = __( 'Beim Aktualisieren ist ein Fehler aufgetreten. Bitte überprüfe das Formular und versuche es erneut.', 'brainpress' );
							$form_message_class = 'red';
						}
					}
				}
			}

			$content = '
			<p class="'. esc_attr( 'form-info-' . $form_message_class ) .'">' . esc_html( $form_message ) . '</p>
			';

			do_action( 'brainpress_before_settings_form' );

			$student = get_userdata( $student_id );

			$content .= '
			<form id="student-settings" name="student-settings" method="post" class="student-settings">' .
				wp_nonce_field( 'student_settings_save', 'student_settings_nonce', true, false ) . '
				<label>
					' . esc_html__( 'Vorname', 'brainpress' ) . ':
					<input type="text" name="first_name" value="' . esc_attr__( $student->user_firstname ) . '"/>
				</label>
				' . do_action( 'brainpress_after_settings_first_name' ) . '
				<label>
					' . esc_html__( 'Nachname', 'brainpress' ) . ':
					<input type="text" name="last_name" value="' . esc_attr__( $student->user_lastname ) . '"/>
				</label>
				' . do_action( 'brainpress_after_settings_last_name' ) . '
				<label>
					' . esc_html__( 'E-Mail', 'brainpress' ) . ':
					<input type="text" name="email" value="' . esc_attr__( $student->user_email ) . '"/>
				</label>
				' . do_action( 'brainpress_after_settings_email' ) . '
				<label>
					' . esc_html__( 'Benutzername', 'brainpress' ) . ':
					<input type="text" name="username" value="'. esc_attr__( $student->user_login ) .'" disabled="disabled"/>
				</label>
				' . do_action( 'brainpress_after_settings_username' ) . '
				<label>
					' . esc_html__( 'Passwort', 'brainpress' ) . ':
					<input type="password" name="password" value="" placeholder="' . esc_html__( "Wird sich nicht ändern, wenn leer.", 'brainpress' ) .'"/>
				</label>
				' . do_action( 'brainpress_after_settings_passwordon' ) . '
				<label>
					' . esc_html__( 'Bestätige das Passwort', 'brainpress' ) . ':
					<input type="password" name="password_confirmation" value=""/>
				</label>
				' . do_action( 'brainpress_after_settings_pasword' ) . '
				<label class="weak-password-label"><input type="checkbox" name="weak_password_confirm" value="1" /> ' . __( 'Bestätige die Verwendung eines schwachen Passworts.', 'brainpress' ) . '</label>
				<label class="full">
					<input type="submit" name="student-settings-submit" class="apply-button-enrolled" value="' . esc_html__( 'Änderungen speichern', 'brainpress' ) .'"/>
				</label>
			</form>
			';

			do_action( 'brainpress_after_settings_form' );

			return $content;
		} else {
			$signup_redirect = apply_filters(
				'brainpress_signup_redirect_for_guest',
				! CP_IS_CAMPUS
			);

			if ( $signup_redirect ) {
				if ( BrainPress_Core::get_setting( 'general/use_custom_login' ) ) {
					$url = BrainPress_Core::get_slug( 'signup', true );
				} else {
					$url = wp_login_url();
				}

				wp_redirect( $url );
				exit;
			}
		}

	}

	public static function registration_form() {

		$redirect_url = '';
		if ( ! empty( $_REQUEST['redirect_url'] ) ) {
			$redirect_url = $_REQUEST['redirect_url'];
		}
		return do_shortcode( '[course_signup page="signup" signup_title="" redirect_url="' . $redirect_url . '" login_url="' . BrainPress_Core::get_slug( 'login', true ) . '"]' );

	}
}
