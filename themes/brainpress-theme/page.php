<?php
/*
Template Name: Default (footer widgets)
*/

/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the ClassicPress construct of pages
 * and that other 'pages' on your ClassicPress site will use a
 * different template.
 *
 * @package BrainPress
 */

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

global $post;

get_header();

?>
<div id="primary" class="content-area brainpress-page">
	<main id="main" class="site-main" role="main">

		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'content', 'page' );

			// If comments are open or we have at least one comment, load up the comment template.
			if ( ( comments_open() || get_comments_number() ) && 'closed' != $post->comment_status ) {
				comments_template();
			}
		endwhile; // end of the loop.
		?>

	</main><!-- #main -->
</div><!-- #primary -->

<?php
get_sidebar( 'footer' );
get_footer();
