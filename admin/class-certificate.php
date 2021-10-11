<?php
/**
 * Course Certificates Page
 * Display and manages the generated certificates.
 **/
class BrainPress_Admin_Certificate extends BrainPress_Admin_Controller_Menu {
	var $parent_slug = 'brainpress';
	var $slug = 'brainpress_certificate';
	protected $cap = 'brainpress_settings_cap';

	public function __construct() {
		/*
		parent::__construct();

		// Temp solution
		// @todo: Replace this with actual certificates pages under Brainpress
		add_action( 'init', array( $this, 're_register_certificates' ) );

		// Let's temporarily enable certificate caps
		if ( current_user_can( 'brainpress_settings_cap' ) ) {
			add_filter( 'user_has_cap', array( $this, 'add_caps' ), 100, 3 );
		}
		*/
	}

	public function get_labels() {
		return array(
			'title' => __( 'BrainPress Zertifikate', 'brainpress' ),
			'menu_title' => __( 'Zertifikate', 'brainpress' ),
		);
	}

	public function re_register_certificates() {
		register_post_type(
			BrainPress_Data_Certificate::get_post_type_name(),
			array(
				'public' => false,
				'show_ui' => true,
				'capability_type' => 'certificate',
				'map_meta_cap' => null,
				'label' => __( 'Zertifikate', 'brainpress' ),
			)
		);
	}

	public function add_caps( $allcaps, $cap, $args ) {
		// Let's add certificate specific caps
		$allcaps += array(
			'read_certificates' => 1,
			'read_private_certificates' => 1,
			'edit_certificate' => 1,
			'edit_certificates' => 1,
			'edit_published_certificates' => 1,
			'edit_published_certificate' => 1,
			'edit_private_certificates' => 1,
			'edit_private_certificate' => 1,
			'delete_certificates' => 1,
			'delete_certificate' => 1,
			'delete_private_certificates' => 1,
			'delete_private_certificate' => 1,
		);

		return $allcaps;
	}
}
