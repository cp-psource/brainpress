<?php
/**
 * The class use to process user registration and login.
 *
 * @class BrainPress_UserLogin
 * @version 2.0.5
 **/
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

if ( ! class_exists( 'BrainPress_UserLogin' ) ) :
	class BrainPress_UserLogin extends BrainPress_Utility {
		/**
		 * Warning or error message to display on registration form.
		 *
		 * @var (string)
		 **/
		static $form_message = '';

		/**
		 * Form class to render on registration form.
		 *
		 * @var (string)
		 **/
		static $form_message_class = '';

		/**
		 * Process user registration submission.
		 **/
		public static function process_registration_form() {
			if ( isset( $_POST['student-settings-submit'] ) && isset( $_POST['_wpnonce'] )
				&& wp_verify_nonce( $_POST['_wpnonce'], 'student_signup' ) ) {

				check_admin_referer( 'student_signup' );

				/**
				 * Trigger before validating registration form
				 **/
				do_action( 'brainpress_before_signup_validation' );

				$min_password_length = BrainPress_Helper_Utility::get_minimum_password_length();
				$username = $_POST['username'];
				$firstname = $_POST['first_name'];
				$lastname = $_POST['last_name'];
				$email = $_POST['email'];
				$passwd = $_POST['password'];
				$passwd2 = $_POST['password_confirmation'];
				$redirect_url = $_POST['redirect_url'];
				$found_errors = 0;

				if ( $username && $firstname && $lastname && $email && $passwd && $passwd2 ) {
					if ( username_exists( $username ) ) {
						self::$form_message = __( 'Benutzername existiert bereits. Bitte wähle einen anderen.', 'brainpress' );
						$found_errors++;
					} elseif ( ! validate_username( $username ) ) {
						self::$form_message = __( 'Ungültiger Benutzername!', 'brainpress' );
						$found_errors++;
					} elseif ( ! is_email( $email ) ) {
						self::$form_message = __( 'Email Adresse ist nicht gültig.', 'brainpress' );
						$found_errors++;
					} elseif ( email_exists( $email ) ) {
						self::$form_message = __( 'Entschuldigung, diese E-Mail Adresse wird bereits verwendet!', 'brainpress' );
						$found_errors++;
					} elseif ( $passwd != $passwd2 ) {
						self::$form_message = __( 'Passwörter stimmen nicht überein', 'brainpress' );
						$found_errors++;
					} elseif ( ! BrainPress_Helper_Utility::is_password_strong() ) {
						if ( BrainPress_Helper_Utility::is_password_strength_meter_enabled() ) {
							self::$form_message = __( 'Dein Passwort ist zu schwach.', 'brainpress' );
						} else {
							self::$form_message = sprintf( __( 'Dein Passwort muss mindestens %d Zeichen lang sein und mindestens einen Buchstaben und eine Zahl enthalten.', 'brainpress' ), $min_password_length );
						}
						$found_errors++;
					} elseif ( isset( $_POST['tos_agree'] ) && ! cp_is_true( $_POST['tos_agree'] ) ) {
						self::$form_message = __( 'Du musst den Nutzungsbedingungen zustimmen, um Dich anmelden zu können.', 'brainpress' );
						$found_errors++;
					}
				} else {
					self::$form_message = __( 'Alle Felder sind erforderlich.', 'brainpress' );
					$found_errors++;
				}

				if ( $found_errors > 0 ) {
					self::$form_message_class = 'red';
				} else {
					// Register new user
					$student_data = array(
						'default_role' => get_option( 'default_role', 'subscriber' ),
						'user_login' => $username,
						'user_email' => $email,
						'first_name' => $firstname,
						'last_name' => $lastname,
						'user_pass' => $passwd,
						'password_txt' => $passwd,
					);

					$student_data = BrainPress_Helper_Utility::sanitize_recursive( $student_data );
					$student_id = wp_insert_user( $student_data );

					if ( ! empty( $student_id ) ) {
						// Send registration email
						BrainPress_Data_Student::send_registration( $student_id, $student_data );

						$creds = array(
							'user_login' => $username,
							'user_password' => $passwd,
							'remember' => true,
						);
						$user = wp_signon( $creds, false );

						if ( is_wp_error( $user ) ) {
							self::$form_message = $user->get_error_message();
							self::$form_message_class = 'red';
						}

						if ( ! empty( $_POST['course_id'] ) ) {
							$url = get_permalink( (int) $_POST['course_id'] );
							wp_safe_redirect( $url );
						} else {
							if ( ! empty( $redirect_url ) ) {
								wp_safe_redirect( esc_url_raw( apply_filters( 'brainpress_redirect_after_signup_redirect_url', $redirect_url ) ) );
							} else {
								wp_safe_redirect( esc_url_raw( apply_filters( 'brainpress_redirect_after_signup_url', BrainPress_Core::get_slug( 'student_dashboard', true ) ) ) );
							}
						}
						exit;
					} else {
						self::$form_message = __( 'Beim Erstellen des Kontos ist ein Fehler aufgetreten. Bitte überprüfe das Formular und versuche es erneut.', 'brainpress' );
						self::$form_message_class = 'red';
					}
				}
			}
		}

		/**
		 * Render registration form if current user is not logged-in.
		 *
		 * @param (string) $redirect_url
		 * @param (string) $login_url
		 * @param (string) $signup_title
		 * @param (string) $signup_tag
		 *
		 * @return Returns registration form or null.
		 **/
		public static function get_registration_form( $redirect_url = '', $login_url = '', $signup_title = '', $signup_tag = '' ) {
			if ( is_user_logged_in() ) {
				return '';
			}

			ob_start();

			/**
			 * Allow $form_message_class to be filtered before applying.
			 *
			 * @param (string) $form_message_class
			 **/
			self::$form_message_class = apply_filters( 'signup_form_message_class', self::$form_message_class );

			/**
			 * Allow form message to be filtered before rendering.
			 *
			 * @param (string) $form_message
			 **/
			self::$form_message = apply_filters( 'signup_form_message', self::$form_message );

			$args = array(
				'signup_title' => $signup_title,
				'signup_tag' => $signup_tag,
				'form_message' => self::$form_message,
				'form_message_class' => self::$form_message_class,
				'course_id' => isset( $_GET['course_id'] ) ? (int) $_GET['course_id'] : 0,
				'redirect_url' => $redirect_url,
				'login_url' => $login_url,
				'first_name' => isset( $_POST['first_name'] ) ? $_POST['first_name'] : '',
				'last_name' => isset( $_POST['last_name'] ) ? $_POST['last_name'] : '',
				'username' => isset( $_POST['username'] ) ? $_POST['username'] : '',
				'email' => isset( $_POST['email'] ) ? $_POST['email'] : '',
			);

			self::render( 'include/brainpress/view/registration-form', $args );

			$content = ob_get_clean();
			$content = preg_replace( '%\\r\\n|\\n%', '', $content );

			return $content;
		}
	}
endif;
