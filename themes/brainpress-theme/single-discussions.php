<?php
/**
 * Die Vorlage zum Anzeigen aller einzelnen Beiträge.
 *
 * @package BrainPress
 */

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

global $post;

$course_id = $post->ID;

// Wenn nicht angemeldet, leite zur übergeordneten Kursseite weiter.
cp_can_access_course( $course_id );

get_header();
?>

<div id="primary" class="content-area brainpress-single-discussion">
	<main id="main" class="site-main" role="main">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<h1><?php echo do_shortcode( '[course_title course_id="' . $course_id . '"]' ); ?></h1>
			<div class="instructors-content">
				<?php echo do_shortcode( '[course_instructors style="list-flat" course_id="' . $course_id . '"]' ); ?>
			</div>

			<?php
			echo do_shortcode( '[course_unit_archive_submenu course_id="' . $course_id . '"]' );
			?>

			<div class="clearfix"></div>

			<?php
			get_template_part( 'content-discussion', 'single' );
			brainpress_post_nav();
			?>

		<?php endwhile; // end of the loop.  ?>
	</main><!-- #main -->
</div><!-- #primary -->

<?php
get_sidebar( 'footer' );
get_footer();
