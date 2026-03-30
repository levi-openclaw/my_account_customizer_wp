<?php
/**
 * WC Membership for Teams Compatibility.
 *
 * @since 0.4.2
 * @package ThemeGrill\WoocommerceCustomizer\Compatibility
 */

namespace ThemeGrill\WoocommerceCustomizer\Compatibility;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Class WCMembershipForTeamsCompatibility.
 *
 * @since 0.4.2
 */
class WCMembershipForTeamsCompatibility {

	/**
	 * Single instance of this class.
	 *
	 * @since 0.4.2
	 * @var null
	 */
	private static $instance = null;

	/**
	 * Teams Area instance.
	 *
	 * @since 0.4.2
	 * @var null
	 */
	private $teams_area = null;

	/**
	 * Endpoint.
	 *
	 * @since 0.4.2
	 * @var string
	 */
	private $endpoint = 'teams';

	/**
	 * Get instance of WCMembershipForTeamsCompatibility.
	 *
	 * @since 0.4.2
	 * @return WCMembershipForTeamsCompatibility|null
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
	 */
	private function __construct() {
		$this->setup();
		add_filter( 'tgwc_get_endpoints', array( $this, 'modify_endpoints' ) );
		add_action( 'tgwc_my_account_menu_item', array( $this, 'wc_membership_for_teams_navigation' ), 1 );
	}

	/**
	 * Setup.
	 *
	 * @since 0.4.2
	 * @return void
	 */
	private function setup() {
		if ( ! function_exists( 'wc_memberships_for_teams' ) ) {
			return;
		}

		$wc_membership_for_teams_frontend_instance = wc_memberships_for_teams()->get_frontend_instance();

		if ( empty( $wc_membership_for_teams_frontend_instance ) ) {
			return;
		}

		$this->teams_area = $wc_membership_for_teams_frontend_instance->get_teams_area_instance();
		$this->endpoint   = $this->get_endpoint();
	}

	/**
	 * WC membership for teams navigation.
	 *
	 * @since 0.4.2
	 * @return void
	 */
	public function wc_membership_for_teams_navigation() {
		if ( empty( $this->teams_area ) ) {
			return;
		}

		$team = $this->teams_area->get_teams_area_team();

		if ( empty( $team ) || 'teams' !== tgwc_get_current_endpoint() ) {
			return;
		}

		remove_all_actions( 'tgwc_my_account_menu_item' );

		$teams_area_nav_items = $this->teams_area->get_teams_area_navigation_items( $team );

		foreach ( $teams_area_nav_items as $key => $value ) {
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

	/**
	 * Get endpoint.
	 *
	 * @since 0.4.2
	 * @return string
	 */
	private function get_endpoint() {
		$teams = wc_memberships_for_teams_get_teams();
		$path  = 'teams';

		if ( false !== $teams && 1 === count( $teams ) ) {
			$my_account_url = wc_get_page_permalink( 'myaccount' );
			$path           = str_replace( $my_account_url, '', $this->teams_area->get_teams_area_url( $teams[0] ) );
		}

		return untrailingslashit( $path );
	}

	/**
	 * Modify endpoints based on teams.
	 *
	 * @param array $endpoints Endpoints.
	 * @return array
	 */
	public function modify_endpoints( $endpoints ) {
		if ( ! empty( $this->teams_area ) && false !== strpos( $this->endpoint, 'teams/' ) ) {
			$keys  = array_keys( $endpoints );
			$index = array_search( 'teams', $keys, true );

			if ( false !== $index && in_array( $this->endpoint, $keys, true ) ) {
				unset( $endpoints[ $this->endpoint ] );
				unset( $keys[ array_search( $this->endpoint, $keys, true ) ] );

				$keys[ $index ] = $this->endpoint;
				$new_endpoints  = array_combine( $keys, $endpoints );

				if ( false !== $new_endpoints ) {
					$endpoints = $new_endpoints;
				}
			}
		}

		return $endpoints;
	}
}
