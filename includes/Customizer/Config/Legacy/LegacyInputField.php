<?php
/**
 * Legacy configs.
 *
 * @package ThemeGrill\WoocommerceCustomizer\Customizer\Config\Legacy
 * @since   2.0.0
 */

namespace ThemeGrill\WoocommerceCustomizer\Customizer\Config\Legacy;

defined( 'ABSPATH' ) || exit;

class LegacyInputField {
	/**
	 * Input fields.
	 *
	 * @return void
	 */
	public static function input_fields_controls() {
		$controls[] = array(
			'id'      => 'tgwc_customize[legacy][input_field][label]',
			'setting' => array(
				'default' => '',
			),
			'control' => array(
				'label'   => esc_html__( 'Input Field', 'customize-my-account-page-for-woocommerce' ),
				'section' => 'tgwc_customize[legacy]',
				'type'    => 'tgwc-label',
				'class'   => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\Label',
			),
		);
		$controls[] = array(
			'id'      => 'tgwc_customize[input_field][general][label]',
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
			'id'      => 'tgwc_customize[input_field][general][padding]',
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
				'description' => esc_html__( 'Set padding', 'customize-my-account-page-for-woocommerce' ),
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
			'id'      => 'tgwc_customize[input_field][normal][label]',
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
			// General Normal
		$controls[]     = array(
			'id'      => 'tgwc_customize[input_field][general][label]',
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
				'id'      => 'tgwc_customize[input_field][general][padding]',
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
					'description' => esc_html__( 'Set padding', 'customize-my-account-page-for-woocommerce' ),
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
			// Input Normal state
			$controls[] = array(
				'id'      => 'tgwc_customize[input_field][normal][label]',
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
			// $controls[] = array(
			// 'id'      => 'tgwc_customize[input_field][normal][color]',
			// 'setting' => array(
			// 'default' => '',
			// ),
			// 'control' => array(
			// 'label'       => esc_html__( 'Text Color', 'customize-my-account-page-for-woocommerce' ),
			// 'description' => esc_html__( 'Choose text color', 'customize-my-account-page-for-woocommerce' ),
			// 'section'     => 'tgwc_customize[legacy]',
			// 'type'        => 'color',
			// ),
			// );

			// $controls[] = array(
			// 'id'      => 'tgwc_customize[input_field][normal][background_color]',
			// 'setting' => array(
			// 'default' => '',
			// ),
			// 'control' => array(
			// 'label'       => esc_html__( 'Background Color', 'customize-my-account-page-for-woocommerce' ),
			// 'description' => esc_html__( 'Choose background color', 'customize-my-account-page-for-woocommerce' ),
			// 'section'     => 'tgwc_customize[legacy]',
			// 'type'        => 'color',
			// ),
			// );

			$controls[] = array(
				'id'      => 'tgwc_customize[input_field][normal][border_style]',
				'setting' => array(
					'default' => 'inherit',
				),
				'control' => array(
					'label'       => esc_html__( 'Border', 'customize-my-account-page-for-woocommerce' ),
					'description' => esc_html__( 'Choose background border', 'customize-my-account-page-for-woocommerce' ),
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
				'id'      => 'tgwc_customize[input_field][normal][border_width]',
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
						$border_control = $manager->get_control( 'tgwc_customize[input_field][normal][border_style]' );
						$border = $border_control->value();
						return 'none' !== $border;
					},
				),
			);

			// $controls[] = array(
			// 'id'      => 'tgwc_customize[input_field][normal][border_color]',
			// 'setting' => array(
			// 'default' => '',
			// ),
			// 'control' => array(
			// 'label'           => esc_html__( 'Border Color', 'customize-my-account-page-for-woocommerce' ),
			// 'description'     => esc_html__( 'Choose border color', 'customize-my-account-page-for-woocommerce' ),
			// 'section'         => 'tgwc_customize[input_field]',
			// 'type'            => 'color',
			// 'active_callback' => function ( $control ) {
			// $manager = $control->setting->manager;
			// $border_control = $manager->get_control( 'tgwc_customize[input_field][normal][border_style]' );
			// $border = $border_control->value();
			// return 'none' !== $border;
			// },
			// ),
			// );
			// Input Focus

			$controls[] = array(
				'id'      => 'tgwc_customize[input_field][focus][label]',
				'setting' => array(
					'default' => '',
				),
				'control' => array(
					'label'   => esc_html__( 'Focus State', 'customize-my-account-page-for-woocommerce' ),
					'section' => 'tgwc_customize[legacy]',
					'type'    => 'tgwc-label',
					'class'   => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\Label',
				),
			);
			// $controls[] = array(
			// 'id'      => 'tgwc_customize[input_field][focus][color]',
			// 'setting' => array(
			// 'default' => '',
			// ),
			// 'control' => array(
			// 'label'       => esc_html__( 'Text Color', 'customize-my-account-page-for-woocommerce' ),
			// 'description' => esc_html__( 'Choose text color', 'customize-my-account-page-for-woocommerce' ),
			// 'section'     => 'tgwc_customize[legacy]',
			// 'type'        => 'color',
			// ),
			// );

			// $controls[] = array(
			// 'id'      => 'tgwc_customize[input_field][focus][background_color]',
			// 'setting' => array(
			// 'default' => '',
			// ),
			// 'control' => array(
			// 'label'       => esc_html__( 'Background Color', 'customize-my-account-page-for-woocommerce' ),
			// 'description' => esc_html__( 'Choose background color', 'customize-my-account-page-for-woocommerce' ),
			// 'section'     => 'tgwc_customize[legacy]',
			// 'type'        => 'color',
			// ),
			// );

			$controls[] = array(
				'id'      => 'tgwc_customize[input_field][focus][border_style]',
				'setting' => array(
					'default' => 'inherit',
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
				'id'      => 'tgwc_customize[input_field][focus][border_width]',
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
						$border_control = $manager->get_control( 'tgwc_customize[input_field][focus][border_style]' );
						$border = $border_control->value();
						return 'none' !== $border;
					},
				),
			);

			// $controls[] = array(
			// 'id'      => 'tgwc_customize[input_field][focus][border_color]',
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
			// $border_control = $manager->get_control( 'tgwc_customize[input_field][focus][border_style]' );
			// $border = $border_control->value();
			// return 'none' !== $border;
			// },
			// ),
			// );

			// Buttons
			$controls[] = array(
				'id'      => 'tgwc_customize[button][focus][label]',
				'setting' => array(
					'default' => '',
				),
				'control' => array(
					'label'   => esc_html__( 'Button', 'customize-my-account-page-for-woocommerce' ),
					'section' => 'tgwc_customize[legacy]',
					'type'    => 'tgwc-label',
					'class'   => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\Label',
				),
			);
			return $controls;
	}
}
