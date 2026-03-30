<?php
/**
 * Plugin Name: Customize My Account Page For Woocommerce
 * Plugin URI: https://woocommerce.com/products/customize-my-account-page-for-woocommerce/
 * Description: Allows you to register custom WooCommerce tabs on my-account page and customize the design.
 * Author: ThemeGrill
 * Author URI: https://themegrill.com
 * Version: 2.1.9
 * Text Domain: customize-my-account-page-for-woocommerce
 * Domain Path: /languages
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Woo: 6142928:76e57b430b40fc0f2eaa53c78092333d
 * WC requires at least: 3.5.0
 * WC tested up to: 10.0.4
 */

use ThemeGrill\WoocommerceCustomizer\WoocommerceCustomizer;

defined( 'ABSPATH' ) || exit;

// Deactivate free plugin if it is installed.
if ( in_array( 'customize-my-account-lite-for-woocommerce/customize-my-account-lite-for-woocommerce.php', get_option( 'active_plugins', array() ), true ) ) {
	if ( function_exists( 'deactivate_plugins' ) ) {
		deactivate_plugins( 'customize-my-account-lite-for-woocommerce/customize-my-account-lite-for-woocommerce.php' );
		add_action(
			'admin_notices',
			function () {
				printf(
					'<div class="notice notice-info is-dismissible"><p><strong>%s </strong>%s</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">%s</span></button></div>',
					esc_html( 'Customize My Account Lite:' ),
					wp_kses_post( 'Customize My Account Lite doesn\'t require free version of the plugin. So, free version has now been deactivated', '' ),
					esc_html__( 'Dismiss this notice.', 'customize-my-account-page-for-woocommerce' )
				);
			}
		);
	}
}

if ( ! defined( 'TGWC_PLUGIN_FILE' ) ) {
	define( 'TGWC_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'TGWC_VERSION' ) ) {
	define( 'TGWC_VERSION', '2.1.9' );
}

if ( ! defined( 'TGWC_ABSPATH' ) ) {
	define( 'TGWC_ABSPATH', dirname( TGWC_PLUGIN_FILE ) . '/' );
}

if ( ! defined( 'TGWC_PLUGIN_BASENAME' ) ) {
	define( 'TGWC_PLUGIN_BASENAME', dirname( TGWC_PLUGIN_FILE ) );
}

if ( ! defined( 'TGWC_TEMPLATE_PATH' ) ) {
	define( 'TGWC_TEMPLATE_PATH', dirname( TGWC_PLUGIN_FILE ) . '/templates/' );
}

require_once __DIR__ . '/vendor/autoload.php';


/**
 * Returns the main instance of Themegrill WooCommerce Customizer.
 *
 * @since 0.1.0
 * @return ThemeGrill\WoocommerceCustomizer\WoocommerceCustomizer
 */
// phpcs:ignore
if(!function_exists('TGWC')) {
	function TGWC() {
		return WoocommerceCustomizer::instance();
	}

	TGWC();
}
