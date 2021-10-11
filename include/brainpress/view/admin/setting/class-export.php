<?php
class BrainPress_View_Admin_Setting_Export extends BrainPress_View_Admin_Setting_SettingPage {
	var $slug = 'export';

	public static function init() {
		self::$_instance = new self;
	}

	public static function add_tabs( $tabs ) {
		$tabs['export'] = array(
			'title' => __( 'Exportiere Kurse', 'brainpress' ),
			'description' => __( 'Exportiere Kurse', 'brainpress' ),
			'order' => 30,
		);

		return $tabs;
	}
}