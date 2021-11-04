<?php
/**
 * The template part for displaying a message that posts cannot be found.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package BrainPress
 */

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

?>
<section class="no-results not-found">
	<header class="page-header">
		<h1 class="page-title"><?php _e( 'Keine Kurse gefunden', 'brainpress' ); ?></h1>
	</header><!-- .page-header -->

	<div class="page-content">
		<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

			<p><?php
			printf(
				__( 'Bereit Deinen ersten Post zu veröffentlichen? <a href="%1$s">Erste Schritte hier</a>.', 'brainpress' ),
				esc_url( admin_url( 'post-new.php' ) )
			);
			?></p>

		<?php elseif ( is_search() ) : ?>

			<p><?php
			_e( 'Entschuldigung, aber nichts stimmte mit Deinen Suchbegriffen überein. Bitte versuche es erneut mit anderen Schlüsselwörtern.', 'brainpress' );
			?></p>
			<?php get_search_form(); ?>

		<?php else : ?>

			<p><?php
			_e( 'Anscheinend können wir nicht finden, wonach Du suchst. Vielleicht hilft die Suche.', 'brainpress' );
			?></p>
			<?php get_search_form(); ?>

		<?php endif; ?>
	</div><!-- .page-content -->
</section><!-- .no-results -->
