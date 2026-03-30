<?php
/**
 * Legacy configs.
 *
 * @package ThemeGrill\WoocommerceCustomizer\Customizer\Config
 * @since   2.0.0
 */

namespace ThemeGrill\WoocommerceCustomizer\Customizer\Config;

use ThemeGrill\WoocommerceCustomizer\Customizer\Config\Legacy\LegacyAvatar;
use ThemeGrill\WoocommerceCustomizer\Customizer\Config\Legacy\LegacyButton;
use ThemeGrill\WoocommerceCustomizer\Customizer\Config\Legacy\LegacyContent;
use ThemeGrill\WoocommerceCustomizer\Customizer\Config\Legacy\LegacyInputField;
use ThemeGrill\WoocommerceCustomizer\Customizer\Config\Legacy\LegacyNavigation;
use ThemeGrill\WoocommerceCustomizer\Customizer\Config\Legacy\LegacyWrapper;

defined( 'ABSPATH' ) || exit;

class Legacy {
	/**
	 * Instance variable.
	 *
	 * @since 2.0.0
	 * @static
	 *
	 * @var ThemeGrill\WoocommerceCustomizer\Customizer\Config\Legacy
	 */
	private static $instance = null;

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	private function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialize and get instance of the class.
	 *
	 * @since 2.0.0
	 * @static
	 *
	 * @return ThemeGrill\WoocommerceCustomizer\Customizer\Config\Legacy
	 */
	public static function init() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize hooks.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	private function init_hooks() {
		add_filter( 'tgwc_customizer_sections', array( $this, 'add_sections' ) );
		add_filter( 'tgwc_customizer_controls', array( $this, 'add_controls' ) );
	}

	/**
	 * Add sections.
	 *
	 * @since 2.0.0
	 *
	 * @param array $sections List of sections.
	 * @return array Modified list of sections.
	 */
	public function add_sections( $sections ) {
		$sections[] = array(
			'id'       => 'tgwc_customize[legacy]',
			'title'    => esc_html__( 'Legacy Options', 'customize-my-account-page-for-woocommerce' ),
			'priority' => 10620,
		);

		return $sections;
	}

	/**
	 * Add controls.
	 *
	 * @since 2.0.0
	 *
	 * @param array $controls List of controls.
	 * @return array Modified list of controls.
	 */
	public function add_controls( $controls ) {

		$controls = array_merge( $controls, LegacyWrapper::wrapper_controls() );

		$controls = array_merge( $controls, LegacyAvatar::avatar_controls() );

		$controls = array_merge( $controls, LegacyNavigation::navigation_controls() );

		$controls = array_merge( $controls, LegacyContent::content_controls() );

		$controls = array_merge( $controls, LegacyInputField::input_fields_controls() );

		$controls = array_merge( $controls, LegacyButton::button_controls() );

		return $controls;
	}
}
