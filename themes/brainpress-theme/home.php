<?php
/**
 * The home template file
 *
 * useful if you use static page for posts page
 * if doesn't exists, index.php will be used in this case
 * even if you select the page template - it will be ignored
 *
 * @package BrainPress
 */

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

get_header();

?>
<div id="primary" class="content-area content-side-area">
	<main id="main" class="site-main" role="main">

		<?php
		if ( have_posts() ) :

			/* Start the Loop */
			while ( have_posts() ) :
				the_post();

				/* Include the Post-Format-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php ( where ___ is the Post Format name ) and that will be used instead.
				 */
				get_template_part( 'content', get_post_format() );

			endwhile;

			cp_numeric_posts_nav( 'navigation-pagination' );

		else :

			get_template_part( 'content', 'none' );

		endif;
		?>

	</main><!-- #main -->
</div><!-- #primary -->
<?php

get_sidebar();
get_footer();
