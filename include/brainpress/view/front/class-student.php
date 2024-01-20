<?php

class BrainPress_View_Front_Student {

	public static function init() {
		add_action('brainpress_after_settings_username', array( 'BrainPress_Helper_UI', 'password_strength_meter'));
	}

	public static function render_enrollment_process_page() {

		if ( ! is_user_logged_in() ) {
			_e( 'Du musst angemeldet sein, um die Aktion abzuschließen', 'brainpress' );
			return;
		}

		if ( ! isset( $_POST['course_id'] ) || ! is_numeric( $_POST['course_id'] ) ) {
			_e( 'Bitte wähle zuerst einen Kurs aus, für den Du dich einschreiben möchtest.', 'brainpress' );
			return;
		}

		$course_price = 0;

		check_admin_referer( 'enrollment_process' );

		$course_id = (int) $_POST['course_id'];
		$student_id = get_current_user_ID();
		$course = new Course( $course_id );
		$pass_errors = 0;

		global $brainpress;

		$is_paid = get_post_meta( $course_id, 'paid_course', true ) == 'on' ? true : false;

		/** This filter is documented in * include/brainpress/helper/integration/class-woocommerce.php */
		$is_user_purchased_course = apply_filters( 'brainpress_is_user_purchased_course', false, $course, $student_id );

		if ( $is_paid && isset( $course->details->psecommerce_product ) && '' != $course->details->psecommerce_product && $brainpress->psecommerce_active ) {
			$course_price = 1; //forces user to purchase course / show purchase form
			$course->is_user_purchased_course( $course->details->psecommerce_product, $student_id );
		}

		if ( 'passcode' == $course->details->enroll_type ) {
			if ( $_POST['passcode'] != $course->details->passcode ) {
				$pass_errors ++;
			}
		}

		if ( ! BrainPress_Data_Student::is_enrolled_in_course( $student_id, $course_id ) ) {
			if ( 0 == $pass_errors ) {
				if ( 0 == $course_price ) {//Course is FREE
					//Enroll student in
					if ( BrainPress_Data_Course::enroll_student( $student_id, $course_id ) ) {
						printf( __( 'Herzlichen Glückwunsch, Du hast dich erfolgreich für den Kurs "%s" eingeschrieben! Überprüfe Dein %s für weitere Informationen.', 'brainpress' ), '<strong>' . $course->details->post_title . '</strong>', '<a href="' . $this->get_student_dashboard_slug( true ) . '">' . __( 'Dashboard', 'brainpress' ) . '</a>' );

					} else {
						_e( 'Während des Einschreibungsprozesses ist ein Fehler aufgetreten. Bitte versuche es später noch einmal.', 'brainpress' );
					}
				} else {
					if ( $course->is_user_purchased_course( $course->details->psecommerce_product, $student_id ) ) {
						//Enroll student in
						if ( BrainPress_Data_Course::enroll_student( $student_id, $course_id ) ) {
							printf( __( 'Herzlichen Glückwunsch, Du hast dich erfolgreich für den Kurs "%s" eingeschrieben! Überprüfe Dein %s für weitere Informationen.', 'brainpress' ), '<strong>' . $course->details->post_title . '</strong>', '<a href="' . $this->get_student_dashboard_slug( true ) . '">' . __( 'Dashboard', 'brainpress' ) . '</a>' );
						} else {
							_e( 'Während des Einschreibungsprozesses ist ein Fehler aufgetreten. Bitte versuche es später noch einmal.', 'brainpress' );
						}
					} else {
						$course->show_purchase_form( $course->details->psecommerce_product );
					}
				}
			} else {
				printf( __( 'Passcode ist ungültig. Bitte %s und versuche es erneut.', 'brainpress' ), '<a href="' . esc_url( $course->get_permalink() ) . '">' . __( 'geh zurück', 'brainpress' ) . '</a>' );

			}
		} else {
			$course_status = BrainPress_Data_Course::get_course_status( $course_id );
			$suffix = 'units';

			if ( 'future' === $course_status ) {
				$suffix = '';
			}

			wp_redirect( trailingslashit( $course->get_permalink() ) . $suffix );
			exit;
		}

	}

	public static function render_student_dashboard_page( $student_id = 0, $atts = array() ) {

		if ( ! is_user_logged_in() ) {
			_e( 'Du musst angemeldet sein, um die Aktion abzuschließen', 'brainpress' );
			exit;
		} else {
			if ( empty( $student_id ) ) {
				$student_id = get_current_user_id();
			}
		}

		$student_courses = BrainPress_Data_Student::get_enrolled_courses_ids( $student_id );
		?>
			<div class="student-dashboard-wrapper">
		<?php

		// Instructor Course List
		$show = 'dates,class_size';

		$course_list = do_shortcode( '[course_list instructor="' . $student_id . '" instructor_msg="" status="all" title_tag="h1" title_class="h1-title" list_wrapper_before="" show_divider="yes"  left_class="enroll-box-left" right_class="enroll-box-right" course_class="enroll-box" title_link="no" show="' . $show . '" show_title="no" admin_links="true" show_button="no" show_media="no"]' );

		$show_random_courses = true;

		if ( ! empty( $course_list )
			&& ( BrainPress_Data_Capabilities::is_instructor() || BrainPress_Data_Capabilities::is_facilitator() ) ) {
			echo '<div class="dashboard-managed-courses-list">';
			echo '<h1 class="title managed-courses-title">' . __( 'Kurse welche Du verwaltest:', 'brainpress' ) . '</h1>';
			echo '<div class="course-list course-list-managed course course-student-dashboard">';
			echo $course_list;
			echo '</div>';
			echo '</div>';
			echo '<div class="clearfix"></div>';
		}

		$shortcode_attributes = array(
			'student' => get_current_user_id(),
			'student_msg' => '',
			'status' => 'incomplete',
		);

		if ( ! empty( $atts['show_withdraw_link'] ) && 'yes' == $atts['show_withdraw_link'] ) {
			$shortcode_attributes['show_withdraw_link'] = 'yes';
		}

		$shortcode_attributes = apply_filters( 'course_list_page_student_dashsboard', $shortcode_attributes );
		$shortcode_attributes = BrainPress_Helper_Utility::convert_array_to_params( $shortcode_attributes );
		$course_list = do_shortcode( '[course_list '.$shortcode_attributes.']' );

		// Add some random courses.
		if ( empty( $course_list ) && $show_random_courses ) {

			//Random Courses
			echo '<div class="dashboard-random-courses-list">';
			echo '<h3 class="title suggested-courses">' . __( 'Du bist in keinem Kurs eingeschrieben.', 'brainpress' ) . '</h3>';
			_e( 'Hier sind einige, die Dir den Einstieg erleichtern sollen:', 'brainpress' );
			echo '<hr />';
			echo '<div class="dashboard-random-courses">' . do_shortcode( '[course_random number="3" featured_title="" media_type="image"]' ) . '</div>';
			echo '</div>';
		} else {
			// Course List
			echo '<div class="dashboard-current-courses-list">';
			echo '<h1 class="title enrolled-courses-title current-courses-title">' . __( 'Deine aktuellen Kurse:', 'brainpress' ) . '</h1>';
			echo '<div class="course-list course-list-current course course-student-dashboard">';
			echo $course_list;
			echo '</div>';
			echo '</div>';
			echo '<div class="clearfix"></div>';
		}

		// Completed courses
		$show = 'dates,class_size';

		$shortcode_attributes = array(
			'student' => get_current_user_id(),
			'student_msg' => '',
			'status' => 'completed',
		);
		/**
		 * Allow to change cshortcode attributes before fired.
		 *
		 * @since 2.0.4
		 */
		$shortcode_attributes = apply_filters( 'course_list_page_student_dashsboard', $shortcode_attributes );
		$shortcode_attributes = BrainPress_Helper_Utility::convert_array_to_params( $shortcode_attributes );
		$course_list = do_shortcode( '[course_list '.$shortcode_attributes.']' );
		if ( ! empty( $course_list ) ) {
			// Course List
			echo '<div class="dashboard-completed-courses-list">';
			echo '<h1 class="title completed-courses-title">' . __( 'Abgeschlossene Kurse:', 'brainpress' ) . '</h1>';
			echo '<div class="course-list course-list-completed course course-student-dashboard">';
			echo $course_list;
			echo '</div>';
			echo '</div>';
			echo '<div class="clearfix"></div>';
		}
?>
	</div>  <!-- student-dashboard-wrapper -->
<?php
	}

	public static function render_student_settings_page() {

		if ( ! is_user_logged_in() ) {
			_e( 'Du musst angemeldet sein, um die Aktion abzuschließen', 'brainpress' );
			exit;
		}

		$form_message_class = '';
		$form_message = '';

		if ( isset( $_POST['student-settings-submit'] ) ) {

			if ( ! isset( $_POST['student_settings_nonce'] ) || ! wp_verify_nonce( $_POST['student_settings_nonce'], 'student_settings_save' )
			) {
				_e( "Änderungen können nicht gespeichert werden weil nonce nicht verifiziert hat.", 'brainpress' );
			} else {
				$student_data = array();
				$student_data['ID'] = get_current_user_id();
				$form_errors = 0;

				do_action( 'brainpress_before_settings_validation' );

				if ( '' != $_POST['password'] ) {
					if ( $_POST['password'] == $_POST['password_confirmation'] ) {
						$student_data['user_pass'] = $_POST['password'];
					} else {
						$form_message = __( "Passwörter stimmen nicht überein", 'brainpress' );
						$form_message_class = 'red';
						$form_errors ++;
					}

					if (!BrainPress_Helper_Utility::is_password_strong()) {
						if(BrainPress_Helper_Utility::is_password_strength_meter_enabled())
						{
							$form_message = __('Dein Passwort ist zu schwach.', 'brainpress');
						}
						else {
							$form_message = sprintf(__('Dein Passwort muss mindestens %d Zeichen lang sein und mindestens einen Buchstaben und eine Zahl enthalten.', 'brainpress'), BrainPress_Helper_Utility::get_minimum_password_length());
						}
						$form_message_class = 'red';
						$form_errors++;
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

				if ( 0 == $form_errors ) {
					if ( BrainPress_Data_Student::update_student_data( get_current_user_id(), $student_data ) ) {
						$form_message = __( 'Das Profil wurde erfolgreich aktualisiert.', 'brainpress' );
						$form_message_class = 'regular';
					} else {
						$form_message = __( 'Beim Aktualisieren ist ein Fehler aufgetreten. Bitte überprüfe Formular und versuche es erneut.', 'brainpress' );
						$form_message_class = 'red';
					}
				}
			}
		}
		$student = get_userdata( get_current_user_id() );
?>
	<p class="<?php echo esc_attr( 'form-info-' . $form_message_class ); ?>"><?php echo esc_html( $form_message ); ?></p>
	<?php do_action( 'brainpress_before_settings_form' ); ?>
	<form id="student-settings" name="student-settings" method="post" class="student-settings">
	<?php wp_nonce_field( 'student_settings_save', 'student_settings_nonce' ); ?>
	<p><label><?php _e( 'Vorname', 'brainpress' ); ?>: <input type="text" name="first_name" value="<?php esc_attr_e( $student->user_firstname ); ?>"/></label></p><?php do_action( 'brainpress_after_settings_first_name' ); ?>

	<p><label><?php _e( 'Nachname', 'brainpress' ); ?>: <input type="text" name="last_name" value="<?php esc_attr_e( $student->user_lastname ); ?>"/></label></p><?php do_action( 'brainpress_after_settings_last_name' ); ?>

	<p><label><?php _e( 'E-Mail', 'brainpress' ); ?>: <input type="text" name="email" value="<?php esc_attr_e( $student->user_email ); ?>"/></label></p><?php do_action( 'brainpress_after_settings_email' ); ?>

	<p><label><?php _e( 'Benutzername', 'brainpress' ); ?>: <input type="text" name="username" value="<?php esc_attr_e( $student->user_login ); ?>" disabled="disabled"/> </label></p><?php do_action( 'brainpress_after_settings_username' ); ?>

	<p><label><?php _e( 'Passwort', 'brainpress' ); ?>: <input type="password" name="password" value="" placeholder="<?php _e( "Wird sich nicht ändern, wenn leer.", 'brainpress' ); ?>"/> </label></p><?php do_action( 'brainpress_after_settings_passwordon' ); ?>

	<p><label><?php _e( 'Bestätige das Passwort', 'brainpress' ); ?>: <input type="password" name="password_confirmation" value=""/> </label></p><?php do_action( 'brainpress_after_settings_pasword' ); ?>

	<p class="weak-password-confirm"><label><input type="checkbox" name="confirm_weak_password" value="1" /><?php _e( 'Bestätige die Verwendung eines schwachen Passworts', 'brainpress' ); ?></label></p><?php do_action( 'brainpress_after_settings_confirm_weak_password' ); ?>

<input type="submit" name="student-settings-submit" class="apply-button-enrolled" value="<?php _e( 'Änderungen speichern', 'brainpress' ); ?>"/>
	</form><?php
		do_action( 'brainpress_after_settings_form' );
	}
}

