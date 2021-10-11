/*! BrainPress - v2.2.2
 * https://n3rds.work/piestingtal_source/ps-brainpress-wordpress-lms-online-akademie-plugin//
 * Copyright (c) 2019; * Licensed GPLv2+ */
(function( $ ){
    $( document ).ready( function() {
		$( '.cp_featured_widget_course_link .apply-button.apply-button-details' ).on( 'click', function( e ) {
			var target = e.currentTarget;

			if ( $( target ).attr( 'data-link' ) ) {
				window.location.href = $( target ).attr( 'data-link' );
			}
		} );
    
    } );
})( jQuery );