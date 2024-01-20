<?php
/**
 * Reports table
 *
 * @package ClassicPress
 * @subpackage BrainPress
 **/
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}
class BrainPress_Admin_Table_Reports extends WP_List_Table {
	var $courses = array();
	var $course_id = 0;
	var $last_student_progress = array();
	var $is_cache_path_writable = false;

	/** Class constructor */
	public function __construct() {

		parent::__construct( array(
			'singular' => __( 'Bericht', 'brainpress' ),
			'plural' => __( 'Berichte', 'brainpress' ),
			'ajax' => false,// should this table support ajax?
		) );

		$this->is_cache_path_writable = BrainPress_Helper_PDF::is_cache_path_writable();

		$this->courses = BrainPress_Data_Instructor::get_accessable_courses();

		if ( empty( $_REQUEST['course_id'] ) && ! empty( $this->courses ) ) {
			$this->course_id = $this->courses[0]->ID;
		} else {
			$this->course_id = (int) $_REQUEST['course_id'];
		}
	}

	public function prepare_items() {
		global $wpdb;

		$screen = get_current_screen();
		/**
		 * Per Page
		 */
		$option = $screen->get_option( 'per_page', 'option' );
		$per_page = (int) get_user_option( $option );
		if ( empty( $per_page ) || $per_page < 1 ) {
			$per_page = $this->get_option( 'per_page', 'default' );
			if ( ! $per_page ) {
				$per_page = 20;
			}
		}
		$per_page = $this->get_items_per_page( 'brainpress_reports_per_page', $per_page );

		$current_page = $this->get_pagenum();
		$offset = ( $current_page - 1 ) * $per_page;
		$s = isset( $_POST['s'] )? mb_strtolower( trim( $_POST['s'] ) ):false;

		if ( is_multisite() ) {
			$course_meta_key = $wpdb->prefix . 'enrolled_course_date_' . $this->course_id;
		} else {
			$course_meta_key = 'enrolled_course_date_' . $this->course_id;
		}

		$args = array(
			'meta_key' => $course_meta_key,
			'meta_compare' => 'EXISTS',
			'number' => $per_page,
			'offset' => $offset,
			'search' => $s,
		);

		$users = new WP_User_Query( $args );
		$this->items = $users->get_results();

		$total_items = $users->get_total();
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page' => $per_page,
				'course_id' => $this->course_id,
			)
		);
	}

	public function no_items() {
		_e( 'No students found.', 'brainpress' );
	}

	public function get_columns() {
		return array(
			'cb' => '<input type="checkbox" />',
			'id' => __( 'ID', 'brainpress' ),
			'name' => __( 'Studentenname', 'brainpress' ),
			'responses' => __( 'Antworten', 'brainpress' ),
			'average' => __( 'Durchschnittlich', 'brainpress' ),
			'report' => __( 'Download', 'brainpress' ),
			'html' => __( 'Ansehen', 'brainpress' ),
		);
	}

	public function get_bulk_actions() {
		$actions = array(
			'download' => __( 'Download', 'brainpress' ),
			'download_summary' => __( 'Zusammenfassung herunterladen', 'brainpress' ),
			'show' => __( 'Anzeigen', 'brainpress' ),
			'show_summary' => __( 'Zusammenfassung anzeigen', 'brainpress' ),
		);

		return $actions;
	}

	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="students[]" value="%s" />', $item->ID
		);
	}

	public function column_ID( $item ) {
		return $item->ID;
	}

	public function column_name( $item ) {
		$avatar = get_avatar( $item->user_email, 28 );
		$name = BrainPress_Helper_Utility::get_user_name( $item->ID, true );

		return $avatar . $name;
	}

	public function column_responses( $item ) {
		BrainPress_Data_Student::get_calculated_completion_data( $item->ID, $this->course_id );
		$this->last_student_progress = BrainPress_Data_Student::get_completion_data( $item->ID, $this->course_id );
		$responses = (int) BrainPress_Data_Student::count_course_responses( $item->ID, $this->course_id, $this->last_student_progress );

		return $responses;
	}

	public function column_average( $item ) {
		$average = BrainPress_Helper_Utility::get_array_val(
			$this->last_student_progress,
			'completion/average'
		);

		return (float) $average . '%';
	}

	protected function handle_row_actions( $item, $column_name, $primary ) {
		if ( 'name' !== $column_name ) {
			return '';
		}
	}

	public function column_report( $item ) {
		if ( true === $this->is_cache_path_writable ) {
			$download_url = add_query_arg(
				array(
					'student_id' => $item->ID,
					'course_id' => $this->course_id,
					'_wpnonce' => wp_create_nonce( 'brainpress_download_report' ),
				)
			);
			return sprintf(
				'<a href="%s" data-student="%d" data-course="%d"><i class="fa fa-file-pdf-o" aria-hidden="true"></i>&nbsp;</a>',
				esc_url( $download_url ),
				esc_attr( $item->ID ),
				esc_attr( $this->course_id )
			);
		}
		return sprintf( '<span title="%s" data-click="false"></span>', esc_attr__( 'Wir können keine PDF-Dateien erstellen. Das Cache-Verzeichnis ist nicht beschreibbar.', 'brainpress' ) );
	}

	/**
	 * Preview Report in HTML
	 *
	 * @since 2.0.5
	 */
	public function column_html( $item ) {
		$download_url = add_query_arg(
			array(
				'student_id' => $item->ID,
				'course_id' => $this->course_id,
				'mode' => 'html',
				'_wpnonce' => wp_create_nonce( 'brainpress_download_report' ),
			)
		);
		return sprintf(
			'<a href="%s" data-student="%d" data-course="%d"><i class="fa fa-file-text-o" aria-hidden="true"></i>&nbsp;</a>',
			esc_url( $download_url ),
			esc_attr( $item->ID ),
			esc_attr( $this->course_id )
		);
	}

	public function extra_tablenav( $which ) {
		if ( 'top' !== $which ) {
			return;
		}

		$options = array();
		$options['value'] = $this->course_id;
		$options['class'] = 'medium dropdown';
		$options['placeholder'] = __( 'Kurs auswählen', 'brainpress' );
		$courses = BrainPress_Helper_UI::get_course_dropdown( 'course_id', 'course_id', $this->courses, $options );
		?>
		<div class="alignleft course-filter">
			<?php echo $courses; ?>
			<input type="submit" class="button action" name="action" value="<?php esc_attr_e( 'Filter', 'brainpress' ); ?>" />
		</div>
		<?php
		$this->search_box( __( 'Suche', 'brainpress' ), 'search_students' );
	}

	public function pagination( $which ) {
		if ( 'top' !== $which ) {
			return parent::pagination( $which );
		}
	}
}
