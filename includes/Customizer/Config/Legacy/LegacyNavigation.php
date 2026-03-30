<?php
/**
 * Legacy configs.
 *
 * @package ThemeGrill\WoocommerceCustomizer\Customizer\Config\Legacy
 * @since   2.0.0
 */

namespace ThemeGrill\WoocommerceCustomizer\Customizer\Config\Legacy;

defined( 'ABSPATH' ) || exit;

class LegacyNavigation {
	/**
	 * Navigation controls.
	 *
	 * @return void
	 */
	public static function navigation_controls() {
		$controls[] = array(
			'id'      => 'tgwc_customize[legacy][navigation][label]',
			'setting' => array(
				'default' => '',
			),
			'control' => array(
				'label'   => esc_html__( 'Navigation', 'customize-my-account-page-for-woocommerce' ),
				'section' => 'tgwc_customize[legacy]',
				'type'    => 'tgwc-label',
				'class'   => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\Label',
			),
		);

		$controls = array_merge( $controls, self::normal_controls() );
		$controls = array_merge( $controls, self::hover_controls() );
		$controls = array_merge( $controls, self::active_controls() );
		$controls = array_merge( $controls, self::general_controls() );

		return $controls;
	}

	public static function active_controls() {
			$controls[] = array(
				'id'      => 'tgwc_customize[navigation][icon_position]',
				'setting' => array(
					'default'           => 'left',
					'sanitize_callback' => 'sanitize_text_field',
				),
				'control' => array(
					'label'       => esc_html__( 'Icon Position', 'customize-my-account-page-for-woocommerce' ),
					'description' => esc_html__( 'Choose Icon Position', 'customize-my-account-page-for-woocommerce' ),
					'section'     => 'tgwc_customize[legacy]',
					'type'        => 'tgwc-buttonset',
					'class'       => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\ButtonSet',
					'choices'     => array(
						'left'  => array(
							'name' => esc_html__( 'Left', 'customize-my-account-page-for-woocommerce' ),
						),
						'right' => array(
							'name' => esc_html__( 'Right', 'customize-my-account-page-for-woocommerce' ),
						),
					),
				),
			);

			$controls[] = array(
				'id'      => 'tgwc_customize[navigation][active][label]',
				'setting' => array(
					'default' => '',
				),
				'control' => array(
					'label'   => esc_html__( 'Active State', 'customize-my-account-page-for-woocommerce' ),
					'section' => 'tgwc_customize[legacy]',
					'type'    => 'tgwc-label',
					'class'   => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\Label',
				),
			);
			$controls[] = array(
				'id'      => 'tgwc_customize[navigation][active][border_style]',
				'setting' => array(
					'default' => 'inherit',
				),
				'control' => array(
					'label'       => esc_html__( 'Border', 'customize-my-account-page-for-woocommerce' ),
					'description' => esc_html__( 'Select active background border style', 'customize-my-account-page-for-woocommerce' ),
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
				'id'      => 'tgwc_customize[navigation][active][border_width]',
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
					'description'     => esc_html__( 'Set tab active border width', 'customize-my-account-page-for-woocommerce' ),
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
						$border_control = $manager->get_control( 'tgwc_customize[navigation][active][border_style]' );
						$border = $border_control->value();
						return 'none' !== $border;
					},
				),
			);

			return $controls;
	}

	/**
	 * Hover section controls.
	 *
	 * @since 0.1.0
	 *
	 * @return array Hover section controls.
	 */
	public static function hover_controls() {
		$controls[] = array(
			'id'      => 'tgwc_customize[navigation][hover][label]',
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

		// $controls[] = array(
		// 'id'      => 'tgwc_customize[navigation][hover][color]',
		// 'setting' => array(
		// 'default' => '',
		// ),
		// 'control' => array(
		// 'label'       => esc_html__( 'Text Color', 'customize-my-account-page-for-woocommerce' ),
		// 'description' => esc_html__( 'Choose tab hover text color', 'customize-my-account-page-for-woocommerce' ),
		// 'section'     => 'tgwc_customize[legacy]',
		// 'type'        => 'color',
		// ),
		// );

		// $controls[] = array(
		// 'id'      => 'tgwc_customize[navigation][hover][background_color]',
		// 'setting' => array(
		// 'default' => '',
		// ),
		// 'control' => array(
		// 'label'       => esc_html__( 'Background Color', 'customize-my-account-page-for-woocommerce' ),
		// 'description' => esc_html__( 'Choose tab hover background color', 'customize-my-account-page-for-woocommerce' ),
		// 'section'     => 'tgwc_customize[legacy]',
		// 'type'        => 'color',
		// ),
		// );

		$controls[] = array(
			'id'      => 'tgwc_customize[navigation][hover][border_style]',
			'setting' => array(
				'default' => 'inherit',
			),
			'control' => array(
				'label'       => esc_html__( 'Border', 'customize-my-account-page-for-woocommerce' ),
				'description' => esc_html__( 'Select hover background border style', 'customize-my-account-page-for-woocommerce' ),
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
			'id'      => 'tgwc_customize[navigation][hover][border_width]',
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
				'description'     => esc_html__( 'Set tab hover border width', 'customize-my-account-page-for-woocommerce' ),
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
					$border_control = $manager->get_control( 'tgwc_customize[navigation][hover][border_style]' );
					$border = $border_control->value();
					return 'none' !== $border;
				},
			),
		);

		// $controls[] = array(
		// 'id'      => 'tgwc_customize[navigation][hover][border_color]',
		// 'setting' => array(
		// 'default' => '',
		// ),
		// 'control' => array(
		// 'label'           => esc_html__( 'Border Color', 'customize-my-account-page-for-woocommerce' ),
		// 'description'     => esc_html__( 'Choose tab hover border color', 'customize-my-account-page-for-woocommerce' ),
		// 'section'         => 'tgwc_customize[legacy]',
		// 'type'            => 'color',
		// 'active_callback' => function ( $control ) {
		// $manager = $control->setting->manager;
		// $border_control = $manager->get_control( 'tgwc_customize[navigation][hover][border_style]' );
		// $border = $border_control->value();
		// return 'none' !== $border;
		// },
		// ),

		// );

		return $controls;
	}

	/**
	 * Normal section controls.
	 *
	 * @since 0.1.0
	 *
	 * @return array Normal section controls.
	 */
	public static function normal_controls() {
		$controls[] = array(
			'id'      => 'tgwc_customize[navigation][normal][label]',
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
		// 'id'      => 'tgwc_customize[navigation][normal][color]',
		// 'setting' => array(
		// 'default' => '',
		// ),
		// 'control' => array(
		// 'label'       => esc_html__( 'Text Color', 'customize-my-account-page-for-woocommerce' ),
		// 'description' => esc_html__( 'Choose Tab text color', 'customize-my-account-page-for-woocommerce' ),
		// 'section'     => 'tgwc_customize[legacy]',
		// 'type'        => 'color',
		// ),
		// );

		// $controls[] = array(
		// 'id'      => 'tgwc_customize[navigation][normal][background_color]',
		// 'setting' => array(
		// 'default' => '',
		// ),
		// 'control' => array(
		// 'label'       => esc_html__( 'Background Color', 'customize-my-account-page-for-woocommerce' ),
		// 'description' => esc_html__( 'Choose tab background color', 'customize-my-account-page-for-woocommerce' ),
		// 'section'     => 'tgwc_customize[legacy]',
		// 'type'        => 'color',
		// ),
		// );

		$controls[] = array(
			'id'      => 'tgwc_customize[navigation][normal][border_style]',
			'setting' => array(
				'default' => 'solid',
			),
			'control' => array(
				'label'       => esc_html__( 'Border', 'customize-my-account-page-for-woocommerce' ),
				'description' => esc_html__( 'Select tab background border', 'customize-my-account-page-for-woocommerce' ),
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
			'id'      => 'tgwc_customize[navigation][normal][border_width]',
			'setting' => array(
				'default' => array(
					'top'    => 1,
					'right'  => 0,
					'bottom' => 0,
					'left'   => 0,
				),
			),
			'control' => array(
				'label'           => esc_html__( 'Border Width', 'customize-my-account-page-for-woocommerce' ),
				'description'     => esc_html__( 'Set tab border width', 'customize-my-account-page-for-woocommerce' ),
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
					$border_control = $manager->get_control( 'tgwc_customize[navigation][normal][border_style]' );
					$border = $border_control->value();
					return 'none' !== $border;
				},
			),
		);

		// $controls[] = array(
		// 'id'      => 'tgwc_customize[navigation][normal][border_color]',
		// 'setting' => array(
		// 'default' => '',
		// ),
		// 'control' => array(
		// 'label'           => esc_html__( 'Border Color', 'customize-my-account-page-for-woocommerce' ),
		// 'description'     => esc_html__( 'Choose tab border color', 'customize-my-account-page-for-woocommerce' ),
		// 'section'         => 'tgwc_customize[legacy]',
		// 'type'            => 'color',
		// 'active_callback' => function ( $control ) {
		// $manager = $control->setting->manager;
		// $border_control = $manager->get_control( 'tgwc_customize[navigation][normal][border_style]' );
		// $border = $border_control->value();
		// return 'none' !== $border;
		// },
		// ),
		// );

		return $controls;
	}

	/**
	 * General section controls.
	 *
	 * @since 0.1.0
	 *
	 * @return array General section controls.
	 */
	public static function general_controls() {
		$controls[] = array(
			'id'      => 'tgwc_customize[navigation][general][label]',
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
			'id'      => 'tgwc_customize[navigation][general][padding]',
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
				'description' => esc_html__( 'Set navigation item padding.', 'customize-my-account-page-for-woocommerce' ),
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
