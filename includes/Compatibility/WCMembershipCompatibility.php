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

		// Build tab sections. Content comes first if available.
		$has_content = $this->memberships_have_content( $user_memberships );

		$tabs = array();

		if ( $has_content ) {
			$tabs['content'] = array(
				'label'    => __( 'Content', 'customize-my-account-page-for-woocommerce' ),
				'callback' => array( $this, 'render_combined_content' ),
			);
		}

		$tabs['memberships'] = array(
			'label'    => __( 'Memberships', 'customize-my-account-page-for-woocommerce' ),
			'callback' => array( $this, 'render_memberships_overview' ),
		);
		$tabs['discounts'] = array(
			'label'    => __( 'Discounts', 'customize-my-account-page-for-woocommerce' ),
			'callback' => array( $this, 'render_combined_discounts' ),
		);
		$tabs['products'] = array(
			'label'    => __( 'Products', 'customize-my-account-page-for-woocommerce' ),
			'callback' => array( $this, 'render_combined_products' ),
		);

		$tabs = apply_filters( 'tgwc_membership_unified_tabs', $tabs );

		echo '<div class="tgwc-unified-members-area">';

		// --- Tab navigation ---
		echo '<ul class="tgwc-membership-tabs">';
		$first = true;
		foreach ( $tabs as $tab_id => $tab ) {
			$active = $first ? ' active' : '';
			echo '<li class="tgwc-membership-tab' . esc_attr( $active ) . '">';
			echo '<a href="#tgwc-tab-' . esc_attr( $tab_id ) . '" data-tab="' . esc_attr( $tab_id ) . '">' . esc_html( $tab['label'] ) . '</a>';
			echo '</li>';
			$first = false;
		}
		echo '</ul>';

		// --- Tab panels ---
		$first = true;
		foreach ( $tabs as $tab_id => $tab ) {
			$display = $first ? '' : ' style="display:none;"';
			echo '<div id="tgwc-tab-' . esc_attr( $tab_id ) . '" class="tgwc-membership-tab-panel"' . $display . '>';
			call_user_func( $tab['callback'], $user_memberships );
			echo '</div>';
			$first = false;
		}

		// --- Tab switching JS ---
		?>
		<script id="tgwc-membership-tabs-v2.1.7">
		(function() {
			var tabs = document.querySelectorAll('.tgwc-membership-tabs .tgwc-membership-tab a');
			var panels = document.querySelectorAll('.tgwc-membership-tab-panel');
			tabs.forEach(function(tab) {
				tab.addEventListener('click', function(e) {
					e.preventDefault();
					var target = this.getAttribute('data-tab');
					tabs.forEach(function(t) { t.parentElement.classList.remove('active'); });
					panels.forEach(function(p) { p.style.display = 'none'; });
					this.parentElement.classList.add('active');
					var panel = document.getElementById('tgwc-tab-' + target);
					if (panel) panel.style.display = '';
				});
			});
		})();
		</script>
		<?php

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
	 * Render combined discounts across all memberships as product cards.
	 *
	 * Collects all discount rules, resolves them to actual WooCommerce
	 * products (expanding category rules to their products), deduplicates
	 * by product ID keeping only the best discount, and renders a card
	 * grid with image, name, original price, member price, and badge.
	 *
	 * @since 2.1.0
	 *
	 * @param \WC_Memberships_User_Membership[] $memberships User memberships.
	 * @return void
	 */
	private function render_combined_discounts( $memberships ) {
		// Step 1: Collect best discount per product ID across all memberships.
		$product_discounts = array(); // product_id => { amount, is_pct, plan_name }

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
				$is_pct     = ( false !== strpos( $type, 'percentage' ) );

				// Resolve object IDs to actual product IDs.
				$product_ids = array();

				if ( empty( $object_ids ) ) {
					// Rule applies to ALL products — query published products.
					$all_products = wc_get_products( array(
						'status' => 'publish',
						'limit'  => 100,
						'return' => 'ids',
					) );
					$product_ids = $all_products;
				} else {
					foreach ( $object_ids as $oid ) {
						// Check if this is a product.
						$product = wc_get_product( $oid );
						if ( $product && 'publish' === $product->get_status() ) {
							$product_ids[] = $oid;
							continue;
						}

						// Check if it's a product category term.
						$term = get_term( $oid, 'product_cat' );
						if ( $term && ! is_wp_error( $term ) ) {
							$cat_products = wc_get_products( array(
								'status'   => 'publish',
								'category' => array( $term->slug ),
								'limit'    => 100,
								'return'   => 'ids',
							) );
							$product_ids = array_merge( $product_ids, $cat_products );
						}
					}
				}

				// Register best discount per product.
				foreach ( $product_ids as $pid ) {
					$pid = (int) $pid;
					if ( ! isset( $product_discounts[ $pid ] ) ) {
						$product_discounts[ $pid ] = array(
							'amount'    => $amount,
							'is_pct'    => $is_pct,
							'plan_name' => $plan_name,
						);
					} else {
						// Keep the better discount (higher percentage wins; if mixed types, percentage 100 > fixed).
						$existing = $product_discounts[ $pid ];
						if ( $is_pct && $amount > $existing['amount'] ) {
							$product_discounts[ $pid ] = array(
								'amount'    => $amount,
								'is_pct'    => $is_pct,
								'plan_name' => $plan_name,
							);
						} elseif ( ! $existing['is_pct'] && ! $is_pct && $amount > $existing['amount'] ) {
							$product_discounts[ $pid ] = array(
								'amount'    => $amount,
								'is_pct'    => false,
								'plan_name' => $plan_name,
							);
						}
					}
				}
			}
		}

		if ( empty( $product_discounts ) ) {
			echo '<p>' . esc_html__( 'No discounts available at this time.', 'customize-my-account-page-for-woocommerce' ) . '</p>';
			return;
		}

		// Step 2: Render using proven card pattern — scoped styles + clean HTML.
		?>
		<style id="tgwc-discount-cards-v2.1.7">
			/* tgwc-discount-cards v2.1.7 */
			.tgwc-dg { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; align-items: start; align-content: start; }
			.tgwc-dc { display: flex; align-items: center; gap: 14px; padding: 8px 14px 8px 8px; border: 2px solid rgba(23,25,21,0.1); border-radius: 8px; background: #fff; min-width: 0; overflow: hidden; cursor: pointer; text-decoration: none; color: inherit; box-sizing: border-box; transition: border-color .2s, background .2s; }
			.tgwc-dc:hover { border-color: rgba(162,150,74,0.25); background: rgba(162,150,74,0.08); }
			.tgwc-dc-thumb { flex: 0 0 80px; width: 80px; height: 58px; border-radius: 5px; overflow: hidden; background: rgba(23,25,21,0.05); }
			.tgwc-dc-thumb img { width: 100% !important; height: 100% !important; object-fit: contain !important; display: block !important; margin: 0 !important; padding: 0 !important; max-width: none !important; }
			.tgwc-dc-body { flex: 1; display: flex; align-items: center; justify-content: space-between; gap: 10px; min-width: 0; }
			.tgwc-dc-name { font-family: 'DM Sans', -apple-system, sans-serif; font-size: 13px; font-weight: 600; color: #171915; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; min-width: 0; }
			.tgwc-dc-end { flex: 0 0 auto; display: flex; flex-direction: column; align-items: flex-end; gap: 2px; white-space: nowrap; }
			.tgwc-dc-badge { display: inline-block; background: #171915; color: #fff; padding: 2px 10px; font-family: 'DM Sans', sans-serif; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; border-radius: 3px; line-height: 1.6; }
			.tgwc-dc-badge-free { background: #a2964a; }
			.tgwc-dc-prices { display: flex; align-items: baseline; gap: 5px; font-family: 'DM Sans', sans-serif; font-size: 13px; }
			.tgwc-dc-prices del { color: rgba(23,25,21,0.4); font-weight: 400; font-size: 12px; }
			.tgwc-dc-prices strong { font-weight: 700; color: #171915; }
			.tgwc-dc-via { font-family: 'DM Sans', sans-serif; font-size: 10px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.06em; color: rgba(23,25,21,0.45); }
			.tgwc-dc-cart { display: inline-block; margin-top: 4px; padding: 4px 14px; border-radius: 4px; background: #a2964a; color: #fff; font-family: 'DM Sans', sans-serif; font-size: 11px; font-weight: 600; text-decoration: none; text-transform: uppercase; letter-spacing: 0.04em; transition: background .15s; }
			.tgwc-dc-cart:hover { background: #8a7f3e; }
			@media (max-width: 768px) { .tgwc-dg { grid-template-columns: 1fr; } }
		</style>
		<?php
		echo '<div class="tgwc-dg">';

		foreach ( $product_discounts as $product_id => $disc ) {
			$product = wc_get_product( $product_id );
			if ( ! $product ) {
				continue;
			}

			$name          = $product->get_name();
			$permalink     = $product->get_permalink();
			$regular_price = (float) $product->get_regular_price();
			$thumb_id      = $product->get_image_id();
			$thumb_url     = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'thumbnail' ) : wc_placeholder_img_src( 'thumbnail' );
			$add_to_cart   = $product->add_to_cart_url();

			if ( $disc['is_pct'] ) {
				if ( $disc['amount'] >= 100 ) {
					$badge_text   = __( 'FREE', 'customize-my-account-page-for-woocommerce' );
					$badge_class  = 'tgwc-dc-badge tgwc-dc-badge-free';
					$member_price = 0;
				} else {
					$badge_text   = round( $disc['amount'] ) . '% ' . __( 'off', 'customize-my-account-page-for-woocommerce' );
					$badge_class  = 'tgwc-dc-badge';
					$member_price = $regular_price * ( 1 - $disc['amount'] / 100 );
				}
			} else {
				$badge_text   = wc_price( $disc['amount'] ) . ' ' . __( 'off', 'customize-my-account-page-for-woocommerce' );
				$badge_class  = 'tgwc-dc-badge';
				$member_price = max( 0, $regular_price - $disc['amount'] );
			}

			echo '<div class="tgwc-dc" onclick="window.location.href=\'' . esc_url( $permalink ) . '\'">';
				echo '<div class="tgwc-dc-thumb"><img src="' . esc_url( $thumb_url ) . '" alt="" /></div>';
				echo '<div class="tgwc-dc-body">';
					echo '<span class="tgwc-dc-name">' . esc_html( $name ) . '</span>';
					echo '<span class="tgwc-dc-end">';
						echo '<span class="' . esc_attr( $badge_class ) . '">' . esc_html( $badge_text ) . '</span>';
						echo '<span class="tgwc-dc-prices">';
						if ( $regular_price > 0 ) {
							echo '<del>' . wp_kses_post( wc_price( $regular_price ) ) . '</del>';
						}
						echo '<strong>' . wp_kses_post( wc_price( $member_price ) ) . '</strong>';
						echo '</span>';
						echo '<span class="tgwc-dc-via">' . esc_html( $disc['plan_name'] ) . '</span>';
						if ( $product->is_purchasable() && $product->is_in_stock() ) {
							echo '<a href="' . esc_url( $add_to_cart ) . '" class="tgwc-dc-cart" onclick="event.stopPropagation();">' . esc_html__( 'Add to Cart', 'customize-my-account-page-for-woocommerce' ) . '</a>';
						}
					echo '</span>';
				echo '</div>';
			echo '</div>';
		}

		echo '</div>';
	}

	/**
	 * Check whether any of the user's memberships have restricted content.
	 *
	 * Lightweight check that returns true as soon as at least one valid
	 * content item is found (does not build the full list).
	 *
	 * @since 2.1.0
	 *
	 * @param \WC_Memberships_User_Membership[] $memberships User memberships.
	 * @return bool
	 */
	private function memberships_have_content( $memberships ) {
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
				$object_ids = $rule->get_object_ids();
				foreach ( $object_ids as $object_id ) {
					$post = get_post( $object_id );
					if ( $post && 'publish' === $post->post_status ) {
						return true;
					}
				}
			}
		}
		return false;
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
		echo '<th>' . esc_html__( 'Excerpt', 'customize-my-account-page-for-woocommerce' ) . '</th>';
		echo '<th></th>';
		echo '</tr></thead><tbody>';

		foreach ( $content_items as $item ) {
			echo '<tr>';
			echo '<td><a href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['title'] ) . '</a></td>';
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
		<style id="tgwc-memberships-compat-v2.1.7">
			/* tgwc-memberships-compat v2.1.7 */
			/* ================================================================
			   Gamut Design Tokens
			   ================================================================ */
			.tgwc-unified-members-area,
			.woocommerce-MyAccount-content .my_account_memberships {
				--brand-olive: #a2964a;
				--brand-olive-hover: #8a7f3e;
				--brand-olive-light: rgba(162, 150, 74, 0.08);
				--brand-olive-border: rgba(162, 150, 74, 0.25);
				--grass-100: #171915;
				--grass-80: rgba(23, 25, 21, 0.8);
				--grass-50: rgba(23, 25, 21, 0.5);
				--grass-20: rgba(23, 25, 21, 0.2);
				--grass-10: rgba(23, 25, 21, 0.1);
				--grass-05: rgba(23, 25, 21, 0.05);
				--font-serif: 'Cormorant Garamond', Georgia, serif;
				--font-sans: 'DM Sans', -apple-system, sans-serif;
			}

			/* ================================================================
			   Tab Navigation
			   ================================================================ */
			.tgwc-membership-tabs {
				list-style: none;
				margin: 0 0 24px;
				padding: 0;
				display: flex;
				gap: 0;
				border-bottom: 2px solid var(--grass-10, rgba(23,25,21,0.1));
			}

			.tgwc-membership-tabs li {
				margin: 0;
			}

			.tgwc-membership-tabs li a {
				display: block;
				padding: 7px 12px;
				text-decoration: none;
				color: var(--grass-50, rgba(23,25,21,0.5));
				font-family: var(--font-sans, 'DM Sans', sans-serif);
				font-size: 13px;
				font-weight: 500;
				border-bottom: 2px solid transparent;
				margin-bottom: -2px;
				border-radius: 6px 6px 0 0;
				transition: color 0.2s ease, border-color 0.2s ease;
			}

			.tgwc-membership-tabs li a:hover {
				color: var(--grass-80, rgba(23,25,21,0.8));
			}

			.tgwc-membership-tabs li.active a {
				color: var(--brand-olive, #a2964a);
				border-bottom-color: var(--brand-olive, #a2964a);
				font-weight: 600;
			}

			/* ================================================================
			   Section Titles
			   ================================================================ */
			.tgwc-unified-members-area h3 {
				font-family: var(--font-serif, 'Cormorant Garamond', serif);
				font-size: 26px;
				font-weight: 600;
				color: var(--grass-100, #171915);
				margin: 0 0 16px;
			}

			/* ================================================================
			   Tables (Memberships overview, Content, Products)
			   ================================================================ */
			.tgwc-unified-members-area .shop_table,
			.woocommerce-MyAccount-content .my_account_memberships {
				width: 100%;
				border-collapse: collapse;
				margin-bottom: 1.5em;
				font-family: var(--font-sans, 'DM Sans', sans-serif);
			}

			.tgwc-unified-members-area .shop_table th,
			.woocommerce-MyAccount-content .my_account_memberships th {
				font-size: 11px;
				font-weight: 600;
				text-transform: uppercase;
				letter-spacing: 0.06em;
				color: var(--grass-50, rgba(23,25,21,0.5));
				border-bottom: 2px solid var(--grass-10, rgba(23,25,21,0.1));
				padding: 10px 12px;
				text-align: left;
			}

			.tgwc-unified-members-area .shop_table td,
			.woocommerce-MyAccount-content .my_account_memberships td {
				padding: 12px;
				text-align: left;
				vertical-align: middle;
				border-bottom: 1px solid var(--grass-05, rgba(23,25,21,0.05));
				color: var(--grass-80, rgba(23,25,21,0.8));
				font-size: 14px;
			}

			.tgwc-unified-members-area .shop_table tbody tr:last-child td,
			.woocommerce-MyAccount-content .my_account_memberships tbody tr:last-child td {
				border-bottom: none;
			}

			.tgwc-unified-members-area .shop_table a {
				color: var(--brand-olive, #a2964a);
				text-decoration: none;
				font-weight: 500;
			}

			.tgwc-unified-members-area .shop_table a:hover {
				color: var(--brand-olive-hover, #8a7f3e);
			}

			/* Table view button — olive primary */
			.tgwc-unified-members-area .shop_table .woocommerce-button.button {
				padding: 10px 28px;
				border: none;
				border-radius: 6px;
				background: var(--brand-olive, #a2964a);
				color: #fff;
				font-family: var(--font-sans, 'DM Sans', sans-serif);
				font-size: 14px;
				font-weight: 600;
				text-decoration: none;
				transition: background 0.15s ease;
				display: inline-block;
			}

			.tgwc-unified-members-area .shop_table .woocommerce-button.button:hover {
				background: var(--brand-olive-hover, #8a7f3e);
				color: #fff;
			}

			/* ================================================================
			   Responsive
			   ================================================================ */
			@media screen and (max-width: 768px) {
				.woocommerce-MyAccount-content .my_account_memberships,
				.tgwc-unified-members-area .shop_table {
					display: block;
					overflow-x: auto;
					-webkit-overflow-scrolling: touch;
				}

				.tgwc-membership-tabs {
					overflow-x: auto;
					-webkit-overflow-scrolling: touch;
					flex-wrap: nowrap;
				}

				.tgwc-membership-tabs li a {
					white-space: nowrap;
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
