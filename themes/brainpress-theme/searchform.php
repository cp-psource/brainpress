<?php
/**
 * Die Vorlage zum Anzeigen von Suchformularen in BrainPress
 *
 * @package BrainPress
 */

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label>
		<span class="screen-reader-text"><?php _ex( 'Suchen nach:', 'label', 'brainpress' ); ?></span>
		<input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Suche &hellip;', 'placeholder', 'brainpress' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s">
	</label>
	<input type="submit" class="search-submit" value="<?php echo esc_attr_x( 'Suchen', 'submit button', 'brainpress' ); ?>">
</form>
