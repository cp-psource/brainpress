<?php
/**
 * Students Table
 *
 * This class extends WP_Users_List_Table to manage courses students.
 *
 * @package ClassicPress
 * @subpackage BrainPress
 **/
class BrainPress_Admin_Table_Students extends BrainPress_Admin_Table_Instructors {
	static $student_progress = array();
	private $courses;

	public function prepare_items() {

		global $wpdb;
		/**
		 * Search
		 */
		$usersearch = isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST['s'] ) ) : '';
		/**
		 * Per Page
		 */
		$per_page = $this->get_per_page();
		$per_page = $this->get_items_per_page( 'brainpress_students_per_page', $per_page );
		/**
		 * pagination
		 */
		$current_page = $this->get_pagenum();
		$offset = ( $current_page - 1 ) * $per_page;
		/**
		 * Query args
		 */

		$usersearch = isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST['s'] ) ) : '';

		$paged = $this->get_pagenum();
		$role = 'role';

		if ( is_multisite() ) {
			$role = $wpdb->prefix . $role;
		}

		$args = array(
			'number' => $per_page,
			'offset' => $offset,
			'meta_key' => $role,
			'meta_value' => 'student',
			'fields' => 'all_with_meta',
			'search' => $usersearch,
		);

		if ( is_multisite() ) {
			$args['blog_id'] = get_current_blog_id();
		}

		if ( ! empty( $_GET['course_id'] ) ) {
			// Show only students of current course
			$this->course_id = (int) $_GET['course_id'];
			$student_ids = BrainPress_Data_Course::get_student_ids( $this->course_id );
			if ( empty( $student_ids ) ) {
				return;
			}
			$args['include'] = $student_ids;
		}

		if ( '' !== $args['search'] ) {
			$args['search'] = '*' . $args['search'] . '*';
		}

		if ( $this->is_site_users ) {
			$args['blog_id'] = $this->site_id;
		}

		// Query the user IDs for this page
		$wp_user_search = new WP_User_Query( $args );

		$students = $wp_user_search->get_results();
		foreach ( $students as $user_id => $student ) {
			$student->count_enrolled_courses = BrainPress_Data_Student::count_enrolled_courses_ids( $user_id, true );
			$this->items[ $user_id ] = $student;
		}
		$this->set_pagination_args( array(
			'total_items' => $wp_user_search->get_total(),
			'per_page' => $per_page,
		) );
	}

	public function get_columns() {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'user_id' => __( 'ID', 'brainpress' ),
			'student_name' => __( 'Name', 'brainpress' ),
			'last_login' => __( 'Letzter Login', 'brainpress' ),
			'courses' => __( 'Anzahl Kurse', 'brainpress' ),
			'courses_list' => __( 'Kurse', 'brainpress' ),
		);

		if ( ! empty( $this->course_id ) ) {
			unset( $columns['courses'] );
			unset( $columns['courses_list'] );
			$columns['average'] = __( 'Durchschnittlich', 'brainpress' );
			$columns['status'] = __( 'Status', 'brainpress' );
		}

		return $columns;
	}

	public function get_bulk_actions() {
		$actions = array();

		// @todo: add sanity check
		$actions['withdraw'] = __( 'Zurückziehen', 'brainpress' );

		return $actions;
	}

	protected function pagination( $which ) {
		// Show pagination only at the bottom
		if ( 'top' !== $which ) {
			parent::pagination( $which );
		} else {
			$this->search_box( __( 'Suche Studenten', 'brainpress' ), 'search' );
		}
	}

	public function column_user_id( $user_id ) {
		return $user_id;
	}

	public function column_courses( $user_id ) {
		$profile_link = BrainPress_Data_Student::get_admin_profile_url( $user_id );
		return sprintf( '<a href="%s">%s</a>', $profile_link, $this->items[ $user_id ]->count_enrolled_courses );
	}

	public function column_student_name( $user_id ) {
		$actions = array();
		$user = get_userdata( $user_id );
		$actions['user_id'] = sprintf( __( 'Benutzer ID: %d', 'brainpress' ), $user_id );
		// User avatar
		$avatar = get_avatar( $user->user_email, 32 );
		$name = BrainPress_Helper_Utility::get_user_name( $user_id, true );
		// Generate row actions
		$url = remove_query_arg(
			array(
				'view',
				'_wpnonce',
				'nonce',
				'student_id',
			)
		);
		// @todo: Add sanity check/validation
		$profile_link = BrainPress_Data_Student::get_admin_profile_url( $user_id );
		$actions['courses'] = sprintf( '<a href="%s">%s</a>', esc_url( $profile_link ), __( 'Profil ansehen', 'brainpress' ) );

		/**
		 * Withdraw
		 */
		if ( 0 !== $this->items[ $user_id ]->count_enrolled_courses ) {
			$action = 'remove_student';
			$nonce_action = BrainPress_Data_Student::get_nonce_action( $action, $user_id );
			$args = array(
				'_wpnonce' => wp_create_nonce( $nonce_action ),
				'student_id' => $user_id,
				'action' => $action,
				'course_id' => 'all',
			);
			$withdraw_title = __( 'Ziehe Dich aus allen Kursen zurück', 'brainpress' );
			/**
			 * Add course data if list if filtered by Course.
			 */
			if ( ! empty( $this->course_id ) ) {
				$withdraw_title = __( 'Zurückziehen', 'brainpress' );
				$args['course_id'] = $this->course_id;
			}
			$delete_url = add_query_arg( $args );
			$actions['delete'] = sprintf( '<a href="%s">%s</a>', esc_url( $delete_url ), $withdraw_title );
		}
		return $avatar . $name . $this->row_actions( $actions );
	}

	public function column_last_login( $user_id ) {
		$date_format = get_option( 'date_format' );
		$time_format = get_option( 'time_format' );
		$last_activity = get_user_meta( $user_id, 'latest_activity', true );

		if ( empty( $last_activity ) ) {
			$return = '-';
		} else {
			$return = date_i18n( $date_format . ' ' . $time_format, BrainPress_Data_Course::strtotime( $last_activity ) );
		}

		return $return;
	}

	public function column_average( $user_id ) {
		if ( empty( self::$student_progress ) ) {
			self::$student_progress = BrainPress_Data_Student::get_completion_data( $user_id, $this->course_id );
		}
		$average = BrainPress_Helper_Utility::get_array_val(
			self::$student_progress,
			'completion/average'
		);

		return (float) $average . '%';
	}

	public function column_status( $user_id ) {
		$status = BrainPress_Data_Student::get_course_status( $this->course_id );

		return $status;
	}

	/**
	 * Show courses list.
	 *
	 * @since 2.0.0
	 *
	 * @param integer $user_id Current row user ID.
	 * @return string List of courses or information about nothing.
	 */
	public function column_courses_list( $user_id ) {
		$courses_ids = BrainPress_Data_Student::get_enrolled_courses_ids( $user_id );
		if ( empty( $courses_ids ) ) {
			return sprintf(
				'<span aria-hidden="true">&#8212;</span><span class="screen-reader-text">%s</span>',
				__( 'Der Student ist für keinen Kurs eingeschrieben.', 'brainpress' )
			);
		}
		$content = '<ul>';
		foreach ( $courses_ids as $course_id ) {
			if ( ! isset( $this->courses[ $course_id ] ) ) {
				$this->courses[ $course_id ] = array(
					'title' => get_the_title( $course_id ),
					'link' => add_query_arg(
						array(
							'post_type' => BrainPress_Data_Course::get_post_type_name(),
							'page' => 'brainpress_students',
							'course_id' => $course_id,
						),
						admin_url( 'edit.php' )
					),
				);
			}
			$content .= sprintf(
				'<li><a href="%s">%s</a></li>',
				esc_url( $this->courses[ $course_id ]['link'] ),
				$this->courses[ $course_id ]['title']
			);
		}
		$content .= '</ul>';
		return $content;
	}
}
