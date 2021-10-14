<?php

class BrainPress_Helper_Extension_PSeCommerce {

	private static $installed = false;

	private static $activated = false;

	private static $base_path = array(
		'pro' => 'psecommerce/psecommerce.php',
		'free' => 'psecommerce/psecommerce.php',
	);

	public static function init() {

		if ( CP_IS_CAMPUS ) {
			return false;
		}

		add_filter( 'brainpress_extensions_plugins', array( __CLASS__, 'add_to_extensions_list' ) );
	}

	public static function add_to_extensions_list( $plugins ) {
		$download_source = 'n3rds.work\/wp-update-server\/?action=download&slug=psecommerce';
		$external_url = 'https://n3rds.work/piestingtal_source/psecommerce-shopsystem/';
		$source_message = __( 'PSource Server', 'brainpress' );
		$is_link = false;
		$base_path = self::$base_path['free'];

		if ( defined( 'CP_IS_PREMIUM' ) && CP_IS_PREMIUM ) {
			/**
			 * Redirect to WPMUDEV Dashboard page
			 */
			$source_message = 'PSource Server';
			$external_url = '';
			$is_link = true;
			$download_source = 'n3rds.work\/wp-update-server\/?action=download&slug=psecommerce';
			$base_path = self::$base_path['pro'];

			if ( is_plugin_active( 'psource-updates/update-notifications.php' ) ) {
				$download_source = add_query_arg( 'page', 'psource', admin_url( 'admin.php' ) );

				
			}
		}

		$plugins[] = array(
			'name' => 'PSeCommerce',
			'slug' => 'psecommerce',
			'base_path' => $base_path,
			'source' => $download_source,
			'source_message' => $source_message,
			'external_url' => $external_url,
			'external' => 'yes',
			'protocol' => 'https',
			'is_link' => $is_link,
		);

		return $plugins;
	}


	public static function installed_scope() {
		$scope = '';

		foreach ( self::$base_path as $key => $path ) {
			$plugin_dir = WP_PLUGIN_DIR . '/' . $path;
			$plugin_mu_dir = WP_CONTENT_DIR . '/mu-plugins/' . $path;
			$location = file_exists( $plugin_dir ) ? trailingslashit( WP_PLUGIN_DIR ) : ( file_exists( $plugin_mu_dir ) ?  WP_CONTENT_DIR . '/mu-plugins/' : '' ) ;
			$scope = ! empty( $location ) ? $key : $scope;
		}

		return $scope;
	}

	public static function installed() {

		$scope = self::installed_scope();
		return ! empty( $scope );

	}

	public static function activated() {

		$scope = self::installed_scope();

		require_once ABSPATH . 'wp-admin/includes/plugin.php'; // Need for plugins_api.

		return ! empty( $scope ) ? is_plugin_active( self::$base_path[ $scope ] ) : false;
	}

	/**
	 * Show MP install/activation notice
	 **/
	public static function mp_notice() {
		/**
		 * check screen
		 */
		$post_type = BrainPress_Data_Course::get_post_type_name();
		$screen = get_current_screen();
		if ( ! isset( $screen->post_type ) || $post_type != $screen->post_type ) {
			return;
		}
		/**
		 * check user meta
		 */
			$user_id = get_current_user_id();
		$show = get_user_option( 'psecommerce-run-notice' );
		if ( 'hide' == $show ) {
			return;
        }
        /**
         * Do not show message, when user already use WooCommerce.
         */
        if ( BrainPress_Helper_Integration_WooCommerce::$is_active ) {
            $woocommerce_is_enabled = BrainPress_Core::get_setting( 'woocommerce/enabled', false );
            if ( $woocommerce_is_enabled ) {
                return;
            }
        }
		$message = '';
		if ( ! self::installed() ) {
			$mp_settings_url = add_query_arg( array(
				'post_type' => $post_type,
				'page' => 'brainpress_settings',
				'tab' => 'extensions',
				),
				admin_url( 'edit.php' )
			);
			$message = sprintf( '<strong>%s</strong> ', __( 'Installiere das PSeCommerce-Plugin, um Kurse zu verkaufen.', 'brainpress' ) );
			$message .= sprintf( '<a href="%s">%s</a>', $mp_settings_url, __( 'Installiere PSeCommerce', 'brainpress' ) );
		} elseif ( ! self::activated() ) {
			$mp_link = sprintf( '<a href="%s">%s</a>', admin_url( 'plugins.php' ), __( 'PSeCommerce', 'brainpress' ) );
			$message = sprintf( __( 'Aktiviere %s um mit den Verkauf von Kursen zu beginnen.', 'brainpress' ), $mp_link );
		} elseif ( self::activated() ) {
			if ( defined( 'MP_VERSION' ) ) {
				if ( version_compare( MP_VERSION, '1.5.2' ) < 0 ) {
					$plugin_url = admin_url( 'plugins.php' );
					$mp = sprintf( '<a href="%s">%s</a>', $plugin_url, '<strong>PSeCommerce</strong>' );
					$cp = defined( 'CP_IS_PREMIUM' ) && CP_IS_PREMIUM ? '<strong>BrainPress</strong>' : '<strong>BrainPress</strong>';
					$cp = sprintf( '<a href="%s">%s</a>', $plugin_url, $cp );
					$message = __( 'Wenn Du eine ältere Version des %s-Plugins verwendest, benötigst Du aus Kompatibilitätsgründen die neueste Version.', 'brainpress' );
					$message .= __( ' Aktualisiere jetzt Dein %s!', 'brainpress' );
					$message = sprintf( $message, $mp, $cp, $mp );
				}
			}
		}

		if ( ! empty( $message ) ) {
			$data = array(
				'dismissible' => true,
				'option-name' => 'psecommerce-run-notice',
				'nonce' => wp_create_nonce( 'psecommerce-run-notice'.$user_id ),
				'user_id' => $user_id,
			);
			echo BrainPress_Helper_UI::admin_notice( $message, 'warning', 'psecommerce-run-notice', $data );
		}
	}
}
