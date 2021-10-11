<?php
/**
 * BrainPress Comments
 *
 * This comments only works with CP export.
 *
 * @since 2.0
 **/
class BrainPress_Admin_Comments extends BrainPress_Admin_Controller_Menu {
	var $parent_slug = 'brainpress';
	var $slug = 'brainpress_comments';
	private static $start_time = 0;
	private static $current_time = 0;
	private static $time_limit_reached = false;
	protected $cap = 'brainpress_settings_cap';
	var $comments_list = null;

	public function get_labels() {
		return array(
			'title' => __( 'BrainPress Kommentare', 'brainpress' ),
			'menu_title' => __( 'Kommentare', 'brainpress' ),
		);
	}

	/**
	 * Process the commentsed courses
	 *
	 * @since 2.0
	 **/
	public function process_form() {
		$action = isset( $_REQUEST['action'] )? $_REQUEST['action']:'default';
		switch ( $action ) {
			case 'approvecomment':
			case 'unapprovecomment':
				if ( isset( $_REQUEST['c'] ) && isset( $_REQUEST['_wpnonce'] ) ) {
					$nonce = $_REQUEST['_wpnonce'];
					$nonce_action = sprintf( 'approve-comment_%d', $_REQUEST['c'] );
					if ( wp_verify_nonce( $nonce, $nonce_action ) ) {
						$commentarr = array(
						'comment_ID' => $_REQUEST['c'],
						'comment_approved' => 'approvecomment' == $action ? 1 : 0,
						);
						wp_update_comment( $commentarr );
					}
					$url = add_query_arg(
						array(
						'page' => $this->slug,
						'post_type' => BrainPress_Data_Course::get_post_type_name(),
						),
						admin_url( 'edit.php' )
					);
					wp_safe_redirect( $url );
					exit;
				}
			break;

			case 'editedcomment':
				if ( isset( $_POST['comment_ID'] ) ) {
					$commentarr = array(
					'comment_ID' => $_POST['comment_ID'],
					'comment_content' => $_POST['content'],
					'comment_approved' => $_POST['comment_status'],
					);
					wp_update_comment( $commentarr );
				}
				$this->slug = 'comment-edit';
			break;

			case 'editcomment':
				$this->slug = 'comment-edit';
			break;

			default:
				$this->comments_list = new BrainPress_Admin_Table_Comments;
				$this->comments_list->prepare_items();
				add_screen_option( 'per_page', array( 'default' => 20, 'option' => 'brainpress_comments_per_page', 'label' => __( 'Anzahl der Kommentare pro Seite:', 'brainpress' ) ) );
			break;
		}
	}
}
