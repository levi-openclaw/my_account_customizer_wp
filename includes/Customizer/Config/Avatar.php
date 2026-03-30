<?php
/**
 * Avatar configs.
 *
 * @package ThemeGrill\WoocommerceCustomizer\Customizer\Config
 * @since   0.1.0
 */

namespace ThemeGrill\WoocommerceCustomizer\Customizer\Config;

defined( 'ABSPATH' ) || exit;

class Avatar {
	/**
	 * Instance variable.
	 *
	 * @since 0.1.0
	 * @static
	 *
	 * @var Avatar
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
	 * @return Avatar
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
		$avatar = wp_parse_args(
			get_option( 'tgwc_settings' ),
			array(
				'custom_avatar' => true,
			)
		);
		if ( tgwc_string_to_bool( $avatar['custom_avatar'] ) ) {

			$sections[] = array(
				'id'       => 'tgwc_customize[avatar]',
				'title'    => esc_html__( 'Profile Settings', 'customize-my-account-page-for-woocommerce' ),
				'priority' => 170,
			);
		}
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

		$controls[] = array(
			'id'      => 'tgwc_customize[avatar][default]',
			'setting' => array(
				'default' => '',
			),
			'control' => array(
				'label'       => esc_html__( 'Profile Picture Placeholder', 'customize-my-account-page-for-woocommerce' ),
				'description' => esc_html__( 'Default image for users without profile pictures', 'customize-my-account-for-woocommerce' ),
				'section'     => 'tgwc_customize[avatar]',
				'type'        => 'tgwc-background',
				'class'       => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\BackgroundImage',
			),
		);
		$controls[] = array(
			'id'      => 'tgwc_customize[avatar][upload_size_limit]',
			'setting' => array(
				'default'           => '2048',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'control' => array(
				'label'       => esc_html__( 'Profile Picture Size Limit', 'customize-my-account-page-for-woocommerce' ),
				'description' => esc_html__( 'Maximum file size(KB) for uploaded images', 'customize-my-account-page-for-woocommerce' ),
				'section'     => 'tgwc_customize[avatar]',
				'type'        => 'tgwc-slider',
				'class'       => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\Slider',
				'input_attrs' => array(
					'min'  => 10,
					'step' => 1,
					'max'  => 2048,
				),
			),
		);

		$controls[] = array(
			'id'      => 'tgwc_customize[avatar][type]',
			'setting' => array(
				'default'           => 'circle',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'control' => array(
				'label'         => esc_html__( 'Profile Picture Style', 'customize-my-account-page-for-woocommerce' ),
				'section'       => 'tgwc_customize[avatar]',
				'type'          => 'tgwc-image_radio',
				'class'         => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\ImageRadio',
				'display_label' => true,
				'choices'       => array(
					'square' => array(
						'name'   => esc_html__( 'Square', 'customize-my-account-page-for-woocommerce' ),
						'image'  => plugins_url( '/assets/images/square-profile.svg', TGWC_PLUGIN_FILE ),
						'width'  => 100,
						'height' => 100,
					),
					'circle' => array(
						'name'   => esc_html__( 'Circle', 'customize-my-account-page-for-woocommerce' ),
						'image'  => plugins_url( '/assets/images/circle-profile.svg', TGWC_PLUGIN_FILE ),
						'width'  => 100,
						'height' => 100,
					),
				),
			),
		);

		return $controls;
	}
}
