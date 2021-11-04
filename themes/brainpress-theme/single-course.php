<?php
/**
 * Die Vorlage zum Anzeigen aller einzelnen Beiträge.
 *
 * @package BrainPress
 */

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

get_header();
?>

<div id="primary" class="content-area brainpress-single-course">
	<main id="main" class="site-main" role="main">

	<?php
	while ( have_posts() ) :
		the_post();

		get_template_part( 'content-course-overview', 'single' );

		brainpress_post_nav();
	endwhile; // end of the loop.
	wp_reset_postdata();
	?>

	</main><!-- #main -->
</div><!-- #primary -->

<?php
get_sidebar( 'footer' );
get_footer();
