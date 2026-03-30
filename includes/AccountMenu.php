<?php
/**
 * Settings page.
 *
 * @package ThemeGrill\WoocommerceCustomizer
 * @since 0.1.0
 */

namespace ThemeGrill\WoocommerceCustomizer;

defined( 'ABSPATH' ) || exit;

class AccountMenu {

	/**
	 * Default endpoints.
	 *
	 * @since 0.4.1
	 * @var array
	 */
	private $default_endpoints = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialization.`
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	private function init() {
		$this->init_hooks();

		do_action( 'tgwc_account_menu_unhook', $this );
	}

	/**
	 * Initialization hooks.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	private function init_hooks() {
		add_action( 'init', array( $this, 'init_default_endpoints' ), 20 );
		add_action( 'init', array( $this, 'add_endpoints' ), 21 );
		add_action( 'init', array( $this, 'flush_rewrite_rules' ), 21 );
		add_action( 'init', array( $this, 'custom_endpoint_title' ), 21 );
		add_action( 'woocommerce_init', array( $this, 'remove_woocommerce_hooks' ) );
		add_filter( 'woocommerce_get_endpoint_url', array( $this, 'modify_endpoint_url' ), PHP_INT_MAX, 4 );
		add_filter( 'woocommerce_account_menu_item_classes', array( $this, 'add_classes' ), PHP_INT_MAX - 10, 2 );
		add_filter( 'woocommerce_get_query_vars', array( $this, 'add_custom_query_vars' ) );
		add_filter( 'the_title', array( $this, 'change_the_title' ), 11 );
		add_filter( 'woocommerce_custom_nav_menu_items', array( $this, 'custom_nav_menu_items' ) );
		add_filter( 'woocommerce_get_endpoint_url', array( $this, 'change_group_endpoint_url' ), 10, 2 );
	}

	/**
	 * Change group endpoint URL.
	 *
	 * @param string $url Endpoint URL.
	 * @param string $endpoint Endpoint slug.
	 * @return string
	 */
	public function change_group_endpoint_url( $url, $endpoint ) {
		$endpoints = tgwc_get_endpoints_flat();
		if ( isset( $endpoints[ $endpoint ] ) && 'group' === $endpoints[ $endpoint ]['type'] ) {
			return '#';
		}
		return $url;
	}

	/**
	 * Add items to custom nav menu items.
	 *
	 * @param array $items Endpoints.
	 * @return array'
	 */
	public function custom_nav_menu_items( $items ) {
		$endpoints = tgwc_get_endpoints_flat();
		return wp_parse_args(
			array_reduce(
				array_keys( $endpoints ),
				function ( $acc, $curr ) use ( $endpoints ) {
					if ( $endpoints[ $curr ]['enable'] ) {
						$acc[ $curr ] = $endpoints[ $curr ]['label'];
					}
					return $acc;
				},
				array()
			),
			$items
		);
	}

	/**
	 * Initialize default endpoints.
	 *
	 * @since 0.4.1
	 * @return void
	 */
	public function init_default_endpoints() {

		if ( ! empty( $this->default_endpoints ) ) {
			return;
		}

		$endpoints_slugs = array(
			'orders'          => get_option( 'woocommerce_myaccount_orders_endpoint', 'orders' ),
			'downloads'       => get_option( 'woocommerce_myaccount_downloads_endpoint', 'downloads' ),
			'edit-address'    => get_option( 'woocommerce_myaccount_edit_address_endpoint', 'edit-address' ),
			'payment-methods' => get_option( 'woocommerce_myaccount_payment_methods_endpoint', 'payment-methods' ),
			'edit-account'    => get_option( 'woocommerce_myaccount_edit_account_endpoint', 'edit-account' ),
			'customer-logout' => get_option( 'woocommerce_logout_endpoint', 'customer-logout' ),
		);

		$endpoints = array(
			'dashboard'       => __( 'Dashboard', 'customize-my-account-page-for-woocommerce' ),
			'orders'          => __( 'Orders', 'customize-my-account-page-for-woocommerce' ),
			'downloads'       => __( 'Downloads', 'customize-my-account-page-for-woocommerce' ),
			'edit-address'    => __( 'Addresses', 'customize-my-account-page-for-woocommerce' ),
			'payment-methods' => __( 'Payment Methods', 'customize-my-account-page-for-woocommerce' ),
			'edit-account'    => __( 'Account Details', 'customize-my-account-page-for-woocommerce' ),
			'customer-logout' => __( 'Log out', 'customize-my-account-page-for-woocommerce' ),
		);

		$tgwc_endpoints = apply_filters( 'woocommerce_account_menu_items', $endpoints, $endpoints_slugs );
		$endpoints      = array_merge( $endpoints, is_array( $tgwc_endpoints ) ? $tgwc_endpoints : array() );

		if ( class_exists( 'WC_Memberships' ) ) {
			$endpoints_slugs['members-area'] = wc_memberships_get_members_area_endpoint();
			$endpoints['members-area']       = __( 'Memberships', 'customize-my-account-page-for-woocommerce' );
		}

		if ( class_exists( 'WC_Subscriptions' ) ) {
			$endpoints_slugs['subscriptions'] = get_option( 'woocommerce_myaccount_subscriptions_endpoint', 'subscriptions' );
			$endpoints['subscriptions']       = __( 'Subscriptions', 'customize-my-account-page-for-woocommerce' );
		}

		if ( is_admin() && function_exists( 'wc_memberships_for_teams' ) ) {
			$endpoints_slugs['teams'] = get_option( 'woocommerce_myaccount_teams_area_endpoint', 'teams' );
			$endpoints['teams']       = __( 'Team', 'customize-my-account-page-for-woocommerce' );
		}

		$this->default_endpoints = apply_filters( 'tgwc_default_endpoints', $endpoints, $endpoints_slugs );
	}

	/**
	 * Get default endpoints
	 *
	 * @since 0.4.1
	 * @return array Default endpoints.
	 */
	public function get_default_endpoints() {
		return apply_filters( 'tgwc_get_default_endpoints', $this->default_endpoints );
	}

	/**
	 * Change the default endpoint title.
	 *
	 * @since 0.2.0
	 *
	 * @param string $title Endpoint title.
	 *
	 * @return string
	 */
	public function change_the_title( $title ) {
		global $wp;

		if ( 'dashboard' !== tgwc_get_default_endpoint() || ! in_the_loop() || ! is_account_page() ) {
			return $title;
		}

		if ( '' === WC()->query->get_current_endpoint() || ! in_the_loop() || ! is_account_page() ) {
			return $title;
		}

		$default_endpoint = \tgwc_get_default_endpoint();
		$endpoint         = \tgwc_get_endpoint( $default_endpoint );

		if ( ! isset( $wp->query_vars['page'], $endpoint['label'] ) ) {
			return $title;
		}

		if ( empty( $endpoint['label'] ) ) {
			return $title;
		}

		// unhook after we've returned our title to prevent it from overriding others
		remove_filter( 'the_title', array( $this, __FUNCTION__ ), 11 );

		return $endpoint['label'];
	}

	/**
	 * Return the title of custom endpoint.
	 *
	 * @since 0.1.0
	 */
	public function custom_endpoint_title() {
		foreach ( tgwc_get_endpoints_by_type( 'endpoint' ) as $slug => $endpoint ) {
			add_filter(
				"woocommerce_endpoint_{$slug}_title",
				function ( $title, $endpoint ) {
					$endpoint = \tgwc_get_endpoint( $endpoint );

					if ( isset( $endpoint['label'] ) ) {
						$title = $endpoint['label'];
					}

					return $title;
				},
				20,
				2
			);
		}
	}

	/**
	 * Add custom endpoint to WooCommerce query vars.
	 *
	 * @since 0.1.0
	 *
	 * @param array $query_vars WooCommerce query vars.
	 *
	 * @return array Modified query vars.
	 */
	public function add_custom_query_vars( $query_vars ) {
		$endpoints = \tgwc_get_endpoints_by_type( 'endpoint' );
		$endpoints = array_keys( $endpoints );
		$endpoints = array_reduce(
			$endpoints,
			function ( $result, $endpoint ) {
				$result[ $endpoint ] = $endpoint;
				return $result;
			},
			array()
		);

		return array_merge( $endpoints, $query_vars );
	}

	/**
	 * Flush rewrite rules on page load.
	 *
	 * @since  0.1.0
	 *
	 * @return void
	 */
	public function flush_rewrite_rules() {
		if ( get_option( 'tgwc_flush_rewrite' ) ) {
			flush_rewrite_rules();
			update_option( 'tgwc_flush_rewrite', false );
		}
	}

	/**
	 * Display account menu item.
	 *
	 * @since 0.1.0
	 *
	 * @param string $slug  Endpoint slug.
	 *
	 * @return void
	 */
	public function display_myaccount_menu_item( $slug ) {
		$endpoint = \tgwc_get_endpoint( $slug );

		if ( false === $endpoint ) {
			return;
		}

		$account_menu_types = array(
			'endpoint',
			'link',
			'group',
		);

		$type = $endpoint['type'];
		if ( in_array( $type, $account_menu_types, true ) ) {
			$endpoint['slug'] = $slug;
			wc_get_template(
				"frontend/{$type}-item.php",
				$endpoint,
				TGWC_TEMPLATE_PATH,
				TGWC_TEMPLATE_PATH
			);
		}
	}

	/**
	 * Remove woocommerce default hooks.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function remove_woocommerce_hooks() {
		$priority = has_action( 'woocommerce_account_navigation', 'woocommerce_account_navigation' );
		if ( false !== $priority ) {
			remove_action( 'woocommerce_account_navigation', 'woocommerce_account_navigation', $priority );
		}

		$priority = has_action( 'woocommerce_account_content', 'woocommerce_account_content' );
		if ( false !== $priority ) {
			remove_action( 'woocommerce_account_content', 'woocommerce_account_content', $priority );
		}
	}

	/**
	 * Add classes to the endpoints.
	 *
	 * @since 0.1.0
	 *
	 * @param array  $classes CSS classes list.
	 * @param string $endpoint Endpoint slug.
	 *
	 * @return array Modified classes list.
	 */
	public function add_classes( $classes, $endpoint ) {
		global $wp_query;
		$query = $wp_query->query;

		$class   = tgwc_get_endpoint_class( $endpoint );
		$classes = array_merge( $classes, $class );

		if ( isset( $query[ $endpoint ] ) ) {
			$classes[] = 'tab_selected';
		}

		// Set the tab selected and is-active for default endpoint set in the settings.
		if ( isset( $query['page'] ) && empty( $query['page'] )
			&& tgwc_get_default_endpoint() === $endpoint ) {
			$classes[] = 'tab_selected';
			$classes[] = 'is-active';
		}

		// Remove the is-active for dashboard if it is not default endpoint.
		if ( isset( $query['page'] ) && empty( $query['page'] ) && 'dashboard' === $endpoint ) {
			$index = array_search( 'is-active', $classes, true );
			unset( $classes[ $index ] );
		}

		$endpoint = \tgwc_get_endpoint( $endpoint );

		if ( false !== $endpoint ) {
			$classes[]     = 'tgwc-' . $endpoint['type'];
			$settings      = tgwc_get_customizer_values();
			$icon_position = isset( $settings['navigation']['icon_position'] ) ? $settings['navigation']['icon_position'] : 'left';
			$classes[]     = "tgwc-navicon-{$icon_position}";
		}

		return apply_filters( 'tgwc_account_menu_item_classes', array_unique( $classes ), $endpoint );
	}

	/**
	 * Modify the url for link type endpoint.
	 *
	 * @since 0.1.0
	 *
	 * @param String $url       Endpoint's URL.
	 * @param String $endpoint  Endpoint slug.
	 * @param String $value     Endpoint value.
	 * @param String $permalink Endpoint permalink
	 *
	 * @return String Modified endpoint URL.
	 */
	public function modify_endpoint_url( $url, $endpoint, $value, $permalink ) {
		// Compatible with WooCommerce Membership by SkyVerge.
		if ( class_exists( 'WC_Memberships' ) && 'members-area' === $endpoint ) {
			$members_area_endpoint = wc_memberships_get_members_area_endpoint();
			$url                   = str_replace( $endpoint, $members_area_endpoint, $url );
		}

		$link_url = tgwc_get_link_url( $endpoint );
		if ( ! empty( $link_url ) ) {
			$url = $link_url;
		}

		return $url;
	}

	/**
	 * Rewrite custom endpoint's URL.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function add_endpoints() {
		$roles     = tgwc_get_user_roles();
		$endpoints = tgwc_get_endpoints_flat();

		$endpoints              = array_filter(
			$endpoints,
			function ( $endpoint ) use ( $roles ) {
				return 'endpoint' === $endpoint['type'];
			}
		);
		$default_endpoint_slugs = TGWC()->account_menu->get_default_endpoints();

		// Rewrite rule for endpoint.
		foreach ( $endpoints as $slug => $endpoint ) {
			if ( ! isset( $default_endpoint_slugs[ $slug ] ) ) {
				$slug = ! empty( $endpoint['slug'] ) ? $endpoint['slug'] : $slug;
				add_rewrite_endpoint( $slug, EP_ROOT | EP_PAGES );
			}
		}

		$default_endpoint = \tgwc_get_default_endpoint();
		if ( 'dashboard' !== $default_endpoint ) {
			add_rewrite_endpoint( 'dashboard', EP_ROOT | EP_PAGES );
		}
	}
}
