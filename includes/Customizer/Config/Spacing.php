<?php
/**
 * Spacing configs.
 *
 * @package ThemeGrill\WoocommerceCustomizer\Customizer\Config
 * @since   0.1.0
 */

namespace ThemeGrill\WoocommerceCustomizer\Customizer\Config;

defined( 'ABSPATH' ) || exit;

class Spacing {
	/**
	 * Instance variable.
	 *
	 * @since 0.1.0
	 * @static
	 *
	 * @var ThemeGrill\WoocommerceCustomizer\Customizer\Config\Spacing
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
	 * @return ThemeGrill\WoocommerceCustomizer\Customizer\Config\Spacing
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
			'id'       => 'tgwc_customize[spacing]',
			'title'    => esc_html__( 'Spacing', 'customize-my-account-page-for-woocommerce' ),
			'priority' => 180,
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
			'id'      => 'tgwc_customize[spacing][page_container][label]',
			'setting' => array(
				'default' => '',
			),
			'control' => array(
				'label'   => esc_html__( 'Page Container', 'customize-my-account-page-for-woocommerce' ),
				'section' => 'tgwc_customize[spacing]',
				'type'    => 'tgwc-label',
				'class'   => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\Label',
			),
		);

		$controls[] = array(
			'id'      => 'tgwc_customize[spacing][page_container][margin]',
			'setting' => array(
				'default' => array(
					'top'    => 0,
					'right'  => 0,
					'bottom' => 0,
					'left'   => 0,
				),
			),
			'control' => array(
				'label'       => esc_html__( 'Margin', 'customize-my-account-page-for-woocommerce' ),
				'section'     => 'tgwc_customize[spacing]',
				'type'        => 'tgwc-dimension',
				'class'       => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\Dimension',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 250,
					'step' => 1,
				),
				'custom_args' => array(
					'anchor'     => true,
					'responsive' => true,
					'input_type' => 'number',
				),
			),
		);
		$controls[] = array(
			'id'      => 'tgwc_customize[spacing][page_container][padding]',
			'setting' => array(
				'default' => array(
					'top'    => 0,
					'right'  => 0,
					'bottom' => 0,
					'left'   => 0,
				),
			),
			'control' => array(
				'label'       => esc_html__( 'Padding', 'customize-my-account-page-for-woocommerce' ),
				'section'     => 'tgwc_customize[spacing]',
				'type'        => 'tgwc-dimension',
				'class'       => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\Dimension',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 250,
					'step' => 1,
				),
				'custom_args' => array(
					'anchor'     => true,
					'responsive' => true,
					'input_type' => 'number',
				),
			),
		);

		$controls[] = array(
			'id'      => 'tgwc_customize[spacing][menu_container][label]',
			'setting' => array(
				'default' => '',
			),
			'control' => array(
				'label'   => esc_html__( 'Menu Container', 'customize-my-account-page-for-woocommerce' ),
				'section' => 'tgwc_customize[spacing]',
				'type'    => 'tgwc-label',
				'class'   => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\Label',
			),
		);

		$controls[] = array(
			'id'      => 'tgwc_customize[spacing][menu_container][padding]',
			'setting' => array(
				'default' => array(
					'desktop' => array(
						'top'    => '',
						'right'  => '',
						'bottom' => '',
						'left'   => '',
					),
				),
			),
			'control' => array(
				'label'       => esc_html__( 'Padding', 'customize-my-account-page-for-woocommerce' ),
				'section'     => 'tgwc_customize[spacing]',
				'type'        => 'tgwc-dimension',
				'class'       => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\Dimension',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 250,
					'step' => 1,
				),
				'custom_args' => array(
					'anchor'     => true,
					'responsive' => true,
					'input_type' => 'number',
				),
			),
		);

		$controls[] = array(
			'id'      => 'tgwc_customize[spacing][menu_container][margin]',
			'setting' => array(
				'default' => array(
					'desktop' => array(
						'top'    => 20,
						'right'  => 0,
						'bottom' => 20,
						'left'   => 0,
					),
				),
			),
			'control' => array(
				'label'       => esc_html__( 'Margin', 'customize-my-account-page-for-woocommerce' ),
				'section'     => 'tgwc_customize[spacing]',
				'type'        => 'tgwc-dimension',
				'class'       => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\Dimension',
				'input_attrs' => array(
					'min'  => -20,
					'max'  => 250,
					'step' => 1,
				),
				'custom_args' => array(
					'anchor'     => true,
					'responsive' => true,
					'input_type' => 'number',
				),
			),
		);

		$controls[] = array(
			'id'      => 'tgwc_customize[spacing][profile_picture][label]',
			'setting' => array(
				'default' => '',
			),
			'control' => array(
				'label'           => esc_html__( 'Profile Picture', 'customize-my-account-page-for-woocommerce' ),
				'section'         => 'tgwc_customize[spacing]',
				'type'            => 'tgwc-label',
				'class'           => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\Label',
				'active_callback' => function ( $control ) {
					$avatar = wp_parse_args(
						get_option( 'tgwc_settings' ),
						array(
							'custom_avatar' => true,
						)
					);
					return tgwc_string_to_bool( $avatar['custom_avatar'] );
				},
			),
		);

		$controls[] = array(
			'id'      => 'tgwc_customize[spacing][avatar][padding]',
			'setting' => array(
				'default' => array(
					'top'    => 0,
					'right'  => 0,
					'bottom' => 0,
					'left'   => 0,
				),
			),
			'control' => array(
				'label'           => esc_html__( 'Padding', 'customize-my-account-page-for-woocommerce' ),
				'description'     => esc_html__( 'Set Avatar Padding', 'customize-my-account-page-for-woocommerce' ),
				'section'         => 'tgwc_customize[spacing]',
				'type'            => 'tgwc-dimension',
				'class'           => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\Dimension',
				'input_attrs'     => array(
					'min'  => 0,
					'max'  => 250,
					'step' => 1,
				),
				'custom_args'     => array(
					'anchor'     => true,
					'responsive' => true,
					'input_type' => 'number',
				),
				'active_callback' => function ( $control ) {
					$avatar = wp_parse_args(
						get_option( 'tgwc_settings' ),
						array(
							'custom_avatar' => true,
						)
					);
					return tgwc_string_to_bool( $avatar['custom_avatar'] );
				},
			),
		);

		return $controls;
	}
}
