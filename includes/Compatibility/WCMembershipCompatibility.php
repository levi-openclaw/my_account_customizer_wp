<?php
/**
 * WC Membership Compatibility.
 *
 * @package ThemeGrill\WoocommerceCustomizer\Compatibility
 * @since 0.4.2
 */

namespace ThemeGrill\WoocommerceCustomizer\Compatibility;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Class WCMembershipCompatibility.
 *
 * @since 0.4.2
 */
class WCMembershipCompatibility {

	/**
	 * Single instance of this class.
	 *
	 * @since 0.4.2
	 * @var null
	 */
	private static $instance = null;

	/**
	 * Holds members area instance.
	 *
	 * @since 0.4.2
	 * @var null
	 */
	private $members_area = null;

	/**
	 * Get WCMembershipCompatibility instance.
	 *
	 * @return WCMembershipCompatibility|null
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
	 * @since 0.4.2
	 * @return void
	 */
	private function __construct() {
		$this->setup();
		add_action( 'tgwc_my_account_menu_item', array( $this, 'wc_membership_navigation' ), 1 );
		add_action( 'wp_head', array( $this, 'output_membership_css' ) );

		// Prevent WCM from redirecting single-membership users to the detail view.
		add_filter( 'wc_memberships_redirect_single_membership', '__return_false' );
		add_action( 'template_redirect', array( $this, 'block_membership_redirect' ), 0 );

		// Render unified members area. Hook the actual endpoint slug (which
		// may differ from 'members-area' based on WCM settings).
		$endpoint_slug = function_exists( 'wc_memberships_get_members_area_endpoint' )
			? wc_memberships_get_members_area_endpoint()
			: 'members-area';
		add_action( 'woocommerce_account_' . $endpoint_slug . '_endpoint', array( $this, 'maybe_render_unified_view' ), 1 );
		// Also hook the internal key used by this plugin.
		if ( 'members-area' !== $endpoint_slug ) {
			add_action( 'woocommerce_account_members-area_endpoint', array( $this, 'maybe_render_unified_view' ), 1 );
		}
	}

	/**
	 * Setup.
	 *
	 * @since 0.4.2
	 * @return void
	 */
	private function setup() {
		if ( ! function_exists( 'wc_memberships' ) ) {
			return;
		}

		$wc_membership_frontend_instance = wc_memberships()->get_frontend_instance();

		if ( empty( $wc_membership_frontend_instance ) ) {
			return;
		}

		if ( version_compare( '1.19.0', \WC_Memberships::VERSION, '<=' ) ) {
			$this->members_area = $wc_membership_frontend_instance->get_my_account_instance()->get_members_area_instance();
		} else {
			$this->members_area = $wc_membership_frontend_instance->get_members_area_instance();
		}
	}

	/**
	 * Block WooCommerce Memberships from redirecting to a single membership's
	 * detail view. WCM hooks into template_redirect to auto-redirect users
	 * who have exactly one membership. We remove that redirect so the unified
	 * view can render instead.
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function block_membership_redirect() {
		if ( empty( $this->members_area ) ) {
			return;
		}

		// Remove WCM's template_redirect handlers that cause the single-membership redirect.
		// The method name varies by WCM version.
		$redirect_methods = array(
			'redirect_to_members_area',
			'redirect_to_member_area',
			'maybe_redirect_to_members_area',
		);

		global $wp_filter;
		if ( isset( $wp_filter['template_redirect'] ) ) {
			foreach ( $wp_filter['template_redirect']->callbacks as $priority => $callbacks ) {
				foreach ( $callbacks as $key => $callback ) {
					if ( ! isset( $callback['function'] ) || ! is_array( $callback['function'] ) ) {
						continue;
					}
					$method = $callback['function'][1] ?? '';
					$object = $callback['function'][0] ?? null;
					if ( is_object( $object ) && in_array( $method, $redirect_methods, true ) ) {
						remove_action( 'template_redirect', $callback['function'], $priority );
					}
				}
			}
		}
	}

	/**
	 * Render the unified members area view when visiting /members-area/
	 * without a specific membership ID.
	 *
	 * Aggregates content, products, and discounts from ALL of the user's
	 * memberships into a single page. For discounts, only the best (highest)
	 * discount per product is shown.
	 *
	 * @since 2.1.0
	 *
	 * @param string $value Endpoint query value.
	 * @return void
	 */
	public function maybe_render_unified_view( $value ) {
		// Only intercept the top-level members-area (no specific membership selected).
		// When $value contains a membership ID (numeric) or a sub-path like
		// "123/content", let WCM handle it normally.
		$value = trim( (string) $value );
		if ( '' !== $value ) {
			return;
		}

		if ( ! function_exists( 'wc_memberships_get_user_memberships' ) ) {
			return;
		}

		$user_memberships = wc_memberships_get_user_memberships();

		if ( empty( $user_memberships ) ) {
			return;
		}

		// Remove default WCM rendering so we fully control output.
		$this->remove_default_members_area_output();

		echo '<div class="tgwc-unified-members-area">';

		// --- Membership overview ---
		$this->render_memberships_overview( $user_memberships );

		// --- Combined Discounts (best discount per product) ---
		$this->render_combined_discounts( $user_memberships );

		// --- Combined Content ---
		$this->render_combined_content( $user_memberships );

		// --- Combined Products ---
		$this->render_combined_products( $user_memberships );

		echo '</div>';

		// Prevent WCM's default output from also rendering.
		remove_all_actions( 'woocommerce_account_members-area_endpoint' );
	}

	/**
	 * Remove WooCommerce Memberships' default members area template output
	 * so we can replace it.
	 *
	 * @since 2.1.0
	 * @return void
	 */
	private function remove_default_members_area_output() {
		if ( empty( $this->members_area ) ) {
			return;
		}
		remove_all_actions( 'wc_memberships_members_area_my-memberships' );
		remove_all_actions( 'wc_memberships_members_area_before_content' );
		remove_all_actions( 'wc_memberships_members_area_after_content' );
	}

	/**
	 * Render the memberships overview table.
	 *
	 * @since 2.1.0
	 *
	 * @param \WC_Memberships_User_Membership[] $memberships User memberships.
	 * @return void
	 */
	private function render_memberships_overview( $memberships ) {
		echo '<div class="tgwc-memberships-overview">';
		echo '<h3>' . esc_html__( 'Your Memberships', 'customize-my-account-page-for-woocommerce' ) . '</h3>';
		echo '<table class="shop_table my_account_memberships"><thead><tr>';
		echo '<th>' . esc_html__( 'Membership', 'customize-my-account-page-for-woocommerce' ) . '</th>';
		echo '<th>' . esc_html__( 'Status', 'customize-my-account-page-for-woocommerce' ) . '</th>';
		echo '<th>' . esc_html__( 'Since', 'customize-my-account-page-for-woocommerce' ) . '</th>';
		echo '<th>' . esc_html__( 'Expires', 'customize-my-account-page-for-woocommerce' ) . '</th>';
		echo '</tr></thead><tbody>';

		foreach ( $memberships as $membership ) {
			$plan       = $membership->get_plan();
			$status     = wc_memberships_get_user_membership_status_name( $membership->get_status() );
			$start_date = $membership->get_local_start_date( 'timestamp' );
			$end_date   = $membership->get_local_end_date( 'timestamp' );

			echo '<tr>';
			echo '<td>' . esc_html( $plan ? $plan->get_name() : __( 'Unknown Plan', 'customize-my-account-page-for-woocommerce' ) ) . '</td>';
			echo '<td>' . esc_html( $status ) . '</td>';
			echo '<td>' . ( $start_date ? esc_html( date_i18n( wc_date_format(), $start_date ) ) : '&mdash;' ) . '</td>';
			echo '<td>' . ( $end_date ? esc_html( date_i18n( wc_date_format(), $end_date ) ) : esc_html__( 'Unlimited', 'customize-my-account-page-for-woocommerce' ) ) . '</td>';
			echo '</tr>';
		}

		echo '</tbody></table></div>';
	}

	/**
	 * Render combined discounts across all memberships.
	 *
	 * When a product has discounts from multiple memberships, only the
	 * highest (best) discount is shown.
	 *
	 * @since 2.1.0
	 *
	 * @param \WC_Memberships_User_Membership[] $memberships User memberships.
	 * @return void
	 */
	private function render_combined_discounts( $memberships ) {
		if ( ! function_exists( 'wc_memberships_get_member_product_discount' ) ) {
			// Fall back to collecting discount rules from plans.
			$this->render_combined_discounts_from_rules( $memberships );
			return;
		}

		$this->render_combined_discounts_from_rules( $memberships );
	}

	/**
	 * Render combined discounts by collecting discount rules from all
	 * membership plans and showing the best discount per product.
	 *
	 * @since 2.1.0
	 *
	 * @param \WC_Memberships_User_Membership[] $memberships User memberships.
	 * @return void
	 */
	private function render_combined_discounts_from_rules( $memberships ) {
		$discount_rows = array();

		foreach ( $memberships as $membership ) {
			$plan = $membership->get_plan();
			if ( ! $plan || ! $membership->is_active() ) {
				continue;
			}

			$plan_name = $plan->get_name();
			$rules     = $plan->get_purchasing_discount_rules();

			if ( empty( $rules ) ) {
				continue;
			}

			foreach ( $rules as $rule ) {
				$object_ids = $rule->get_object_ids();
				$amount     = (float) $rule->get_discount_amount();
				$type       = $rule->get_discount_type();

				// Normalize to a comparable value.
				$is_percentage = ( false !== strpos( $type, 'percentage' ) );

				if ( ! empty( $object_ids ) ) {
					// Discount applies to specific products/categories.
					foreach ( $object_ids as $object_id ) {
						$key = $object_id . '_' . ( $is_percentage ? 'pct' : 'amt' );
						$target_type = ( false !== strpos( $type, 'category' ) ) ? 'category' : 'product';

						if ( ! isset( $discount_rows[ $key ] ) || $amount > $discount_rows[ $key ]['amount'] ) {
							$discount_rows[ $key ] = array(
								'object_id'   => $object_id,
								'target_type' => $target_type,
								'amount'      => $amount,
								'is_pct'      => $is_percentage,
								'plan_name'   => $plan_name,
							);
						}
					}
				} else {
					// Discount applies to all products.
					$key = 'all_' . ( $is_percentage ? 'pct' : 'amt' );
					if ( ! isset( $discount_rows[ $key ] ) || $amount > $discount_rows[ $key ]['amount'] ) {
						$discount_rows[ $key ] = array(
							'object_id'   => 0,
							'target_type' => 'all',
							'amount'      => $amount,
							'is_pct'      => $is_percentage,
							'plan_name'   => $plan_name,
						);
					}
				}
			}
		}

		if ( empty( $discount_rows ) ) {
			return;
		}

		echo '<div class="tgwc-memberships-discounts">';
		echo '<h3>' . esc_html__( 'Your Discounts', 'customize-my-account-page-for-woocommerce' ) . '</h3>';
		echo '<table class="shop_table tgwc-discounts-table"><thead><tr>';
		echo '<th>' . esc_html__( 'Applies To', 'customize-my-account-page-for-woocommerce' ) . '</th>';
		echo '<th>' . esc_html__( 'Discount', 'customize-my-account-page-for-woocommerce' ) . '</th>';
		echo '<th>' . esc_html__( 'Via Membership', 'customize-my-account-page-for-woocommerce' ) . '</th>';
		echo '</tr></thead><tbody>';

		foreach ( $discount_rows as $row ) {
			$applies_to = '';
			if ( 'all' === $row['target_type'] ) {
				$applies_to = __( 'All Products', 'customize-my-account-page-for-woocommerce' );
			} elseif ( 'category' === $row['target_type'] ) {
				$term = get_term( $row['object_id'], 'product_cat' );
				$applies_to = $term && ! is_wp_error( $term ) ? $term->name : sprintf( __( 'Category #%d', 'customize-my-account-page-for-woocommerce' ), $row['object_id'] );
			} else {
				$product = wc_get_product( $row['object_id'] );
				$applies_to = $product ? $product->get_name() : sprintf( __( 'Product #%d', 'customize-my-account-page-for-woocommerce' ), $row['object_id'] );
			}

			$discount_display = $row['is_pct']
				? round( $row['amount'] ) . '%'
				: wc_price( $row['amount'] );

			// Highlight 100% discounts.
			if ( $row['is_pct'] && $row['amount'] >= 100 ) {
				$discount_display = '<strong>' . esc_html__( 'FREE', 'customize-my-account-page-for-woocommerce' ) . '</strong>';
			}

			echo '<tr>';
			echo '<td>' . esc_html( $applies_to ) . '</td>';
			echo '<td>' . wp_kses_post( $discount_display ) . '</td>';
			echo '<td>' . esc_html( $row['plan_name'] ) . '</td>';
			echo '</tr>';
		}

		echo '</tbody></table></div>';
	}

	/**
	 * Render combined content from all memberships.
	 *
	 * Aggregates restricted content accessible to the user across all
	 * their active memberships.
	 *
	 * @since 2.1.0
	 *
	 * @param \WC_Memberships_User_Membership[] $memberships User memberships.
	 * @return void
	 */
	private function render_combined_content( $memberships ) {
		$content_items = array();
		$seen_ids      = array();

		foreach ( $memberships as $membership ) {
			$plan = $membership->get_plan();
			if ( ! $plan || ! $membership->is_active() ) {
				continue;
			}

			$rules = $plan->get_content_restriction_rules();
			if ( empty( $rules ) ) {
				continue;
			}

			foreach ( $rules as $rule ) {
				$object_ids  = $rule->get_object_ids();
				$content_type = $rule->get_content_type();

				foreach ( $object_ids as $object_id ) {
					if ( isset( $seen_ids[ $object_id ] ) ) {
						continue;
					}
					$seen_ids[ $object_id ] = true;

					$post = get_post( $object_id );
					if ( ! $post || 'publish' !== $post->post_status ) {
						continue;
					}

					// Check the user can actually access this content.
					if ( function_exists( 'wc_memberships_is_post_content_restricted' ) && ! current_user_can( 'wc_memberships_view_restricted_post_content', $object_id ) ) {
						continue;
					}

					$content_items[] = array(
						'id'        => $object_id,
						'title'     => $post->post_title,
						'type'      => $post->post_type,
						'url'       => get_permalink( $object_id ),
						'excerpt'   => wp_trim_words( $post->post_excerpt ? $post->post_excerpt : $post->post_content, 25 ),
						'plan_name' => $plan->get_name(),
					);
				}
			}
		}

		if ( empty( $content_items ) ) {
			return;
		}

		echo '<div class="tgwc-memberships-content">';
		echo '<h3>' . esc_html__( 'Your Content', 'customize-my-account-page-for-woocommerce' ) . '</h3>';
		echo '<table class="shop_table tgwc-content-table"><thead><tr>';
		echo '<th>' . esc_html__( 'Title', 'customize-my-account-page-for-woocommerce' ) . '</th>';
		echo '<th>' . esc_html__( 'Type', 'customize-my-account-page-for-woocommerce' ) . '</th>';
		echo '<th>' . esc_html__( 'Excerpt', 'customize-my-account-page-for-woocommerce' ) . '</th>';
		echo '<th></th>';
		echo '</tr></thead><tbody>';

		foreach ( $content_items as $item ) {
			$type_label = get_post_type_object( $item['type'] );
			$type_label = $type_label ? $type_label->labels->singular_name : ucfirst( $item['type'] );

			echo '<tr>';
			echo '<td><a href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['title'] ) . '</a></td>';
			echo '<td>' . esc_html( $type_label ) . '</td>';
			echo '<td>' . esc_html( $item['excerpt'] ) . '</td>';
			echo '<td><a href="' . esc_url( $item['url'] ) . '" class="woocommerce-button button">' . esc_html__( 'View', 'customize-my-account-page-for-woocommerce' ) . '</a></td>';
			echo '</tr>';
		}

		echo '</tbody></table></div>';
	}

	/**
	 * Render combined products from all memberships.
	 *
	 * Shows products the user has access to across all their memberships,
	 * deduplicated by product ID.
	 *
	 * @since 2.1.0
	 *
	 * @param \WC_Memberships_User_Membership[] $memberships User memberships.
	 * @return void
	 */
	private function render_combined_products( $memberships ) {
		$products = array();
		$seen_ids = array();

		foreach ( $memberships as $membership ) {
			$plan = $membership->get_plan();
			if ( ! $plan || ! $membership->is_active() ) {
				continue;
			}

			$rules = $plan->get_product_restriction_rules();
			if ( empty( $rules ) ) {
				continue;
			}

			foreach ( $rules as $rule ) {
				$object_ids = $rule->get_object_ids();
				foreach ( $object_ids as $object_id ) {
					if ( isset( $seen_ids[ $object_id ] ) ) {
						continue;
					}
					$seen_ids[ $object_id ] = true;

					$product = wc_get_product( $object_id );
					if ( ! $product || 'publish' !== $product->get_status() ) {
						continue;
					}

					$products[] = array(
						'id'    => $object_id,
						'name'  => $product->get_name(),
						'price' => $product->get_price_html(),
						'url'   => $product->get_permalink(),
					);
				}
			}
		}

		if ( empty( $products ) ) {
			return;
		}

		echo '<div class="tgwc-memberships-products">';
		echo '<h3>' . esc_html__( 'Member Products', 'customize-my-account-page-for-woocommerce' ) . '</h3>';
		echo '<table class="shop_table tgwc-products-table"><thead><tr>';
		echo '<th>' . esc_html__( 'Product', 'customize-my-account-page-for-woocommerce' ) . '</th>';
		echo '<th>' . esc_html__( 'Price', 'customize-my-account-page-for-woocommerce' ) . '</th>';
		echo '<th></th>';
		echo '</tr></thead><tbody>';

		foreach ( $products as $item ) {
			echo '<tr>';
			echo '<td><a href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['name'] ) . '</a></td>';
			echo '<td>' . wp_kses_post( $item['price'] ) . '</td>';
			echo '<td><a href="' . esc_url( $item['url'] ) . '" class="woocommerce-button button">' . esc_html__( 'View', 'customize-my-account-page-for-woocommerce' ) . '</a></td>';
			echo '</tr>';
		}

		echo '</tbody></table></div>';
	}

	/**
	 * Ensure the memberships list section is always rendered when visiting the
	 * members-area endpoint without a specific membership ID.
	 *
	 * @since 2.1.0
	 *
	 * @param array $args Section arguments.
	 * @return array
	 */
	public function force_memberships_list( $args ) {
		// Ensure pagination shows all memberships.
		if ( is_array( $args ) && isset( $args['per_page'] ) ) {
			$args['per_page'] = max( (int) $args['per_page'], 20 );
		}
		return $args;
	}

	/**
	 * Output front-end CSS to clean up the memberships table on the account page.
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function output_membership_css() {
		if ( ! function_exists( 'is_account_page' ) || ! is_account_page() ) {
			return;
		}
		?>
		<style id="tgwc-memberships-compat">
			/* Memberships table cleanup */
			.woocommerce-MyAccount-content .my_account_memberships {
				width: 100%;
				border-collapse: collapse;
			}

			.woocommerce-MyAccount-content .my_account_memberships th,
			.woocommerce-MyAccount-content .my_account_memberships td {
				padding: 10px 12px;
				text-align: left;
				vertical-align: middle;
				border-bottom: 1px solid #e5e5e5;
			}

			.woocommerce-MyAccount-content .my_account_memberships th {
				font-weight: 600;
			}

			.woocommerce-MyAccount-content .my_account_memberships tbody tr:last-child td {
				border-bottom: none;
			}

			.woocommerce-MyAccount-content .my_account_memberships .membership-actions a {
				display: inline-block;
				margin-right: 8px;
			}

			.woocommerce-MyAccount-content .my_account_memberships .membership-actions a:last-child {
				margin-right: 0;
			}

			/* Members area sub-navigation alignment */
			.woocommerce-MyAccount-content .my-membership-tabs {
				list-style: none;
				margin: 0 0 1.5em;
				padding: 0;
				display: flex;
				gap: 0;
				border-bottom: 2px solid #e5e5e5;
			}

			.woocommerce-MyAccount-content .my-membership-tabs li {
				margin: 0;
			}

			.woocommerce-MyAccount-content .my-membership-tabs li a {
				display: block;
				padding: 8px 16px;
				text-decoration: none;
				color: inherit;
				border-bottom: 2px solid transparent;
				margin-bottom: -2px;
				transition: border-color 0.2s ease, color 0.2s ease;
			}

			.woocommerce-MyAccount-content .my-membership-tabs li.active a,
			.woocommerce-MyAccount-content .my-membership-tabs li a:hover {
				border-bottom-color: currentColor;
			}

			/* Unified members area sections */
			.tgwc-unified-members-area h3 {
				margin: 1.5em 0 0.75em;
				font-size: 1.15em;
				font-weight: 600;
			}

			.tgwc-unified-members-area h3:first-child {
				margin-top: 0;
			}

			.tgwc-unified-members-area .shop_table {
				width: 100%;
				border-collapse: collapse;
				margin-bottom: 1.5em;
			}

			.tgwc-unified-members-area .shop_table th,
			.tgwc-unified-members-area .shop_table td {
				padding: 10px 12px;
				text-align: left;
				vertical-align: middle;
				border-bottom: 1px solid #e5e5e5;
			}

			.tgwc-unified-members-area .shop_table th {
				font-weight: 600;
			}

			.tgwc-unified-members-area .shop_table tbody tr:last-child td {
				border-bottom: none;
			}

			.tgwc-unified-members-area .shop_table a {
				color: inherit;
			}

			.tgwc-unified-members-area .tgwc-discounts-table td:nth-child(2) {
				font-weight: 600;
			}

			/* Responsive */
			@media screen and (max-width: 768px) {
				.woocommerce-MyAccount-content .my_account_memberships,
				.tgwc-unified-members-area .shop_table {
					display: block;
					overflow-x: auto;
					-webkit-overflow-scrolling: touch;
				}
			}
		</style>
		<?php
	}

	/**
	 * WC members area navigation.
	 *
	 * @since 0.4.2
	 * @return void
	 */
	public function wc_membership_navigation() {
		if ( empty( $this->members_area ) ) {
			return;
		}

		$user_membership = $this->members_area->get_members_area_user_membership();

		if ( empty( $user_membership ) || 'members-area' !== tgwc_get_current_endpoint() ) {
			return;
		}

		remove_all_actions( 'tgwc_my_account_menu_item' );

		$members_area_nav_items = $this->members_area->get_members_area_navigation_items( $user_membership->get_plan() );

		foreach ( $members_area_nav_items as $key => $value ) {
			$endpoint = array(
				'slug'  => $key,
				'class' => $value['class'],
				'url'   => $value['url'],
				'label' => $value['label'],
			);
			wc_get_template(
				'frontend/custom-item.php',
				$endpoint,
				TGWC_TEMPLATE_PATH,
				TGWC_TEMPLATE_PATH
			);
		}
	}
}
