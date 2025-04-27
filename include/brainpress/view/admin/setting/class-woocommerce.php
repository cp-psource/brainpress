<?php
/**
 * Admin view.
 *
 * @package BrainPress
 */

/**
 * Settings for Basic Certificate.
 */
class BrainPress_View_Admin_Setting_WooCommerce {

	public static function init() {
		if ( ! BrainPress_Helper_Integration_WooCommerce::is_active() ) {
			return;
		}

		add_filter(
			'brainpress_settings_tabs',
			array( __CLASS__, 'add_tabs' )
		);
		add_action(
			'brainpress_settings_process_woocommerce',
			array( __CLASS__, 'process_form' ),
			10, 2
		);
		add_filter(
			'brainpress_settings_render_tab_woocommerce',
			array( __CLASS__, 'return_content' ),
			10, 3
		);
		add_action(
			'brainpress_general_options_page',
			array( __CLASS__, 'add_woocommerce_general_option' )
		);
	}

	public static function add_tabs( $tabs ) {
		$tabs['woocommerce'] = array(
			'title' => __( 'WooCommerce', 'brainpress' ),
			'description' => __( 'Ermöglicht die Integration von WooCommerce, um Kurse zu verkaufen.', 'brainpress' ),
			'order' => 69,
		);

		return $tabs;
	}

	public static function return_content( $content, $slug, $tab ) {
		$is_enabled = BrainPress_Core::get_setting( 'woocommerce/enabled', false );
		$use_redirect = BrainPress_Core::get_setting( 'woocommerce/redirect', false );
		$unpaid = BrainPress_Core::get_setting( 'woocommerce/unpaid', 'change_status' );
		$delete = BrainPress_Core::get_setting( 'woocommerce/delete', 'change_status' );

		ob_start();
		?>
		<input type="hidden" name="page" value="<?php echo esc_attr( $slug ); ?>" />
		<input type="hidden" name="tab" value="<?php echo esc_attr( $tab ); ?>" />
		<input type="hidden" name="action" value="updateoptions" />
		<?php wp_nonce_field( 'update-brainpress-options', '_wpnonce' ); ?>

			<table class="form-table compressed">
				<tbody>
					<tr>
						<td><label>
							<input type="checkbox"
								<?php checked( cp_is_true( $is_enabled ) ); ?>
								name="brainpress_settings[woocommerce][enabled]"
								class="certificate_enabled"
								value="1" />
							<?php esc_html_e( 'Verwende WooCommerce, um Kurse zu verkaufen', 'brainpress' ); ?>
						</label>
						<p class="description"><?php _e( 'Wenn diese Option aktiviert ist, wird WooCommerce anstelle von MarketPress für den Verkauf von Kursen verwendet', 'brainpress' ) ?></p>
						</td>
					</tr>
					<tr>
						<td><label>
							<input type="checkbox"
								<?php checked( cp_is_true( $use_redirect ) ); ?>
								name="brainpress_settings[woocommerce][redirect]"
								class="certificate_enabled"
								value="1" />
							<?php esc_html_e( 'Leite den WooCommerce-Produktbeitrag zu einem übergeordneten Kursbeitrag um', 'brainpress' ); ?>
						</label>
							<p class="description"><?php _e( 'Wenn diese Option aktiviert ist, werden Besucher, die versuchen, auf einen einzelnen Beitrag von WooCommerce zuzugreifen, automatisch zu einem einzelnen Beitrag eines übergeordneten Kurses weitergeleitet.', 'brainpress' ) ?></p>
						</td>
					</tr>
					<tr>
						<td>
							<h3><?php esc_html_e( 'Wenn der Kurs unbezahlt ist, dann:', 'brainpress' ); ?></h3>
							<ul>
								<li><label><input type="radio"
									<?php checked( $unpaid, 'change_status' ); ?>
									name="brainpress_settings[woocommerce][unpaid]"
									class="certificate_enabled"
									value="change_status" /> <?php esc_html_e( 'Wechsle zum Entwurf eines verwandten WooCommerce-Produkts.', 'brainpress' ); ?></label></li>
								<li><label><input type="radio"
									<?php checked( $unpaid, 'delete' ); ?>
									name="brainpress_settings[woocommerce][unpaid]"
									class="certificate_enabled"
									value="delete" /> <?php esc_html_e( 'Lösche das zugehörige WooCommerce-Produkt.', 'brainpress' ); ?></label></li>
							</ul>
						</td>
					</tr>
					<tr>
						<td>
							<h3><?php esc_html_e( 'Wenn der Kurs gelöscht ist, dann:', 'brainpress' ); ?></h3>
							<ul>
								<li><label><input type="radio"
									<?php checked( $delete, 'change_status' ); ?>
									name="brainpress_settings[woocommerce][delete]"
									class="certificate_enabled"
									value="change_status" /> <?php esc_html_e( 'Wechsle zum Entwurf eines verwandten WooCommerce-Produkts.', 'brainpress' ); ?></label></li>
								<li><label><input type="radio"
									<?php checked( $delete, 'delete' ); ?>
									name="brainpress_settings[woocommerce][delete]"
									class="certificate_enabled"
									value="delete" /> <?php esc_html_e( 'Lösche das zugehörige WooCommerce-Produkt.', 'brainpress' ); ?></label></li>
							</ul>
						</td>
					</tr>
				</tbody>
				</tbody>
			</table>
		<?php
		$content = ob_get_clean();

		return $content;
	}

	public static function process_form( $page, $tab ) {
		// First verify nonce before checking any other POST value.
		if ( ! isset( $_POST['_wpnonce'] ) ) { return; }
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'update-brainpress-options' ) ) { return; }

		if ( 'woocommerce' != $tab ) { return; }
		if ( ! isset( $_POST['action'] ) ) { return; }
		if ( 'updateoptions' != $_POST['action'] ) { return; }

		$settings = BrainPress_Core::get_setting( false ); // false: Get all settings.

		$post_settings = array(
			'woocommerce' => array(
				'enabled' => false,
				'redirect' => false,
				'unpaid' => 'change_status',
				'delete' => 'change_status',
			),
		);

		/**
		 * check data and if exists, then update
		 */
		if (
			isset( $_POST['brainpress_settings'] )
			&& is_array( $_POST['brainpress_settings'] )
			&& isset( $_POST['brainpress_settings']['woocommerce'] )
			&& is_array( $_POST['brainpress_settings']['woocommerce'] )
		) {
			foreach ( $post_settings['woocommerce'] as $key => $value ) {
				if ( isset( $_POST['brainpress_settings']['woocommerce'][ $key ] ) ) {
					$post_settings['woocommerce'][ $key ] = true;
				}
			}
			if (
				isset( $_POST['brainpress_settings']['woocommerce']['unpaid'] )
				&& 'delete' == $_POST['brainpress_settings']['woocommerce']['unpaid']
			) {
				$post_settings['woocommerce']['unpaid'] = 'delete';
			} else {
				$post_settings['woocommerce']['unpaid'] = 'change_status';
			}
			if (
				isset( $_POST['brainpress_settings']['woocommerce']['delete'] )
				&& 'delete' == $_POST['brainpress_settings']['woocommerce']['delete']
			) {
				$post_settings['woocommerce']['delete'] = 'delete';
			} else {
				$post_settings['woocommerce']['delete'] = 'change_status';
			}
		}
		$post_settings = BrainPress_Helper_Utility::sanitize_recursive( $post_settings );
		// Don't replace settings if there is nothing to replace.
		if ( ! empty( $post_settings ) ) {
			BrainPress_Core::update_setting(
				false, // False will replace all settings.
				BrainPress_Core::merge_settings( $settings, $post_settings )
			);
		}
	}
}
