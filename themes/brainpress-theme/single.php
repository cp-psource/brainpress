<?php
/**
 * Die Vorlage zum Anzeigen aller einzelnen Beiträge.
 *
 * @package BrainPress
 */

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

get_header(); ?>

	<div id="primary" class="content-area content-side-area">
		<main id="main" class="site-main" role="main">

		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'content', 'single' );

			brainpress_post_nav();

			// Wenn Kommentare geöffnet sind oder wir mindestens einen Kommentar haben, lade die Kommentarvorlage
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile; // Ende der Schleife.
		?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
