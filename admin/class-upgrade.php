<?php
/**
 * Course Certificates Page
 * Display and manages the generated certificates.
 **/
class BrainPress_Admin_Upgrade extends BrainPress_Admin_Controller_Menu {

	var $parent_slug = 'brainpress';
	var $slug = 'brainpress_upgrade';
	protected $cap = 'brainpress_settings_cap';

	public function init() {
		$brainpress_courses_need_update = get_option( 'brainpress_courses_need_update', false );
		if ( 'yes' == $brainpress_courses_need_update ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		}
	}

	public function get_labels() {
		$brainpress_courses_need_update = get_option( 'brainpress_courses_need_update', 'no' );
		if ( 'yes' == $brainpress_courses_need_update ) {
			$this->init();
			return array(
				'title' => __( 'BrainPress Upgrade', 'brainpress' ),
				'menu_title' => __( 'Upgrade', 'brainpress' ),
			);
		}
		return array();
	}

	/**
	 * Enqueue script, but only on upgrade page.
	 */
	public function admin_enqueue_scripts() {
		$screen = get_current_screen();
		$re = sprintf( '/_page_%s$/', 'brainpress_upgrade' );
		if ( ! preg_match( $re, $screen->id ) ) {
			return;
		}
		$script = BrainPress::$url . 'asset/js/admin-upgrade.js';
		wp_enqueue_script( 'brainpress_admin_upgrade_js', $script, array( 'jquery' ), BrainPress::$version, true );
	}
}
