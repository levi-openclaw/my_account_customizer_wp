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

			/* Responsive memberships table */
			@media screen and (max-width: 768px) {
				.woocommerce-MyAccount-content .my_account_memberships {
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
