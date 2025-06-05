<?php
/**
 * Initialize the premium functions of the CoursePress plugin.
 * File is included directly from main plugin file.
 *
 * @see coursepress.php
 * @package CoursePressPro
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

// Launch CoursePressPro.
CoursePressPro::init();

/**
 * Main entry to hook up and initialize premium features.
 *
 * @since 2.0.0
 */
class CoursePressPro {

	/**
	 * Absolut path to this file (main plugin file).
	 *
	 * @var string
	 */
	public static $file = '';

	/**
	 * File-root of the premium files.
	 *
	 * @var string
	 */
	public static $path = '';

	/**
	 * Dir-name of this plugin (relative to wp-content/plugins).
	 *
	 * @var string
	 */
	public static $dir = '';

	/**
	 * Absolute URL to plugin folder.
	 *
	 * @var string
	 */
	public static $url = '';

	/**
	 * Initialize the premium features!
	 * This function runs before the `plugins_loaded` action.
	 *
	 * @since  2.0.0
	 */
	public static function init() {
		define( 'CP_IS_PREMIUM', true );

		// Overwrite settings for premium files.
		self::$file = __FILE__;
		self::$path = plugin_dir_path( __FILE__ );
		self::$dir = dirname( self::$path );
		self::$url = plugin_dir_url( __FILE__ );

		// Here comes the actual premium code! Yay :)
		add_action(
			'plugins_loaded',
			array( 'CoursePressPro_Core', 'init' ),
			11
		);

		// This class should be moved to a different plugin (i.e. wp-academy)!
		add_action(
			'plugins_loaded',
			array( 'CoursePressPro_Wpmudev', 'init' ),
			11
		);

	}
}