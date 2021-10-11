<?php

/**
 * course import
 *
 * @since 2.0.6
 */


class BrainPress_Helper_Course_Import {

	/**
	 * Import course.
	 *
	 * @since 2.0.6
	 */
	public static function import_sample_course() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}
		$filename = BrainPress::$path.'asset/file/sample-course.json';
		if ( is_readable( $filename ) ) {
			$file_content = file_get_contents( $filename );
			$courses = json_decode( $file_content );
			BrainPress_Admin_Import::course_importer( $courses, 0, true, false, false );
		}
	}
}
