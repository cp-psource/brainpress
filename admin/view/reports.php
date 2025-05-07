<div class="wrap brainpress_wrapper brainpress-reports">
	<h2><?php esc_html_e( 'Berichte', 'brainpress' ); ?></h2>
	<hr />

	<form method="post">
		<?php
		wp_nonce_field( 'brainpress_report' );
		$this->reports_table->display();
		?>
	</form>
</div>