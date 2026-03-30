<?php
/**
 * Navigation configs.
 *
 * @package ThemeGrill\WoocommerceCustomizer\Customizer\Config
 * @since   0.1.0
 */

namespace ThemeGrill\WoocommerceCustomizer\Customizer\Config;

defined( 'ABSPATH' ) || exit;

class Navigation {
	/**
	 * Instance variable.
	 *
	 * @since 0.1.0
	 * @static
	 *
	 * @var ThemeGrill\WoocommerceCustomizer\Customizer\Config\Navigation
	 */
	private static $instance = null;

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 */
	private function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialize and get instance of the class.
	 *
	 * @since 0.1.0
	 * @static
	 *
	 * @return ThemeGrill\WoocommerceCustomizer\Customizer\Config\Navigation
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
	 * @since 0.1.0
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
	 * @since 0.1.0
	 *
	 * @param array $sections List of sections.
	 * @return array Modified list of sections.
	 */
	public function add_sections( $sections ) {
		$sections[] = array(
			'id'       => 'tgwc_customize[navigation]',
			'title'    => esc_html__( 'Navigation Menu', 'customize-my-account-page-for-woocommerce' ),
			'priority' => 160,
		);

		return $sections;
	}


	/**
	 * Add controls.
	 *
	 * @since 0.1.0
	 *
	 * @param array $controls List of controls.
	 * @return array Modified list of controls.
	 */
	public function add_controls( $controls ) {
		$controls = array_merge( $controls, $this->general_controls() );

		return $controls;
	}

	/**
	 * General section controls.
	 *
	 * @since 0.1.0
	 *
	 * @return array General section controls.
	 */
	private function general_controls() {

		$controls[] = array(
			'id'      => 'tgwc_customize[navigation][font_size]',
			'setting' => array(
				'default'           => '16',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'control' => array(
				'label'       => esc_html__( 'Font Size', 'customize-my-account-page-for-woocommerce' ),
				'description' => esc_html__( 'Adjust the size of menu text', 'customize-my-account-page-for-woocommerce' ),
				'section'     => 'tgwc_customize[navigation]',
				'type'        => 'tgwc-slider',
				'class'       => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\Slider',
				'input_attrs' => array(
					'min'  => 12,
					'max'  => 40,
					'step' => 1,
				),
			),
		);

		$controls[] = array(
			'id'      => 'tgwc_customize[navigation][show_icon]',
			'setting' => array(
				'default'           => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			),
			'control' => array(
				'label'       => esc_html__( 'Show Icons', 'customize-my-account-page-for-woocommerce' ),
				'description' => esc_html__( 'Display icons next to menu items', 'customize-my-account-page-for-woocommerce' ),
				'section'     => 'tgwc_customize[navigation]',
				'type'        => 'tgwc-toggle',
				'class'       => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\Toggle',
			),
		);

		$controls[] = array(
			'id'      => 'tgwc_customize[navigation][group_accordion_default_state]',
			'setting' => array(
				'default'           => 'expanded',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'control' => array(
				'label'           => esc_html__( 'Grouped Menu Sections', 'customize-my-account-page-for-woocommerce' ),
				'description'     => esc_html__( 'Should menu sections start open or closed?', 'customize-my-account-page-for-woocommerce' ),
				'section'         => 'tgwc_customize[navigation]',
				'type'            => 'tgwc-buttonset',
				'class'           => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\ButtonSet',
				'choices'         => array(
					'collapsed' => array(
						'name' => esc_html__( 'Closed', 'customize-my-account-page-for-woocommerce' ),
					),
					'expanded'  => array(
						'name' => esc_html__( 'Opened', 'customize-my-account-page-for-woocommerce' ),
					),
				),
				'active_callback' => function ( $control ) {
					$manager = $control->setting->manager;

					if ( ! $manager->get_control( 'tgwc_customize[layout][menu_position]' ) ) {
						return;
					}

					$menu_position = $manager->get_control( 'tgwc_customize[layout][menu_position]' )->value();

					$group = tgwc_get_endpoints_by_type( 'group' );

					return 'tab' !== $menu_position && count( $group ) > 0;
				},
			),
		);

		$controls[] = array(
			'id'      => 'tgwc_customize[navigation][show_logout_btn]',
			'setting' => array(
				'default'           => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			),
			'control' => array(
				'label'       => esc_html__( 'Logout Button', 'customize-my-account-page-for-woocommerce' ),
				'description' => esc_html__( 'Include logout option in the menu. (Enabling the Logout Button disables the logout link endpoint.)', 'customize-my-account-page-for-woocommerce' ),
				'section'     => 'tgwc_customize[navigation]',
				'type'        => 'tgwc-toggle',
				'class'       => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\Toggle',
			),
		);

		return $controls;
	}
}
