<?php
/**
 * WC Subscriptions Compatibility.
 *
 * Adds a "Plan" (product name) column to the My Account > Subscriptions table,
 * renames the "Subscriptions" navigation item to "My Plans", and provides
 * front-end CSS refinements for the subscriptions table.
 *
 * @package ThemeGrill\WoocommerceCustomizer\Compatibility
 * @since 2.1.0
 */

namespace ThemeGrill\WoocommerceCustomizer\Compatibility;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Class WCSubscriptionsCompatibility.
 *
 * @since 2.1.0
 */
class WCSubscriptionsCompatibility {

	/**
	 * Single instance of this class.
	 *
	 * @since 2.1.0
	 * @var WCSubscriptionsCompatibility|null
	 */
	private static $instance = null;

	/**
	 * The label used for the navigation item and page heading.
	 *
	 * @since 2.1.0
	 * @var string
	 */
	private $nav_label = '';

	/**
	 * The label used for the product-name column header.
	 *
	 * @since 2.1.0
	 * @var string
	 */
	private $column_label = '';

	/**
	 * Get singleton instance.
	 *
	 * @since 2.1.0
	 * @return WCSubscriptionsCompatibility
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
	 * @since 2.1.0
	 */
	private function __construct() {
		if ( ! class_exists( 'WC_Subscriptions' ) ) {
			return;
		}

		$this->nav_label    = apply_filters( 'tgwc_subscriptions_nav_label', __( 'My Plans', 'customize-my-account-page-for-woocommerce' ) );
		$this->column_label = apply_filters( 'tgwc_subscriptions_column_label', __( 'Plan', 'customize-my-account-page-for-woocommerce' ) );

		// 1. Rename navigation item.
		add_filter( 'woocommerce_account_menu_items', array( $this, 'rename_subscriptions_nav_item' ), 20 );

		// 2. Add product-name column to the subscriptions table.
		// Hook both filter names to cover all WooCommerce Subscriptions versions.
		add_filter( 'woocommerce_my_subscriptions_columns', array( $this, 'add_plan_column' ) );
		add_filter( 'wcs_get_my_subscriptions_columns', array( $this, 'add_plan_column' ) );
		add_action( 'woocommerce_my_subscriptions_column_subscription-name', array( $this, 'render_plan_column' ) );
		add_action( 'wcs_my_subscriptions_column_subscription-name', array( $this, 'render_plan_column' ) );

		// 3. Fallback: if the template doesn't support custom columns (theme override),
		//    inject the product name via JS on the frontend.
		add_action( 'wp_footer', array( $this, 'inject_plan_names_fallback' ) );

		// 4. Front-end CSS for the subscriptions table.
		add_action( 'wp_head', array( $this, 'output_frontend_css' ) );
	}

	/**
	 * Rename "Subscriptions" to the configured label in the account menu.
	 *
	 * @since 2.1.0
	 *
	 * @param array $items Menu items.
	 * @return array
	 */
	public function rename_subscriptions_nav_item( $items ) {
		if ( isset( $items['subscriptions'] ) ) {
			$items['subscriptions'] = $this->nav_label;
		}
		return $items;
	}

	/**
	 * Insert a "Plan" column before the "Status" column.
	 *
	 * @since 2.1.0
	 *
	 * @param array $columns Table columns.
	 * @return array
	 */
	public function add_plan_column( $columns ) {
		$new_columns = array();

		foreach ( $columns as $key => $label ) {
			// Insert the plan column right before the status column.
			if ( 'subscription-status' === $key ) {
				$new_columns['subscription-name'] = $this->column_label;
			}
			$new_columns[ $key ] = $label;
		}

		// Fallback: if status column was not found, append at the end.
		if ( ! isset( $new_columns['subscription-name'] ) ) {
			$new_columns['subscription-name'] = $this->column_label;
		}

		return $new_columns;
	}

	/**
	 * Render the product names for the plan column.
	 *
	 * @since 2.1.0
	 *
	 * @param \WC_Subscription $subscription The subscription object.
	 * @return void
	 */
	public function render_plan_column( $subscription ) {
		$names = array();

		foreach ( $subscription->get_items() as $item ) {
			$product = $item->get_product();
			$name    = esc_html( $item->get_name() );

			// Link to the product if it still exists and is visible.
			if ( $product && $product->is_visible() ) {
				$name = '<a href="' . esc_url( $product->get_permalink() ) . '">' . $name . '</a>';
			}

			$names[] = $name;
		}

		echo wp_kses_post( implode( ', ', $names ) );
	}

	/**
	 * Fallback: inject plan names via inline JS when the template doesn't
	 * support custom column filters (e.g. theme template override).
	 *
	 * Builds a map of subscription IDs to product names from the current
	 * user's subscriptions and adds a "Plan" column via DOM manipulation.
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function inject_plan_names_fallback() {
		if ( ! function_exists( 'is_account_page' ) || ! is_account_page() ) {
			return;
		}

		if ( ! function_exists( 'wcs_get_subscriptions' ) ) {
			return;
		}

		$subscriptions = wcs_get_subscriptions(
			array(
				'customer_id'            => get_current_user_id(),
				'subscription_status'    => 'any',
				'subscriptions_per_page' => -1,
			)
		);

		if ( empty( $subscriptions ) ) {
			return;
		}

		$plan_map = array();
		foreach ( $subscriptions as $subscription ) {
			$names = array();
			foreach ( $subscription->get_items() as $item ) {
				$names[] = $item->get_name();
			}
			$plan_map[ $subscription->get_id() ] = implode( ', ', $names );
		}

		$column_label = esc_js( $this->column_label );
		$plan_map_json = wp_json_encode( $plan_map );
		?>
		<script id="tgwc-subscriptions-plan-fallback-v2.1.7">
		(function() {
			var table = document.querySelector('.my_account_subscriptions, .woocommerce-orders-table--subscriptions');
			if (!table) return;

			// Check if our column filter already worked (column exists via PHP).
			if (table.querySelector('th.subscription-name, td.subscription-name')) return;

			var planMap = <?php echo $plan_map_json; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>;
			var label = <?php echo wp_json_encode( $this->column_label ); ?>;

			// Find header row and insert Plan column after the first column (Subscription).
			var headerRow = table.querySelector('thead tr');
			if (headerRow) {
				var firstTh = headerRow.querySelector('th');
				if (firstTh) {
					var planTh = document.createElement('th');
					planTh.className = 'subscription-name';
					planTh.textContent = label;
					firstTh.parentNode.insertBefore(planTh, firstTh.nextSibling);
				}
			}

			// Insert plan name cell in each body row.
			var rows = table.querySelectorAll('tbody tr');
			for (var i = 0; i < rows.length; i++) {
				var row = rows[i];
				var firstTd = row.querySelector('td');
				if (!firstTd) continue;

				// Extract subscription ID from the link text (e.g. "#130497").
				var link = firstTd.querySelector('a');
				var idText = link ? link.textContent.trim() : firstTd.textContent.trim();
				var subId = idText.replace('#', '').trim();

				var planTd = document.createElement('td');
				planTd.className = 'subscription-name';
				planTd.textContent = planMap[subId] || '';
				firstTd.parentNode.insertBefore(planTd, firstTd.nextSibling);
			}
		})();
		</script>
		<?php
	}

	/**
	 * Output front-end CSS for the subscriptions table on the account page.
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function output_frontend_css() {
		if ( ! function_exists( 'is_account_page' ) || ! is_account_page() ) {
			return;
		}
		?>
		<style id="tgwc-subscriptions-compat-v2.1.7">
			/* Plan column styling */
			.woocommerce-MyAccount-content .my_account_subscriptions td.subscription-name,
			.woocommerce-MyAccount-content .my_account_subscriptions th.subscription-name {
				min-width: 140px;
			}

			.woocommerce-MyAccount-content .my_account_subscriptions td.subscription-name a {
				color: inherit;
				text-decoration: underline;
				text-decoration-color: transparent;
				transition: text-decoration-color 0.2s ease;
			}

			.woocommerce-MyAccount-content .my_account_subscriptions td.subscription-name a:hover {
				text-decoration-color: currentColor;
			}

			/* Improve table readability on smaller viewports */
			@media screen and (max-width: 768px) {
				.woocommerce-MyAccount-content .my_account_subscriptions {
					display: block;
					overflow-x: auto;
					-webkit-overflow-scrolling: touch;
				}
			}
		</style>
		<?php
	}
}
