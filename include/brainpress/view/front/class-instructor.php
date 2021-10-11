<?php

class BrainPress_View_Front_Instructor {

	public static $discussion = false;  // Used for hooking discussion filters
	public static $title = ''; // The page title
	public static $last_instructor;

	public static function init() {

		add_action( 'parse_request', array( __CLASS__, 'parse_request' ) );

		/**
		 * Intercep virtual page when dealing with invitation code.
		 **/
		add_filter( 'brainpress_virtual_page', array( __CLASS__, 'instructor_verification' ), 10, 2 );

	}

	/**
	 * Try to load "single-instructor.php" template.
	 */
	public static function render_instructor_page() {
		BrainPress_Core::$is_cp_page = true;
		$theme_file = locate_template( array( 'single-instructor.php' ) );
		if ( $theme_file ) {
			BrainPress_View_Front_Course::$template = $theme_file;
			$content = '';
		} else {
			$content = BrainPress_Template_User::render_instructor_page();
		}
		return $content;
	}

	public static function parse_request( &$wp ) {
		if ( array_key_exists( 'instructor_username', $wp->query_vars ) ) {
			$username = sanitize_text_field( $wp->query_vars['instructor_username'] );
			$instructor = BrainPress_Data_Instructor::instructor_by_login( $username );
			if ( empty( $instructor ) ) {
				$instructor = BrainPress_Data_Instructor::instructor_by_hash( $username );
			}
			$content = '';
			if ( empty( $instructor ) ) {
				$content = __( 'Der angeforderte Kursleiter existiert nicht.', 'brainpress' );
			}
			self::$last_instructor = empty( $instructor ) ? 0 : $instructor->ID;
			$page_title = ! empty( self::$last_instructor ) ? BrainPress_Helper_Utility::get_user_name( self::$last_instructor, false, false ) : __( 'Kursleiter nicht gefunden.', 'brainpress' );
			$args = array(
				'slug' => 'instructor_' . self::$last_instructor,
				'title' => $page_title,
				'content' => ! empty( $content ) ? esc_html( $content ) : self::render_instructor_page(),
				'type' => 'brainpress_instructor',
			);
			$pg = new BrainPress_Data_VirtualPage( $args );
			return;
		}
	}

	/**
	 * Intercep the virtual page rendered in main course page.
	 *
	 * @since 2.0
	 *
	 * @param (mixed) $_vr_args		 The previous arguments used to construct a virtual page or (bool) false.
	 * @param (object) $cp		 The object.
	 **/
	public static function instructor_verification( $_vp_args, $cp ) {
		if ( ! isset( $_GET['action'] ) || 'course_invite' != $_GET['action'] ) {
			return $_vp_args;
		}
		$course_invite = BrainPress_Data_Instructor::is_course_invite();

		$vp_args = array(
			'slug' => 'instructor_verification' . $course_invite->course_id,
			'type' => BrainPress_Data_Course::get_post_type_name() . '_archive',
			'is_page' => true,
		);

		$args = array();

		if ( $course_invite ) {

			$is_verified = BrainPress_Data_Instructor::verify_invitation_code( $course_invite->course_id, $course_invite->code, $course_invite->invitation_data );

			if ( $is_verified ) {

				/**
				 * redirect to registration form
				 */
				if ( ! is_user_logged_in() ) {
					if ( BrainPress_Core::get_setting( 'general/use_custom_login' ) ) {
						$url = BrainPress_Core::get_slug( 'signup', true );
					} else {
						$url = wp_login_url();
					}

					$content = sprintf( '<p><a href="%s">%s</a> %s</p>', esc_url( $url ), __( 'Einloggen', 'brainpress' ), __( 'um weitermachen.', 'brainpress' ) );

					$args = array(
						'show_title' => false,
						'title' => apply_filters( 'brainpress_instructor_invitation_title', esc_html__( 'Kursleiter Einladung', 'brainpress' ) ),
						'content' => apply_filters( 'brainpress_instructor_invitation_content', $content ),
					);
					$vp_args = wp_parse_args( $args, $vp_args );

					return $vp_args;
				}

				$user = get_user_by( 'email', $is_verified['email'] );
				$user_id = $user->ID;

				$is_added = BrainPress_Data_Instructor::add_from_invitation( $course_invite->course_id, $user_id, $course_invite->code );

				if ( $is_added ) {
					$main_course = apply_filters( 'brainpress_view_course', BrainPress_View_Front_Course::render_course_main(), $course_invite->course_id, 'main' );
					$args = array(
						'show_title' => true,
						'title' => esc_html__( 'Einladung aktiviert', 'brainpress' ),
						'content' => sprintf(
							'<p>%s %s</p>%s',
							esc_html__( 'Herzliche Glückwünsche. Du bist jetzt Kursleiter dieses Kurses. ', 'brainpress' ),
							sprintf(
								'<a href="%s" class="blue-button small-button button-a">%s</a>',
								esc_url( get_permalink( $course_invite->course_id ) ),
								__( 'Kursdetails', 'brainpress' )
							),
							$main_course
						),
					);
				} else {
					$args = array(
						'show_title' => false,
						'title' => esc_html__( 'Ungültige Einladung', 'brainpress' ),
						'content' => sprintf(
							'<p>%s</p><p>%s</p>',
							esc_html__( 'Dieser Einladungslink ist nicht mit Deiner E-Mail-Adresse verknüpft.', 'brainpress' ),
							esc_html__( 'Bitte wende Dich an Deinen Kursadministrator und bitte ihn, eine neue Einladung an die E-Mail-Adresse zu senden, die u Deinem Konto zugeordnet hast.', 'brainpress' )
						),
					);
				}
			}
		}

		if ( empty( $args ) ) {
			$args = array(
				'show_title' => false,
				'title' => esc_html__( 'Einladung nicht gefunden', 'brainpress' ),
				'content' => sprintf(
					'<div class="cp-warning-box"><p>%s</p><p>%s</p></div>',
					esc_html__( 'Diese Einladung wurde nicht gefunden oder ist nicht mehr verfügbar.', 'brainpress' ),
					esc_html__( 'Bitte kontaktiere uns, wenn Du glaubst, dass dies ein Fehler ist.', 'brainpress' )
				),
			);
		}

		$vp_args = wp_parse_args( $args, $vp_args );

		return $vp_args;
	}

	public static function modal_view() {
		$invite_data = BrainPress_Data_Instructor::is_course_invite();
		?>
		<script type="text/template" id="modal-view4-template" data-type="modal-step" data-modal-action="instructor-verified">
			<div class="bbm-modal__topbar">
				<h3 class="bbm-modal__title"><?php esc_html_e( 'Einladung aktiviert.', 'brainpress' ); ?></h3>
			</div>
			<div class="bbm-modal__section">
				<p><?php esc_html_e( 'Herzliche Glückwünsche. Du bist jetzt Kursleiter dieses Kurses. ', 'brainpress' ); ?></p>
			</div>
			<div class="bbm-modal__bottombar">
				<a href="<?php echo esc_url( get_permalink( $invite_data->course_id ) ); ?>" class="bbm-button button"><?php esc_html_e( 'Fortsetzen...', 'brainpress' ); ?></a>
			</div>
		</script>

		<script type="text/template" id="modal-view5-template" data-type="modal-step" data-modal-action="verification-failed">
			<div class="bbm-modal__topbar">
				<h3 class="bbm-modal__title"><?php esc_html_e( 'Ungültige Einladung.', 'brainpress' ); ?></h3>
			</div>
			<div class="bbm-modal__section">
				<p><?php esc_html_e( 'Dieser Einladungslink ist nicht mit Deiner E-Mail-Adresse verknüpft.', 'brainpress' ); ?></p>
				<p><?php esc_html_e( 'Bitte wende Dich an den Kursadministrator und bitte ihn, eine neue Einladung an die E-Mail-Adresse zu senden, die Du Deinem Konto zugeordnet hast.', 'brainpress' ); ?></p>
			</div>
			<div class="bbm-modal__bottombar">
				<a href="<?php echo esc_url( get_permalink( $invite_data->course_id ) ); ?>" class="bbm-button button"><?php esc_html_e( 'Fortsetzen...', 'brainpress' ); ?></a>
			</div>
		</script>
		<?php
	}
}
