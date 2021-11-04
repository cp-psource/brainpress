<?php

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

$form_message_class = false;
$form_message = false;

if ( isset( $_POST['contact_submit_button'] ) ) {

	check_admin_referer( 'contact_submit' );

	do_action( 'before_contact_validation' );

	if (
		empty( $_POST['sender_name'] ) ||
		empty( $_POST['sender_email'] ) ||
		empty( $_POST['subject'] ) ||
		empty( $_POST['message'] )
	) {
		$form_message = __( 'Alle Felder sind erforderlich.', 'brainpress' );
		$form_message_class = 'error';
	} elseif ( ! is_email( $_POST['sender_email'] ) ) {
		$form_message = __( 'Email Adresse ist nicht gültig.', 'brainpress' );
		$form_message_class = 'error';
	} else {
		add_filter( 'wp_mail_from', 'brainpress_set_sender_from_email' );

		if ( ! function_exists( 'brainpress_set_sender_from_email' ) ) :
			function brainpress_set_sender_from_email( $email ) {
				return sanitize_email( $_POST['sender_email'] );
			}
		endif;

		add_filter( 'wp_mail_from_name', 'brainpress_set_sender_from_name' );

		if ( ! function_exists( 'brainpress_set_sender_from_name' ) ) :
			function brainpress_set_sender_from_name( $name ) {
				return sanitize_text_field( $_POST['sender_name'] );
			}
		endif;

		$sent = wp_mail(
			get_option( 'admin_email' ),
			$_POST['subject'],
			$_POST['message']
		);

		if ( $sent ) {
			$form_message = __( 'Email wurde erfolgreich Versendet! Wir werden so schnell wie möglich antworten.', 'brainpress' );
			$form_message_class = 'regular';
		} else {
			$form_message = __( 'Beim Versuch, die E-Mail zu senden, ist ein Fehler aufgetreten. Bitte versuche es später noch einmal.', 'brainpress' );
			$form_message_class = 'error';
		}
	}
}

if ( $form_message ) :
	?>
	<p class="form-info-<?php echo $form_message_class; ?>">
		<?php echo $form_message; ?>
	</p>
	<?php
endif;

do_action( 'before_contact_form' );

?>
<form id="contact_form" name="contact-form" method="post" class="contact-form">
	<label class="full">
		<?php _e( 'Dein Name', 'brainpress' ); ?>:
		<input type="text" name="sender_name" value="" />
	</label>
	<?php do_action( 'after_contact_name' ); ?>
	<label class="full">
		<?php _e( 'Deine E-Mail', 'brainpress' ); ?>:
		<input type="text" name="sender_email" value="" />
	</label>
	<?php do_action( 'after_contact_email' ); ?>
	<label class="full">
		<?php _e( 'Betreff', 'brainpress' ); ?>:
		<input type="text" name="subject" value="" />
	</label>
	<?php do_action( 'after_contact_subject' ); ?>
	<label class="right">
		<?php _e( 'Nachricht', 'brainpress' ); ?>:
		<textarea name="message"></textarea>
	</label>
	<?php do_action( 'after_contact_message' ); ?>
	<input type="submit" name="contact_submit_button" class="apply-button-enrolled" value="<?php _e( 'Senden', 'brainpress' ); ?>" />

	<?php wp_nonce_field( 'contact_submit' ); ?>
</form>
<?php

do_action( 'after_contact_form' );
