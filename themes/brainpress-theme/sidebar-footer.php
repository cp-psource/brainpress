<?php
/**
 * Die Seitenleiste mit den Fußzeilen-Widget-Bereichen.
 *
 * @package BrainPress
 */

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

?>
<div id="third" class="widget-area footer-widget-area clearf" role="complementary">
	<?php
	do_action( 'before_sidebar' );

	if ( ! dynamic_sidebar( 'sidebar-2' ) ) :
		// Kein Standardinhalt...
	endif; // Widget-Bereich der Seitenleiste beenden
	?>
</div><!-- #secondary -->
