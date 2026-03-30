<?php
/**
 * Class JetpackCRMCompatibility.
 *
 * @package ThemeGrill\WoocommerceCustomizer\Compatibility
 * @since 0.4.5
 */

namespace ThemeGrill\WoocommerceCustomizer\Compatibility;

defined( 'ABSPATH' ) || exit;

/**
 * Class JetpackCRMCompatibility.
 *
 * @since 0.4.5
 */
class JetpackCRMCompatibility {

	/**
	 * Holds the JetpackCRMCompatibility instance.
	 *
	 * @var null|JetpackCRMCompatibility
	 */
	private static $instance = null;

	/**
	 * Holds the ZeroBSCRM instance.
	 *
	 * @var null|\ZeroBSCRM
	 */
	private $zbs = null;

	/**
	 * Get JetpackCRMCompatibility instance.
	 *
	 * @return JetpackCRMCompatibility|null
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->setup();
		add_filter( 'tgwc_get_endpoints', array( $this, 'modify_endpoints' ) );
	}

	/**
	 * Modify endpoints.
	 *
	 * @param array $endpoints Array of endpoints.
	 *
	 * @return array
	 */
	public function modify_endpoints( $endpoints ) {
		if ( is_null( $this->zbs ) || is_admin() ) {
			return $endpoints;
		}
		if ( $this->zbs->modules->portal->is_portal_page() && array_key_exists( 'invoices', $endpoints ) ) {
			unset( $endpoints['invoices'] );
		}
		return $endpoints;
	}

	/**
	 * Setup compatibility.
	 *
	 * @return void
	 */
	private function setup() {
		if ( class_exists( 'ZeroBSCRM' ) ) {
			$this->zbs = \ZeroBSCRM::instance();
		}
	}
}
