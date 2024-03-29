<?php
/**
 * @package BrainPress
 */

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

$args  = BrainPress_Data_Discussion::get_query_args_by_name();
$the_query = new WP_Query( $args );
if ( $the_query->have_posts() ) {
	while ( $the_query->have_posts() ) {
		$the_query->the_post();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">

		<div class="discussion-archive-single-meta">
			<div class="discussion-comments-circle">
				<span class="comments-count">
					<?php echo get_comments_number(); ?>
				</span>
			</div>
		</div>

		<div class="discussion-archive-single">
			<h1 class="discussion-title"><?php the_title(); ?></h1>

			<div class="entry-content">
				<?php the_content(); ?>
			</div><!-- .entry-content -->
			<div class="discussion-meta">
				<span><?php echo get_the_date(); ?></span> |
				<span><?php the_author(); ?></span> |
				<span><?php //echo $discussion->get_unit_name(); ?></span> |
				<span><?php echo get_comments_number(); ?> <?php _e( 'Kommentare', 'brainpress' );?></span>
			</div>
			<div class="clearfix"></div>
		</div>

		<div class="discussion-responses">
			<?php
			// If comments are open or we have at least one comment, load up the comment template
			if ( comments_open() || get_comments_number() ) {
				comments_template( '/comments-discussion.php' );
			} else {
				_e( 'Kommentare sind deaktiviert', 'brainpress' );
			}
			?>
		</div>

	</header><!-- .entry-header -->

	<footer class="entry-meta">
		<?php
		edit_post_link(
			__( 'Bearbeiten', 'brainpress' ),
			'<span class="edit-link">',
			'</span>'
		);
		?>
	</footer><!-- .entry-meta -->
</article><!-- #post-## -->
<?php
	}
}
