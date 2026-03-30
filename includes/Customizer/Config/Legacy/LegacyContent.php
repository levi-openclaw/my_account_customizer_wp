<?php
/**
 * Legacy configs.
 *
 * @package ThemeGrill\WoocommerceCustomizer\Customizer\Config\Legacy
 * @since   2.0.0
 */

namespace ThemeGrill\WoocommerceCustomizer\Customizer\Config\Legacy;

defined( 'ABSPATH' ) || exit;

class LegacyContent {

	/**
	 * Content Controls.
	 *
	 * @return void
	 */
	public static function content_controls() {
		$controls[] = array(
			'id'      => 'tgwc_customize[legacy][content][label]',
			'setting' => array(
				'default' => '',
			),
			'control' => array(
				'label'   => esc_html__( 'Content', 'customize-my-account-page-for-woocommerce' ),
				'section' => 'tgwc_customize[legacy]',
				'type'    => 'tgwc-label',
				'class'   => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\Label',
			),
		);

		// $controls[] = array(
		// 'id'      => 'tgwc_customize[content][background_color]',
		// 'setting' => array(
		// 'default' => '',
		// ),
		// 'control' => array(
		// 'label'       => esc_html__( 'Background color', 'customize-my-account-page-for-woocommerce' ),
		// 'description' => esc_html__( 'Choose Content Background color', 'customize-my-account-page-for-woocommerce' ),
		// 'section'     => 'tgwc_customize[legacy]',
		// 'class'       => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\Color',
		// 'type'        => 'tgwc-color',
		// 'custom_args' => array(
		// 'alpha' => true,
		// ),
		// ),
		// );

		$controls[] = array(
			'id'      => 'tgwc_customize[content][margin]',
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
				'description' => esc_html__( 'Set Content Margin', 'customize-my-account-page-for-woocommerce' ),
				'section'     => 'tgwc_customize[legacy]',
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
			'id'      => 'tgwc_customize[content][padding]',
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
				'description' => esc_html__( 'Set Content Padding', 'customize-my-account-page-for-woocommerce' ),
				'section'     => 'tgwc_customize[legacy]',
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

		return $controls;
	}
}
