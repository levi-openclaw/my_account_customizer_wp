<?php
/**
 * Legacy configs.
 *
 * @package ThemeGrill\WoocommerceCustomizer\Customizer\Config\Legacy
 * @since   2.0.0
 */

namespace ThemeGrill\WoocommerceCustomizer\Customizer\Config\Legacy;

defined( 'ABSPATH' ) || exit;

class LegacyWrapper {
	/**
	 * Wrapper controls
	 *
	 * @return void
	 */
	public static function wrapper_controls() {
		$controls[] = array(
			'id'      => 'tgwc_customize[legacy][wrapper][label]',
			'setting' => array(
				'default' => '',
			),
			'control' => array(
				'label'   => esc_html__( 'Wrapper', 'customize-my-account-page-for-woocommerce' ),
				'section' => 'tgwc_customize[legacy]',
				'type'    => 'tgwc-label',
				'class'   => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\Label',
			),
		);
		$controls[] = array(
			'id'      => 'tgwc_customize[wrapper][font_family]',
			'setting' => array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'control' => array(
				'label'       => esc_html__( 'Font Family', 'customize-my-account-page-for-woocommerce' ),
				'description' => esc_html__( 'Select a desire Google font.', 'customize-my-account-page-for-woocommerce' ),
				'section'     => 'tgwc_customize[legacy]',
				'class'       => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\Select2',
				'type'        => 'tgwc-select2',
				'input_attrs' => array(
					'data-allow_clear' => true,
					'data-placeholder' => _x( 'Select Font Family&hellip;', 'enhanced select', 'customize-my-account-page-for-woocommerce' ),
				),
				'custom_args' => array(
					'google_font' => true,
				),
			),
		);
		// $controls[] = array(
		// 'id'      => 'tgwc_customize[wrapper][background_color]',
		// 'setting' => array(
		// 'default' => '',
		// ),
		// 'control' => array(
		// 'label'       => esc_html__( 'Background color', 'customize-my-account-page-for-woocommerce' ),
		// 'description' => esc_html__( 'Choose Wrapper Background color', 'customize-my-account-page-for-woocommerce' ),
		// 'section'     => 'tgwc_customize[legacy]',
		// 'class'       => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\Color',
		// 'type'        => 'tgwc-color',
		// 'custom_args' => array(
		// 'alpha' => true,
		// ),
		// ),
		// );
		$controls[] = array(
			'id'      => 'tgwc_customize[wrapper][font_size]',
			'setting' => array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'control' => array(
				'label'       => esc_html__( 'Font Size', 'customize-my-account-page-for-woocommerce' ),
				'description' => esc_html__( 'Customize the font size.', 'customize-my-account-page-for-woocommerce' ),
				'section'     => 'tgwc_customize[legacy]',
				'type'        => 'tgwc-slider',
				'class'       => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\Slider',
				'input_attrs' => array(
					'min'  => 12,
					'max'  => 100,
					'step' => 1,
				),
			),
		);
		return $controls;
	}
}
