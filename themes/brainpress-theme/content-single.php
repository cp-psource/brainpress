<?php
/**
 * @package BrainPress
 */

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<h1 class="entry-title"><?php the_title(); ?></h1>

		<div class="entry-meta">
			<?php brainpress_posted_on(); ?>
		</div><!-- .entry-meta -->
	</header><!-- .entry-header -->
	<?php

	if ( has_post_thumbnail() ) {
		echo '<div class="featured-image">';
		the_post_thumbnail();
		echo '</div>';
	}

	?>
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

	<footer class="entry-meta">
		<?php

		// translators: Used between list items, there is a space after the comma.
		$category_list = get_the_category_list( __( ', ', 'brainpress' ) );

		// translators: Used between list items, there is a space after the comma.
		$tag_list = get_the_tag_list( '', __( ', ', 'brainpress' ) );

		if ( ! brainpress_categorized_blog() ) {
			// This blog only has 1 category so we just need to worry about tags in the meta text
			if ( $tag_list ) {
				$meta_text = __( 'Dieser Eintrag wurde mit %2$s markiert. Setze ein Lesezeichen für den <a href="%3$s" rel="bookmark">Permalink</a>.', 'brainpress' );
			} else {
				$meta_text = __( 'Setze ein Lesezeichen für den <a href="%3$s" rel="bookmark">Permalink</a>.', 'brainpress' );
			}
		} else {
			// But this blog has loads of categories so we should probably display them here
			if ( $tag_list ) {
				$meta_text = __( 'Dieser Beitrag wurde unter %1$s veröffentlicht und mit %2$s markiert. Setze ein Lesezeichen für den <a href="%3$s" rel="bookmark">Permalink</a>.', 'brainpress' );
			} else {
				$meta_text = __( 'Dieser Beitrag wurde unter %1$s veröffentlicht. Setze ein Lesezeichen für den <a href="%3$s" rel="bookmark">Permalink</a>.', 'brainpress' );
			}
		} // end check for categories on this blog

		printf(
			$meta_text,
			$category_list,
			$tag_list,
			get_permalink()
		);

		edit_post_link(
			__( 'Bearbeiten', 'brainpress' ),
			'<span class="edit-link">',
			'</span>'
		);
		?>
	</footer><!-- .entry-meta -->
</article><!-- #post-## -->
