<?php
/**
 * The template use for BrainPress custom registration form.
 *
 * @since 2.0.5
 **/
?>
<div class="brainpress-form brainpress-form-signup">
	<?php if ( ! empty( $signup_title ) ) : ?>
		<?php printf( '<%1$s>%2$s</%1$s>', $signup_tag, $signup_title ); ?>
	<?php endif; ?>
	<p class="form-info-<?php echo $form_message_class; ?>"><?php echo $form_message; ?></p>

	<form id="student-settings" name="student-settings" method="post" class="student-settings signup-form">
		<?php
		/**
		 * Trigger before the signup form.
		 **/
		do_action( 'brainpress_before_signup_form' );

		/**
		 * Trigger before signup fields are printed.
		 **/
		do_action( 'brainpress_before_all_signup_fields' );
		?>
		<input type="hidden" name="course_id" value="<?php echo $course_id; ?>"/>
		<input type="hidden" name="redirect_url" value="<?php echo esc_url( $redirect_url ); ?>"/>
		<label class="firstname">
			<span><?php _e( 'Vorname', 'brainpress' ); ?>:</span>
			<input type="text" name="first_name" value="<?php echo esc_attr( $first_name ); ?>"/>
		</label>
		<?php
		/**
		 * Trigger after first_name field.
		 **/
		do_action( 'brainpress_after_signup_first_name' );
		?>

		<label class="lastname">
			<span><?php _e( 'Nachname', 'brainpress' ); ?>:</span>
			<input type="text" name="last_name" value="<?php echo esc_attr( $last_name ); ?>"/>
		</label>
		<?php
		/**
		 * Trigger after last_name field.
		 **/
		do_action( 'brainpress_after_signup_last_name' );
		?>

		<label class="username">
			<span><?php _e( 'Benutzername', 'brainpress' ); ?>:</span>
			<input type="text" name="username" value="<?php echo esc_attr( $username ); ?>" />
		</label>
		<?php
		/**
		 * Trigger after printing username.
		 **/
		do_action( 'brainpress_after_signup_username' );
		?>

		<label class="email">
			<span><?php _e( 'E-Mail', 'brainpress' ); ?>:</span>
			<input type="text" name="email" value="<?php echo esc_attr( $email ); ?>" />
		</label>
		<?php
		/**
		 * Trigger after email field.
		 **/
		do_action( 'brainpress_after_signup_email' );
		?>

		<label class="password">
			<span><?php _e( 'Passwort', 'brainpress' ); ?>:</span>
			<input type="password" name="password" value=""/>
		</label>
		<?php
		/**
		 * Trigger after password field.
		 **/
		do_action( 'brainpress_after_signup_password' );
		?>

		<label class="password-confirm right">
			<span><?php _e( 'Bestätige das Passwort', 'brainpress' ); ?>:</span>
			<input type="password" name="password_confirmation" value=""/>
		</label>
		<label class="weak-password-confirm">
			<input type="checkbox" name="confirm_weak_password" value="1" />
			<span><?php _e( 'Bestätige die Verwendung eines schwachen Passworts', 'brainpress' ); ?></span>
		</label>

		<?php if ( shortcode_exists( 'signup-tos' ) && '1' == get_option( 'show_tos', 0 ) ) : ?>
			<label class="tos full">
				<?php echo do_shortcode( '[signup-tos]' ); ?>
			</label>
		<?php endif; ?>

		<?php
		/**
		 * Trigger after all signup fields are rendered.
		 **/
		do_action( 'brainpress_after_all_signup_fields' );
		?>

		<label class="existing-link full">
			<?php printf( __( 'Hast Du bereits ein Konto? %s%s%s!', 'brainpress' ), '<a href="' . esc_url( $login_url ) . '">', __( 'Melde dich in deinem Konto an', 'brainpress' ), '</a>' ); ?>
		</label>
		<label class="submit-link full-right">
			<input type="submit" name="student-settings-submit" class="apply-button-enrolled" value="<?php esc_attr_e( 'Konto erstellen', 'brainpress' ); ?>" />
		</label>

		<?php
		/**
		 * Trigger when registration form submitted.
		 **/
		do_action( 'brainpress_after_submit' );

		wp_nonce_field( 'student_signup', '_wpnonce', true );

		/**
		 * Trigger after all signform fields are printed.
		 **/
		do_action( 'brainpress_after_signup_form' );
		?>
	</form>
</div>
