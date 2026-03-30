<?php
/**
 * Flatsome Compatibility
 *
 * @package ThemeGrill\WoocommerceCustomizer\Compatibility
 * @since 0.4.4
 */

namespace ThemeGrill\WoocommerceCustomizer\Compatibility;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Class FlatsomeCompatibility.
 *
 * @since 0.4.4
 */
class FlatsomeCompatibility {

	/**
	 * Single instance of this class.
	 *
	 * @since 0.4.4
	 * @var null
	 */
	private static $instance = null;

	/**
	 * Get FlatsomeCompatibility instance.
	 *
	 * @since 0.4.4
	 * @return FlatsomeCompatibility|null
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @since 0.4.4
	 */
	private function __construct() {
		add_filter( 'template_include', array( $this, 'override_my_account_page_template' ) );
	}

	/**
	 * Override my account page template.
	 *
	 * @since 0.4.4
	 * @param string $template The path of the template to include.
	 * @return string
	 */
	public function override_my_account_page_template( $template ) {
		$flatsome = wp_get_theme( 'flatsome' );

		if ( ! $flatsome->exists() || ! is_account_page() || false === strpos( $template, 'page-my-account.php' ) ) {
			return $template;
		}

		remove_action( 'woocommerce_account_dashboard', 'flatsome_my_account_dashboard' );

		return __DIR__ . '/templates/flatsome/page-my-account.php';
	}
}
