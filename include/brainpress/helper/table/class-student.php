<?php
if ( ! class_exists( 'WP_Users_List_Table' ) ) {
	require ABSPATH . 'wp-admin/includes/class-wp-users-list-table.php';
}

class BrainPress_Helper_Table_Student extends WP_Users_List_Table {
	public function prepare_items() {
		// Remove possible query injections
		remove_all_filters( 'users_list_table_query_args' );
		remove_all_filters( 'get_role_list' );
		remove_all_filters( 'pre_user_query' );

		add_filter( 'manage_users_custom_column', array( __CLASS__, 'custom_columns' ), 10, 3 );

		add_filter( 'users_list_table_query_args', array( __CLASS__, 'filter_args' ) );
		add_filter( 'user_row_actions', array( __CLASS__, 'user_row_actions' ), 10, 2 );

		self::delete_student();
		parent::prepare_items();
	}

	public static function filter_args( $args ) {
		$args['meta_value'] = 'student';
		$args['meta_key'] = 'role';

		return $args;
	}

	public static function user_row_actions( $actions, $user_object ) {
		$profile_link = add_query_arg(
			array( 'view' => 'profile', 'student_id' => $user_object->ID )
		);
		$workbook_link = add_query_arg(
			array( 'view' => 'workbook', 'student_id' => $user_object->ID )
		);
		$delete_link = add_query_arg(
			array(
				'student_id' => $user_object->ID,
				'nonce' => wp_create_nonce( 'brainpress_remove_student' ),
				)
		);
		$actions = array(
			'profile' => sprintf( '<a href="%s">%s</a>', $profile_link, __( 'Profil', 'brainpress' ) ),
			'workbook' => sprintf( '<a href="%s">%s</a>', $workbook_link, __( 'Arbeitsmappe', 'brainpress' ) ),
			'delete' => sprintf( '<a href="%s">%s</a>', $delete_link, __( 'Entfernen', 'brainpress' ) ),
		);

		return $actions;
	}

	/**
	 * Withdraw student to all courses
	 **/
	public static function delete_student() {
		if ( empty( $_GET['nonce'] ) ) { return; }
		if ( ! wp_verify_nonce( $_GET['nonce'], 'brainpress_remove_student' ) ) { return; }
		if ( ! isset( $_GET['student_id'] ) ) { return; }

		$student_id = (int) $_GET['student_id'];
		$courses = BrainPress_Data_Student::get_enrolled_courses_ids( $student_id );

		foreach ( $courses as $course_id ) {
			BrainPress_Data_Course::withdraw_student( $student_id, $course_id );
		}

		// Return to student's list.
		$return_url = remove_query_arg(
			array(
				'view',
				'student_id',
				'nonce',
			)
		);
		wp_safe_redirect( $return_url );
		exit;
	}

	public function get_columns() {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'username' => __( 'Benutzername', 'brainpress' ),
			'name' => __( 'Name', 'brainpress' ),
			'registered' => __( 'Registriert', 'brainpress' ),
			'last_activity' => __( 'Letzte Aktivität', 'brainpress' ),
			'courses' => __( 'Kurse', 'brainpress' ),
		);

		if ( ! BrainPress_Data_Capabilities::can_delete_student() ) {
			unset( $columns['remove'] );
		}
		return $columns;
	}

	public static function custom_columns( $null, $column, $user_id ) {
		$student = get_userdata( $user_id );
		$date_format = get_option( 'date_format' );
		$time_format = get_option( 'time_format' );
		$return = '';

		switch ( $column ) {
			case 'id':
				$return = $user_id;
				break;

			case 'registered':
				$return = date_i18n( $date_format, BrainPress_Data_Course::strtotime( $student->user_registered ) );
				break;

			case 'last_activity':
				$last_activity = get_user_meta( $user_id, 'latest_activity', true );
				$last_activity_kind = get_user_meta( $user_id, 'latest_activity_kind', true );
				if ( empty( $last_activity ) ) {
					$last_activity = get_user_meta( $user_id, 'last_login', true );
					if ( ! empty( $last_activity ) ) {
						$last_activity = $last_activity['time'];
					}
					$last_activity_kind = 'login';
				}
				if ( empty( $last_activity ) ) {
					return sprintf( '<small>%s</small>', __( '[niemals]', 'brainpress' ) );
				}
				$return = date_i18n( $date_format . ' ' . $time_format, BrainPress_Data_Course::strtotime( $last_activity ) );
				$return .= '<br /><small>';

				switch ( $last_activity_kind ) {
					case 'course_module_seen':
						$return .= __( 'Kursmodul gesehen.', 'brainpress' );
					break;
					case 'course_seen':
						$return .= __( 'Kurs gesehen', 'brainpress' );
					break;
					case 'course_unit_seen':
						$return .= __( 'Kurseinheit gesehen.', 'brainpress' );
					break;
					case 'enrolled':
						$return .= __( 'Benutzer hat sich für einen Kurs eingeschrieben.', 'brainpress' );
					break;
					case 'login':
						$return .= __( 'Benutzer hat sich angemeldet.', 'brainpress' );
					break;
					case 'module_answered':
						$return .= __( 'Beantwortete ein Modul.', 'brainpress' );
					break;
					default:
						$return .= __( 'Unbekannte Stundenaktion.', 'brainpress' );
					break;
				}
				$return .= '</small>';
				break;

			case 'courses':
				$courses = BrainPress_Data_Student::count_enrolled_courses_ids( $user_id );
				$profile_link = add_query_arg(
					array( 'view' => 'profile', 'student_id' => $user_id )
				);
				$return = sprintf( '<a href="%s">%s</a>', $profile_link, $courses );
				break;

		}

		return $return;
	}

	public function extra_tablenav( $which ) {
		// Do nothing...
	}

	public function no_items() {
		esc_html_e( 'Keine Studenten gefunden.', 'brainpress' );
	}

	public function display() {
		?>
		<div class="wrap">
			<h2>
				<?php
				esc_html_e( 'Studenten', 'brainpress' );
				if ( BrainPress_Data_Capabilities::can_create_student() ) {
					$add_link = admin_url( 'user-new.php' );
					?>
					<a href="<?php echo $add_link; ?>" class="add-new-h2">
						<?php esc_html_e( 'Neuen Studenten hinzufügen', 'brainpress' ); ?>
					</a>
				<?php
				}
				?>
			</h2>
			<hr />
			<form method="post">
				<?php
					$this->search_box( __( 'Suche', 'brainpress' ), 'student' );
					parent::display();
				?>
			</form>
		</div>
		<?php
	}
}
