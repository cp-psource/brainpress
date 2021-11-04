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
		<div class="instructors-content">
			<?php echo do_shortcode( '[course_instructors list="true" link="true"]' ); ?>
		</div>
	</header><!-- .entry-header -->

	<section id="course-summary">
		<?php
		$course_media = do_shortcode( '[course_media]' );
		if ( $course_media ) :
			?>
			<div class="course-video">
				<?php
				// Show course media
				echo $course_media;
				?>
			</div>
		<?php endif; ?>

		<div class="entry-content-excerpt <?php echo ($course_media ? '' : 'entry-content-excerpt-right' ); ?>">
			<div class="course-box">
				<?php
				// Change to yes for 'Open-ended'.
				echo do_shortcode( '[course_dates show_alt_display="yes"]' );

				// Change to yes for 'Open-ended'.
				echo do_shortcode( '[course_enrollment_dates show_alt_display="no"]' );
				echo do_shortcode( '[course_class_size]' );
				echo do_shortcode( '[course_enrollment_type label="' . __( 'Wer kann sich einschreiben?: ', 'brainpress' ) . '"]' );
				echo do_shortcode( '[course_language]' );
				echo do_shortcode( '[course_cost]' );
				?>
			</div><!--course-box-->
			<div class="quick-course-info">
				<?php echo do_shortcode( '[course_join_button]' ); ?>
			</div>
		</div>
	</section>

	<section id="additional-summary">
		<div class="social-shares">
			<span>
				<?php _e( 'KURS TEILEN', 'brainpress' ); ?>
			</span>
			<a href="http://www.facebook.com/sharer/sharer.php?s=100&p[url]=<?php the_permalink(); ?>&p[images][0]=&p[title]=<?php the_title(); ?>&p[summary]=<?php echo urlencode( strip_tags( get_the_excerpt() ) ); ?>" class="facebook-share" target="_blank"></a>
			<a href="http://twitter.com/home?status=<?php the_title(); ?> <?php the_permalink(); ?>" class="twitter-share" target="_blank"></a>
			<a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php the_permalink(); ?>" class="linkedin-share" target="_blank"></a>
			<a href="mailto:?subject=<?php the_title(); ?>&body=<?php echo strip_tags( get_the_excerpt() ); ?>" target="_top" class="email-share"></a>
		</div><!--social shares-->
	</section>

	<br clear="all" />
<?php $instructors = BrainPress_Data_Shortcode_Instructor::course_instructors( array( 'style' => 'block' ) ); ?>
<div class="entry-content <?php echo esc_attr( empty( $instructors )? '':'left-content' ); ?>">
		<h1 class="h1-about-course"><?php _e( 'Über diesen Kurs', 'brainpress' ); ?></h1>
		<div class="content"><?php echo do_shortcode( '[course_description course_id="' . get_the_ID() . '"]' ); ?></div>
<?php
if ( BrainPress_Data_Course::get_setting( get_the_ID(), 'structure_visible', true ) ) : ?>
			<h1 class = "h1-about-course"><?php
			_e( 'Kursaufbau', 'brainpress' );
			?></h1>
			<?php echo do_shortcode( '[course_structure label="" show_title="no" show_divider="yes"]' );
		endif;

		wp_link_pages(
			array(
				'before' => '<div class="page-links">' . __( 'Seiten:', 'brainpress' ),
				'after' => '</div>',
			)
		);
		?>
	</div><!-- .entry-content -->

	<?php if ( ! empty( $instructors ) ) : ?>
		<div class="course-instructors right-content">
			<h1 class="h1-instructors"><?php _e( 'Kursleiter', 'brainpress' ); ?></h1>
			<?php echo $instructors; ?>
		</div><!--course-instructors right-content-->
	<?php endif; ?>

	<br clear="all" />

	<footer class="entry-meta">
		<?php

		$args = array(
			'echo' => false,
			'hierarchical' => false,
			'style' => '',
			'taxonomy' => 'course_category',
		);
		$category_list = '';
		$categories = wp_get_post_terms( get_the_ID(), 'course_category' );

		$cats = array();
		foreach( $categories as $cat ) {
			$cats[] = sprintf( '<a href="%s">%s</a>', get_term_link( $cat->term_id ), $cat->name );
		}
		$category_list = implode(', ', $cats );

		// Translators: Used between list items, there is a space after the comma.
		$tag_list = get_the_tag_list( '', __( ', ', 'brainpress' ) );

		/**
		 * default meta text
		 */
		$meta_text = __( 'Setze ein Lesezeichen für den <a href="%3$s" rel="bookmark">Permalink</a>.', 'brainpress' );
		/**
		 * check categories and tags
		 */
		if ( ! brainpress_categorized_blog() ) {
			// This blog only has 1 category so we just need to worry about tags in the meta text.
			if ( $tag_list ) {
				$meta_text = __( 'Dieser Eintrag wurde %2$s getaggt. Setze ein Lesezeichen für den <a href="%3$s" rel="bookmark">Permalink</a>.', 'brainpress' );
			} else {
				$meta_text = '';
			}
		} else {
			// But this blog has loads of categories so we should probably display them here.
			if ( ! empty( $tag_list ) ) {
				$meta_text = __( 'Dieser Eintrag wurde veröffentlicht in %1$s und getaggt mit %2$s. Setze ein Lesezeichen für den <a href="%3$s" rel="bookmark">Permalink</a>.', 'brainpress' );
			} else if ( ! empty( $category_list ) ) {
				$meta_text = __( 'Dieser Eintrag wurde veröffentlicht in %1$s. Setze ein Lesezeichen für den <a href="%3$s" rel="bookmark">Permalink</a>.', 'brainpress' );
			}
		} // end check for categories on this blog.

		printf(
			$meta_text,
			$category_list,
			$tag_list,
			get_permalink()
		);
		?>

		<?php
		edit_post_link(
			__( 'Bearbeiten', 'brainpress' ),
			'<span class="edit-link">',
			'</span>'
		);
		?>
	</footer><!-- .entry-meta -->
</article><!-- #post-## -->
