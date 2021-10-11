<div class="wrap brainpress_wrapper brainpress-students">
	<h2><?php esc_html_e( 'Studenten', 'brainpress' ); ?></h2>
	<hr />

	<form method="post">
		<?php
		wp_nonce_field( $this->slug, $this->slug );
		$this->students_list->display();
		?>
	</form>
</div>