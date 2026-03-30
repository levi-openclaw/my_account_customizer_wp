<?php
/**
 * Layout configs.
 *
 * @package ThemeGrill\WoocommerceCustomizer\Customizer\Config
 * @since   0.1.0
 */

namespace ThemeGrill\WoocommerceCustomizer\Customizer\Config;

defined( 'ABSPATH' ) || exit;

class Layout {
	/**
	 * Instance variable.
	 *
	 * @since 0.1.0
	 * @static
	 *
	 * @var ThemeGrill\WoocommerceCustomizer\Customizer\Config\Layout
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
	 * @return ThemeGrill\WoocommerceCustomizer\Customizer\Config\Layout
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
			'id'       => 'tgwc_customize[layout]',
			'title'    => esc_html__( 'Layout & Design', 'customize-my-account-page-for-woocommerce' ),
			'priority' => 150,
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

		$controls[]  = array(
			'id'      => 'tgwc_customize[layout][menu_position]',
			'setting' => array(
				'default'           => 'vertical-left',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'control' => array(
				'label'         => esc_html__( 'Menu Position', 'customize-my-account-for-woocommerce' ),
				'description'   => esc_html__( 'Choose where the navigation menu appears', 'customize-my-account-for-woocommerce' ),
				'section'       => 'tgwc_customize[layout]',
				'type'          => 'tgwc-image_radio',
				'class'         => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\ButtonSet',
				'display_label' => true,
				'choices'       => array(
					'vertical-left'  => array(
						'name'   => esc_html__( 'Vertical Left', 'customize-my-account-page-for-woocommerce' ),
						'image'  => plugins_url( '/assets/images/vertical-left.svg', TGWC_PLUGIN_FILE ),
						'width'  => 100,
						'height' => 100,
					),
					'vertical-right' => array(
						'name'   => esc_html__( 'Vertical Right', 'customize-my-account-page-for-woocommerce' ),
						'image'  => plugins_url( '/assets/images/vertical-right.svg', TGWC_PLUGIN_FILE ),
						'width'  => 100,
						'height' => 100,
					),
					'tab'            => array(
						'name'   => esc_html__( 'Horizontal', 'customize-my-account-page-for-woocommerce' ),
						'image'  => plugins_url( '/assets/images/top-horizontal.svg', TGWC_PLUGIN_FILE ),
						'width'  => 100,
						'height' => 100,
					),
				),
			),
		);
		$menu_styles = array(
			'minimal' => array(
				'name' => esc_html__( 'Default', 'customize-my-account-page-for-woocommerce' ),
			),
			'modern'  => array(
				'name' => esc_html__( 'Modern', 'customize-my-account-page-for-woocommerce' ),
			),
			'classic' => array(
				'name' => esc_html__( 'Classic', 'customize-my-account-page-for-woocommerce' ),
			),
		);

		if ( ! get_option( 'tgwc_initial_started_version', false ) ) {
			$menu_styles = array_merge( $menu_styles, array( 'legacy' => array( 'name' => esc_html__( 'Legacy', 'customize-my-account-page-for-woocommerce' ) ) ) );
		}

		$controls[] = array(
			'id'      => 'tgwc_customize[layout][menu_style]',
			'setting' => array(
				'default'           => 'minimal',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'control' => array(
				'label'       => esc_html__( 'Menu Style', 'customizer-my-account-page-for-woocommerce' ),
				'description' => esc_html__( 'Select the visual appearance of your menu', 'customize-my-account-for-woocommerce' ),
				'section'     => 'tgwc_customize[layout]',
				'type'        => 'tgwc-buttonset',
				'class'       => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\ButtonSet',
				'choices'     => $menu_styles,
			),
		);

		$controls[] = array(
			'id'      => 'tgwc_customize[layout][color_palette]',
			'setting' => array(
				'default' => array(
					'activePalette' => array(
						'minimal' => 'minimal_brick_red',
						'modern'  => 'modern_brick_red',
						'classic' => 'classic_brick_red',
					),
					'palettes'      => tgwc_get_color_palettes(),
				),
			),
			'control' => array(
				'label'       => esc_html__( 'Color palette', 'customize-my-account-page-for-woocommerce' ),
				'section'     => 'tgwc_customize[layout]',
				'type'        => 'tgwc-color_palette',
				'class'       => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\ColorPalette',
				'input_attrs' => array(
					'className' => 'color-palette-container',
					'custom'    => 'true',
				),
			),
		);

		return $controls;
	}
}
