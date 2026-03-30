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
