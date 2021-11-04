<?php
/**
 * Die Vorlage zum Anzeigen einzelner Beitragsposten mit Modulen
 *
 * @package BrainPress
 */

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

global $wp, $wp_query;

$course_id = do_shortcode( '[get_parent_course_id]' );

add_thickbox();

$paged = ! empty( $wp->query_vars['paged'] ) ? absint( $wp->query_vars['paged'] ) : 1;

// Leite zur übergeordneten Kursseite weiter, wenn nicht angemeldet oder keine Vorschau der Einheit/Seite angezeigt wird.
while ( have_posts() ) :
	the_post();
	BrainPress_Data_Course::previewability( $course_id );
endwhile;

get_header();

?>
<div id="primary" class="content-area brainpress-single-unit">
	<main id="main" class="site-main" role="main">
		<?php while ( have_posts() ) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header class="entry-header">
					<h3 class="entry-title course-title">
						<?php
						echo do_shortcode( '[course_title course_id="' . $course_id . '"]' );
						?>
					</h3>
					<?php
					$is_focus = BrainPress_Data_Course::get_setting( $course_id, 'course_view', 'normal' );
					if ( 'focus' == $is_focus ) :
						echo do_shortcode( '[course_unit_archive_submenu course_id="' . $course_id . '"]' );
					endif;
					?>
				</header><!-- .entry-header -->
				<div class="instructors-content"></div>

				<div class="clearfix"></div>

				<?php
				echo do_shortcode( '[course_title course_id="' . get_the_ID() . '"]' );
				echo BrainPress_Template_Unit::unit_with_modules();
				?>
			</article>
		<?php endwhile; // Ende der Schleife. ?>
	</main><!-- #main -->
</div><!-- #primary -->

<?php
get_sidebar( 'footer' );
get_footer();
