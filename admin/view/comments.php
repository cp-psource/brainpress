<div class="wrap brainpress_wrapper brainpress-comments">
	<h1><?php esc_html_e( 'Kommentare', 'brainpress' ); ?></h1>
	<hr />

	<form method="post">
		<?php
		wp_nonce_field( $this->slug, $this->slug );
		$this->comments_list->display();
		?>
	</form>
</div>
