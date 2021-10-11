<?php
/**
 * Add sample courses whenever BrainPress is activated if no existing courses.
 *
 * @class BrainPress_Admin_SampleCourses
 * @since 2.0.7
 **/
class BrainPress_Admin_SampleCourses {
	static function add_sample_courses() {
		if ( ! self::has_courses() ) {
			self::add_courses();
		}

		add_option( 'brainpress_maybe_redirect', true );

	}

	/**
	 * Check if current install have previously created courses.
	 *
	 * @return bool			Returns true if there are courses found otherwise false.
	 **/
	static function has_courses() {
		$args = array(
			'post_type' => BrainPress_Data_Course::get_post_type_name(),
			'post_status' => 'any'
		);
		$courses = get_posts( $args );

		return count( $courses ) > 0;
	}

	/**
	 * Insert sample courses into DB.
	 *
	 * @return null
	 **/
	static function add_courses() {
		$filename = BrainPress::$path .'asset/file/sample-course.json';

		try {
			if ( is_readable( $filename ) ) {
				$file_content = file_get_contents( $filename );
				$courses = json_decode( $file_content );
				BrainPress_Admin_Import::course_importer( $courses, 0, true, false, false );
			}
		} catch( Exception $e ) {
			// Do nothing, it log the error when DEBUG is on
		}
	}
}