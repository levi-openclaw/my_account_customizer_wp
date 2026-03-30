<?php
/**
 * Eligibility-based access control for My Account menu items.
 *
 * Hides menu items unless the current user actually has data for them
 * (e.g. active subscriptions, memberships, downloads, license keys).
 * Each section can be toggled on/off from the plugin's Settings tab.
 *
 * Third-party plugins can register their own eligibility rules via the
 * `tgwc_eligibility_rules` filter.
 *
 * @package ThemeGrill\WoocommerceCustomizer
 * @since 2.1.0
 */

namespace ThemeGrill\WoocommerceCustomizer;

defined( 'ABSPATH' ) || exit;

class EligibilityAccess {

	/**
	 * Singleton instance.
	 *
	 * @var EligibilityAccess|null
	 */
	private static $instance = null;

	/**
	 * Registered eligibility rules.
	 *
	 * @var array
	 */
	private $rules = array();

	/**
	 * Get singleton instance.
	 *
	 * @return EligibilityAccess
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
		$this->register_default_rules();
		add_filter( 'woocommerce_account_menu_items', array( $this, 'filter_menu_items' ), 25 );
	}

	/**
	 * Register the built-in eligibility rules.
	 *
	 * Each rule is an array with:
	 *   - label       (string) Human-readable name for the settings UI.
	 *   - description (string) Help text shown below the toggle.
	 *   - endpoint    (string) The menu-item key to hide/show.
	 *   - callback    (callable) Returns true when the user is eligible.
	 *   - plugin      (string) Class or function that must exist for the rule to apply.
	 *
	 * @return void
	 */
	private function register_default_rules() {
		$rules = array(
			'subscriptions' => array(
				'label'       => __( 'My Plans / Subscriptions', 'customize-my-account-page-for-woocommerce' ),
				'description' => __( 'Only show when the user has at least one subscription.', 'customize-my-account-page-for-woocommerce' ),
				'endpoint'    => 'subscriptions',
				'callback'    => array( $this, 'user_has_subscriptions' ),
				'plugin'      => 'WC_Subscriptions',
			),
			'memberships'   => array(
				'label'       => __( 'Memberships', 'customize-my-account-page-for-woocommerce' ),
				'description' => __( 'Only show when the user has at least one active membership.', 'customize-my-account-page-for-woocommerce' ),
				'endpoint'    => 'members-area',
				'callback'    => array( $this, 'user_has_memberships' ),
				'plugin'      => 'WC_Memberships',
			),
			'downloads'     => array(
				'label'       => __( 'Downloads', 'customize-my-account-page-for-woocommerce' ),
				'description' => __( 'Only show when the user has downloadable files.', 'customize-my-account-page-for-woocommerce' ),
				'endpoint'    => 'downloads',
				'callback'    => array( $this, 'user_has_downloads' ),
				'plugin'      => null,
			),
			'license-keys'  => array(
				'label'       => __( 'License Keys', 'customize-my-account-page-for-woocommerce' ),
				'description' => __( 'Only show when the user has license keys. Works with WooCommerce Software Add-on, License Manager, and similar plugins.', 'customize-my-account-page-for-woocommerce' ),
				'endpoint'    => 'license-keys',
				'callback'    => array( $this, 'user_has_license_keys' ),
				'plugin'      => null,
			),
		);

		/**
		 * Filter the eligibility rules.
		 *
		 * Third-party plugins can add their own rules here:
		 *
		 *   add_filter( 'tgwc_eligibility_rules', function( $rules ) {
		 *       $rules['my-section'] = array(
		 *           'label'       => 'My Section',
		 *           'description' => 'Only show when …',
		 *           'endpoint'    => 'my-section',
		 *           'callback'    => 'my_eligibility_check',
		 *           'plugin'      => null,
		 *       );
		 *       return $rules;
		 *   } );
		 *
		 * @param array $rules Eligibility rules.
		 */
		$this->rules = apply_filters( 'tgwc_eligibility_rules', $rules );
	}

	/**
	 * Get all registered rules.
	 *
	 * @return array
	 */
	public function get_rules() {
		return $this->rules;
	}

	/**
	 * Get the saved eligibility settings from the database.
	 *
	 * @return array Associative array of rule_key => bool (enabled).
	 */
	public function get_eligibility_settings() {
		$settings = TGWC()->get_settings()->get_settings();
		$saved    = isset( $settings['eligibility'] ) ? $settings['eligibility'] : array();

		$defaults = array();
		foreach ( array_keys( $this->rules ) as $key ) {
			$defaults[ $key ] = false; // Disabled by default — all items visible.
		}

		return wp_parse_args( $saved, $defaults );
	}

	/**
	 * Filter menu items based on eligibility.
	 *
	 * @param array $items Account menu items.
	 * @return array
	 */
	public function filter_menu_items( $items ) {
		if ( is_admin() || ! is_user_logged_in() ) {
			return $items;
		}

		$eligibility_settings = $this->get_eligibility_settings();

		foreach ( $this->rules as $rule_key => $rule ) {
			// Skip if this toggle is not enabled.
			if ( empty( $eligibility_settings[ $rule_key ] ) ) {
				continue;
			}

			// Skip if the required plugin is not active.
			if ( ! empty( $rule['plugin'] ) && ! class_exists( $rule['plugin'] ) && ! function_exists( $rule['plugin'] ) ) {
				continue;
			}

			$endpoint = $rule['endpoint'];

			// If the endpoint is in the menu and the user is NOT eligible, remove it.
			if ( isset( $items[ $endpoint ] ) && is_callable( $rule['callback'] ) ) {
				if ( ! call_user_func( $rule['callback'] ) ) {
					unset( $items[ $endpoint ] );
				}
			}
		}

		return $items;
	}

	// ------------------------------------------------------------------
	// Built-in eligibility callbacks
	// ------------------------------------------------------------------

	/**
	 * Check if user has any subscriptions (including cancelled/expired).
	 *
	 * Includes all statuses so users with cancelled or expired subscriptions
	 * can still see the tab and resubscribe.
	 *
	 * @return bool
	 */
	public function user_has_subscriptions() {
		if ( ! function_exists( 'wcs_get_subscriptions' ) ) {
			return false;
		}
		$subscriptions = wcs_get_subscriptions(
			array(
				'customer_id'       => get_current_user_id(),
				'subscription_status' => array( 'active', 'on-hold', 'pending', 'pending-cancel', 'cancelled', 'expired' ),
				'subscriptions_per_page' => 1,
			)
		);
		return ! empty( $subscriptions );
	}

	/**
	 * Check if user has any memberships.
	 *
	 * @return bool
	 */
	public function user_has_memberships() {
		if ( ! function_exists( 'wc_memberships_get_user_memberships' ) ) {
			return false;
		}
		$memberships = wc_memberships_get_user_memberships( get_current_user_id() );
		return ! empty( $memberships );
	}

	/**
	 * Check if user has any downloads.
	 *
	 * @return bool
	 */
	public function user_has_downloads() {
		if ( ! function_exists( 'wc_get_customer_available_downloads' ) ) {
			return false;
		}
		$downloads = wc_get_customer_available_downloads( get_current_user_id() );
		return ! empty( $downloads );
	}

	/**
	 * Check if user has license keys.
	 *
	 * Supports multiple license-key plugins by checking common storage patterns:
	 * - WooCommerce Software Add-on (postmeta _api_activations)
	 * - License Manager for WooCommerce (lmfwc_licenses table)
	 * - WooCommerce API Manager
	 *
	 * @return bool
	 */
	public function user_has_license_keys() {
		global $wpdb;
		$user_id = get_current_user_id();

		// License Manager for WooCommerce.
		if ( class_exists( 'LicenseManagerForWooCommerce' ) || class_exists( '\IdeoLogix\DigitalLicenseManager\Boot\Bootstrap' ) ) {
			$table = $wpdb->prefix . 'lmfwc_licenses';
			if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) === $table ) {
				$count = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT COUNT(*) FROM {$table} WHERE user_id = %d",
						$user_id
					)
				);
				return intval( $count ) > 0;
			}
		}

		// WooCommerce API Manager.
		if ( class_exists( 'WooCommerce_API_Manager' ) || class_exists( 'WC_AM_API_Activation_Data_Store' ) ) {
			$table = $wpdb->prefix . 'wc_am_api_activation';
			if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) === $table ) {
				$count = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT COUNT(*) FROM {$table} WHERE user_id = %d",
						$user_id
					)
				);
				return intval( $count ) > 0;
			}
		}

		// WooCommerce Software Add-on — licenses stored as order-item meta.
		$orders = wc_get_orders(
			array(
				'customer_id' => $user_id,
				'limit'       => 1,
				'meta_key'    => '_api_software_title_parent',
				'meta_compare' => 'EXISTS',
				'return'       => 'ids',
			)
		);
		if ( ! empty( $orders ) ) {
			return true;
		}

		/**
		 * Let other license-key plugins declare eligibility.
		 *
		 * @param bool $has_keys Whether the user has license keys.
		 * @param int  $user_id  The current user ID.
		 */
		return apply_filters( 'tgwc_user_has_license_keys', false, $user_id );
	}
}
