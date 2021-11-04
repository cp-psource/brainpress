<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package BrainPress
 */

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<h1 class="entry-title"><?php the_title(); ?></h1>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php
		the_content();
		wp_link_pages(
			array(
				'before' => '<div class="page-links">' . __( 'Seiten:', 'brainpress' ),
				'after' => '</div>',
			)
		);
		?>
	</div><!-- .entry-content -->
	<?php
	edit_post_link(
		__( 'Bearbeiten', 'brainpress' ),
		'<footer class="entry-meta"><span class="edit-link">',
		'</span></footer>'
	);
	?>
</article><!-- #post-## -->
