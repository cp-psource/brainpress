<?php

/**
 * Grades
 *
 * @since 2.0.5
 */

class BrainPress_Template_Grades {
	/**
	 * Render page.
	 *
	 * @since 2.0.5
	 */
	public static function render() {
		$course = BrainPress_Helper_Utility::the_course();
		$course_id = $course->ID;
		$content = '';
		$content .= do_shortcode( '[course_unit_submenu]' );
		$content .= '<div class="cp-student-grades">';
		/**
		 * table
		 */
		$content .= do_shortcode( '[student_grades_table]' );
		/**
		 * Total
		 */
		$content .= '<div class="total_grade">';
		$shortcode = sprintf( '[course_progress course_id="%d"]', $course_id );
		$content .= apply_filters( 'brainpress_grade_caption', __( 'Gesamt:', 'brainpress' ) );
		$content .= ' ';
		$content .= do_shortcode( $shortcode );
		$content .= '%</div>';
		$content .= '</div>';
		return $content;
	}
}
