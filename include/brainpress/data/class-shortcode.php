<?php
/**
 * Shortcode functions.
 *
 * @package BrainPress
 */

/**
 * Initializes BrainPress shortcodes.
 */
class BrainPress_Data_Shortcode {

	/**
	 * Load the individual shortcode modules.
	 * For better maintenance and performance the shortcodes are split into
	 * multiple files instead of having one huge file.
	 *
	 * @since  2.0.0
	 */
	public static function init() {
		BrainPress_Data_Shortcode_Course::init();
		BrainPress_Data_Shortcode_CourseTemplate::init();
		BrainPress_Data_Shortcode_Instructor::init();
		BrainPress_Data_Shortcode_Student::init();
		BrainPress_Data_Shortcode_Template::init();
		BrainPress_Data_Shortcode_Unit::init();
	}
}