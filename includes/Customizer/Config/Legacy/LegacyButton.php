<?php
/**
 * Legacy configs.
 *
 * @package ThemeGrill\WoocommerceCustomizer\Customizer\Config\Legacy
 * @since   2.0.0
 */

namespace ThemeGrill\WoocommerceCustomizer\Customizer\Config\Legacy;

defined( 'ABSPATH' ) || exit;

class LegacyButton {
	/**
	 * Button controls.
	 *
	 * @return void
	 */
	public static function button_controls() {
		$controls[] = array(
			'id'      => 'tgwc_customize[button][general][label1]',
			'setting' => array(
				'default' => '',
			),
			'control' => array(
				'label'   => esc_html__( 'General', 'customize-my-account-page-for-woocommerce' ),
				'section' => 'tgwc_customize[legacy]',
				'type'    => 'tgwc-label',
				'class'   => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\Label',
			),
		);
		$controls[] = array(
			'id'      => 'tgwc_customize[button][general][font_size]',
			'setting' => array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'control' => array(
				'label'       => esc_html__( 'Font Size', 'customize-my-account-page-for-woocommerce' ),
				'description' => esc_html__( 'Choose font size', 'customize-my-account-page-for-woocommerce' ),
				'section'     => 'tgwc_customize[legacy]',
				'type'        => 'tgwc-slider',
				'class'       => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\Slider',
				'input_attrs' => array(
					'min'  => 12,
					'max'  => 120,
					'step' => 1,
				),
			),
		);

		$controls[] = array(
			'id'      => 'tgwc_customize[button][general][line_height]',
			'setting' => array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'control' => array(
				'label'       => esc_html__( 'Line Height', 'customize-my-account-page-for-woocommerce' ),
				'description' => esc_html__( 'Choose line height', 'customize-my-account-page-for-woocommerce' ),
				'section'     => 'tgwc_customize[legacy]',
				'type'        => 'tgwc-slider',
				'class'       => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\Slider',
				'input_attrs' => array(
					'min'  => 1,
					'max'  => 10,
					'step' => .01,
				),
			),
		);

		$controls[] = array(
			'id'      => 'tgwc_customize[button][normal][label]',
			'setting' => array(
				'default' => '',
			),
			'control' => array(
				'label'   => esc_html__( 'Normal State', 'customize-my-account-page-for-woocommerce' ),
				'section' => 'tgwc_customize[legacy]',
				'type'    => 'tgwc-label',
				'class'   => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\Label',
			),
		);

		$controls[] = array(
			'id'      => 'tgwc_customize[button][normal][border_style]',
			'setting' => array(
				'default' => 'none',
			),
			'control' => array(
				'label'       => esc_html__( 'Border', 'customize-my-account-page-for-woocommerce' ),
				'description' => esc_html__( 'Choose border style', 'customize-my-account-page-for-woocommerce' ),
				'section'     => 'tgwc_customize[legacy]',
				'type'        => 'select',
				'choices'     => array(
					'none'    => esc_html__( 'None', 'customize-my-account-page-for-woocommerce' ),
					'solid'   => esc_html__( 'Solid', 'customize-my-account-page-for-woocommerce' ),
					'dotted'  => esc_html__( 'Dotted', 'customize-my-account-page-for-woocommerce' ),
					'dashed'  => esc_html__( 'Dashed', 'customize-my-account-page-for-woocommerce' ),
					'double'  => esc_html__( 'Double', 'customize-my-account-page-for-woocommerce' ),
					'groove'  => esc_html__( 'Groove', 'customize-my-account-page-for-woocommerce' ),
					'ridge'   => esc_html__( 'Ridge', 'customize-my-account-page-for-woocommerce' ),
					'inset'   => esc_html__( 'Inset', 'customize-my-account-page-for-woocommerce' ),
					'outset'  => esc_html__( 'Outset', 'customize-my-account-page-for-woocommerce' ),
					'hidden'  => esc_html__( 'hidden', 'customize-my-account-page-for-woocommerce' ),
					'inherit' => esc_html__( 'Inherit', 'customize-my-account-page-for-woocommerce' ),
				),
			),
		);

		$controls[] = array(
			'id'      => 'tgwc_customize[button][normal][border_width]',
			'setting' => array(
				'default' => array(
					'top'    => 0,
					'right'  => 0,
					'bottom' => 0,
					'left'   => 0,
				),
			),
			'control' => array(
				'label'           => esc_html__( 'Border Width', 'customize-my-account-page-for-woocommerce' ),
				'description'     => esc_html__( 'Border width description', 'customize-my-account-page-for-woocommerce' ),
				'section'         => 'tgwc_customize[legacy]',
				'type'            => 'tgwc-dimension',
				'class'           => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\Dimension',
				'input_attrs'     => array(
					'min'  => 0,
					'max'  => 250,
					'step' => 1,
				),
				'custom_args'     => array(
					'anchor'     => true,
					'input_type' => 'number',
				),
				'active_callback' => function ( $control ) {
					$manager = $control->setting->manager;
					$border_control = $manager->get_control( 'tgwc_customize[button][normal][border_style]' );
					$border = $border_control->value();
					return 'none' !== $border;
				},
			),
		);

		// $controls[] = array(
		// 'id'      => 'tgwc_customize[button][normal][border_color]',
		// 'setting' => array(
		// 'default' => '',
		// ),
		// 'control' => array(
		// 'label'           => esc_html__( 'Border Color', 'customize-my-account-page-for-woocommerce' ),
		// 'description'     => esc_html__( 'Border color description', 'customize-my-account-page-for-woocommerce' ),
		// 'section'         => 'tgwc_customize[legacy]',
		// 'type'            => 'color',
		// 'active_callback' => function ( $control ) {
		// $manager = $control->setting->manager;
		// $border_control = $manager->get_control( 'tgwc_customize[button][normal][border_style]' );
		// $border = $border_control->value();
		// return 'none' !== $border;
		// },
		// ),
		// );

		$controls[] = array(
			'id'      => 'tgwc_customize[button][hover][label]',
			'setting' => array(
				'default' => '',
			),
			'control' => array(
				'label'   => esc_html__( 'Hover State', 'customize-my-account-page-for-woocommerce' ),
				'section' => 'tgwc_customize[legacy]',
				'type'    => 'tgwc-label',
				'class'   => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\Label',
			),
		);

		$controls[] = array(
			'id'      => 'tgwc_customize[button][hover][border_style]',
			'setting' => array(
				'default' => 'inherit',
			),
			'control' => array(
				'label'       => esc_html__( 'Border', 'customize-my-account-page-for-woocommerce' ),
				'description' => esc_html__( 'Choose background border style', 'customize-my-account-page-for-woocommerce' ),
				'section'     => 'tgwc_customize[legacy]',
				'type'        => 'select',
				'choices'     => array(
					'none'    => esc_html__( 'None', 'customize-my-account-page-for-woocommerce' ),
					'solid'   => esc_html__( 'Solid', 'customize-my-account-page-for-woocommerce' ),
					'dotted'  => esc_html__( 'Dotted', 'customize-my-account-page-for-woocommerce' ),
					'dashed'  => esc_html__( 'Dashed', 'customize-my-account-page-for-woocommerce' ),
					'double'  => esc_html__( 'Double', 'customize-my-account-page-for-woocommerce' ),
					'groove'  => esc_html__( 'Groove', 'customize-my-account-page-for-woocommerce' ),
					'ridge'   => esc_html__( 'Ridge', 'customize-my-account-page-for-woocommerce' ),
					'inset'   => esc_html__( 'Inset', 'customize-my-account-page-for-woocommerce' ),
					'outset'  => esc_html__( 'Outset', 'customize-my-account-page-for-woocommerce' ),
					'hidden'  => esc_html__( 'hidden', 'customize-my-account-page-for-woocommerce' ),
					'inherit' => esc_html__( 'Inherit', 'customize-my-account-page-for-woocommerce' ),
				),
			),
		);

		$controls[] = array(
			'id'      => 'tgwc_customize[button][hover][border_width]',
			'setting' => array(
				'default' => array(
					'top'    => 0,
					'right'  => 0,
					'bottom' => 0,
					'left'   => 0,
				),
			),
			'control' => array(
				'label'           => esc_html__( 'Border Width', 'customize-my-account-page-for-woocommerce' ),
				'description'     => esc_html__( 'Set border width', 'customize-my-account-page-for-woocommerce' ),
				'section'         => 'tgwc_customize[legacy]',
				'type'            => 'tgwc-dimension',
				'class'           => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\Dimension',
				'input_attrs'     => array(
					'min'  => 0,
					'max'  => 250,
					'step' => 1,
				),
				'custom_args'     => array(
					'anchor'     => true,
					'input_type' => 'number',
				),
				'active_callback' => function ( $control ) {
					$manager = $control->setting->manager;
					$border_control = $manager->get_control( 'tgwc_customize[button][hover][border_style]' );
					$border = $border_control->value();
					return 'none' !== $border;
				},
			),
		);

		// $controls[] = array(
		// 'id'      => 'tgwc_customize[button][hover][border_color]',
		// 'setting' => array(
		// 'default' => '',
		// ),
		// 'control' => array(
		// 'label'           => esc_html__( 'Border Color', 'customize-my-account-page-for-woocommerce' ),
		// 'description'     => esc_html__( 'Border color description', 'customize-my-account-page-for-woocommerce' ),
		// 'section'         => 'tgwc_customize[legacy]',
		// 'type'            => 'color',
		// 'active_callback' => function ( $control ) {
		// $manager = $control->setting->manager;
		// $border_control = $manager->get_control( 'tgwc_customize[button][hover][border_style]' );
		// $border = $border_control->value();
		// return 'none' !== $border;
		// },
		// ),
		// );

		$controls[] = array(
			'id'      => 'tgwc_customize[button][general][label2]',
			'setting' => array(
				'default' => '',
			),
			'control' => array(
				'label'   => esc_html__( 'General', 'customize-my-account-page-for-woocommerce' ),
				'section' => 'tgwc_customize[legacy]',
				'type'    => 'tgwc-label',
				'class'   => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\Label',
			),
		);
		$controls[] = array(
			'id'      => 'tgwc_customize[button][general][padding]',
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
				'description' => esc_html__( 'Set Padding', 'customize-my-account-page-for-woocommerce' ),
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
			'id'      => 'tgwc_customize[button][general][margin]',
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
				'description' => esc_html__( 'Set Margin', 'customize-my-account-page-for-woocommerce' ),
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
