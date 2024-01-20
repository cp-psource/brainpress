/*! BrainPress - v2.2.2
 * https://n3rds.work/piestingtal_source/ps-brainpress-classicpress-lms-online-akademie-plugin/
 * Copyright (c) 2019; * Licensed GPLv2+ */
(jQuery(function() {
	jQuery(document).ready( function($) {
		var brainpress_upgrade_form = $('form#brainpress-update-courses-form');
		var brainpress_upgrade_holder = $('#brainpress-updater-holder');
		var brainpress_upgrade_original = brainpress_upgrade_holder.html();
		var brainpress_upgrade_spinner = '<p class="working"><span><i class="fa fa-spinner fa-pulse"></i></span> ' + brainpress_upgrade_form.data('label-working') + '</p>';
		/**
		 * Do not process if ther is no upgrade form.
		 */
		if ( 0 === brainpress_upgrade_form.length ) {
			return;
		}
		/**
		 * handle button click
		 */
		$('.button').on( 'click', brainpress_upgrade_form, function() {
			var $thiz = $(this);
			var course_id = $('input[name=course]', brainpress_upgrade_form ).val();
			input_data = {
				action: "brainpress_upgrade_update",
				user_id: $("input[name=user_id]").val(),
				_wpnonce: $("input[name=_wpnonce]").val(),
				_wp_http_referer: $("input[name=_wp_http_referer]").val(),
				course_id: "",
				section: ""
			};
			brainpress_upgrade_holder.html( brainpress_upgrade_spinner );
			brainpress_upgrade_course( course_id , false, input_data );
			$( ".working", brainpress_upgrade_holder ).detach();
			return false;
		});
		function brainpress_upgrade_course( course_id, section, input_data) {
			brainpress_upgrade_holder.append( brainpress_upgrade_spinner );
			input_data.course_id = course_id;
			input_data.section = section;
			$.ajax({
				type: "POST",
				url: ajaxurl,
				data: input_data,
				dataType: "json"
			}).done( function(data) {
				if ( data.success ) {
					$( ".working", brainpress_upgrade_holder ).detach();
					brainpress_upgrade_holder.append( data.message );
					if ( "undefined" == typeof( data.course_id ) || "stop" == data.course_id ) {
						brainpress_upgrade_holder.append( brainpress_upgrade_form.data('label-done') );
						return false;
					} else {
						brainpress_upgrade_course( data.course_id, data.section, input_data );
					}
				}
			}).fail( function( data ) {
				brainpress_upgrade_holder.append( brainpress_upgrade_form.data('label-fail') );
			})
		}
	});
}));
