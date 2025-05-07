<div class="wrap brainpress_wrapper brainpress-export">
	<h2><?php esc_html_e( 'Exportieren', 'brainpress' ); ?></h2>
	<p class="description page-tagline">
		<?php esc_html_e( 'Wähle Kurse aus, die auf eine andere Webseite exportiert werden sollen.', 'brainpress' ); ?>
	</p>
	<form method="post" class="has-disabled">
		<?php wp_nonce_field( 'brainpress_export', 'brainpress_export' ); ?>
		<div class="cp-left">
			<p>
				<label>
					<input type="checkbox" class="input-key" name="brainpress[all]" value="1" />
					<?php esc_html_e( 'Alle Kurse', 'brainpress' ); ?>
				</label>
			</p>
			<?php
			$per_page = 20;
			$paged = ! empty( $_REQUEST['paged'] ) ? (int) $_REQUEST['paged'] : 1;

			$args = array(
				'post_type' => BrainPress_Data_Course::get_post_type_name(),
				'post_status' => array( 'publish', 'draft', 'private' ),
				'posts_per_page' => $per_page,
				'paged' => $paged,
				'suppress_filters' => true,
			);
			$courses = new WP_Query( $args );

			if ( $courses->have_posts() ):
				while( $courses->have_posts() ): $courses->the_post();
			?>
				<p>
					<label>
						<input type="checkbox" class="input-key" name="brainpress[courses][<?php the_ID(); ?>]" value="<?php the_ID(); ?>" />
						<?php the_title(); ?>
					</label>
				</p>
			<?php
				endwhile;
			endif;
			?>
		</div>
		<div>
			<h3><?php esc_html_e( 'Export Optionen', 'brainpress' ); ?></h3>
			<div>
				<label>
					<input type="checkbox" name="brainpress[students]" class="input-requiredby" value="1" />
					<?php esc_html_e( 'Studenten einbeziehen', 'brainpress' ); ?>
				</label>
				<p class="description">
					<?php esc_html_e( 'Umfasst die Kursteilnehmer und ihren Fortschritt bei der Einreichung von Kursen.', 'brainpress' ); ?>
				</p>
			</div><br />
			<div>
				<label>
					<input type="checkbox" name="brainpress[comments]" data-required-imput="brainpress[students]" disabled="disabled" value="1" />
					<?php esc_html_e( 'Thread/Kommentare einschließen', 'brainpress' ); ?>
				</label>
				<p class="description">
					<?php esc_html_e( 'Enthält Kommentare aus dem Kursforum und Diskussionsmodulen.', 'brainpress' ); ?>
				</p>
			</div>
		</div>
		<div class="cp-right">
		<?php
			// Show paginate
			echo BrainPress_Helper_UI::admin_paginate( $paged, $courses->found_posts, $per_page, '', __( 'Kurse', 'brainpress' ) );
		?>
		</div>
		<div class="clear cp-submit">
			<?php submit_button( __( 'Exportiere Kurse', 'brainpress' ), 'button-primary disabled' ); ?>
		</div>
	</form>
</div>
