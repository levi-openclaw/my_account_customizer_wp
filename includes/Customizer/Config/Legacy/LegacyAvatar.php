<?php
/**
 * Legacy configs.
 *
 * @package ThemeGrill\WoocommerceCustomizer\Customizer\Config\Legacy
 * @since   2.0.0
 */

namespace ThemeGrill\WoocommerceCustomizer\Customizer\Config\Legacy;

defined( 'ABSPATH' ) || exit;

class LegacyAvatar {
	/**
	 * Avatar controls.
	 *
	 * @return void
	 */
	public static function avatar_controls() {
		$controls[] = array(
			'id'      => 'tgwc_customize[legacy][avatar][label]',
			'setting' => array(
				'default' => '',
			),
			'control' => array(
				'label'   => esc_html__( 'Avatar', 'customize-my-account-page-for-woocommerce' ),
				'section' => 'tgwc_customize[legacy]',
				'type'    => 'tgwc-label',
				'class'   => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\Label',
			),
		);

		$controls[] = array(
			'id'      => 'tgwc_customize[avatar][layout]',
			'setting' => array(
				'default'           => 'left',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'control' => array(
				'label'       => esc_html__( 'Avatar Layout', 'customize-my-account-page-for-woocommerce' ),
				'description' => esc_html__( 'Choose Avatar layout', 'customize-my-account-page-for-woocommerce' ),
				'section'     => 'tgwc_customize[legacy]',
				'type'        => 'tgwc-image_radio',
				'class'       => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\ImageRadio',
				'choices'     => array(
					'left'     => array(
						'name'   => esc_html__( 'Avatar left aligned', 'customize-my-account-page-for-woocommerce' ),
						'image'  => plugins_url( '/assets/images/avatar-left-aligned.svg', TGWC_PLUGIN_FILE ),
						'width'  => 100,
						'height' => 100,
					),
					'right'    => array(
						'name'   => esc_html__( 'Avatar right aligned', 'customize-my-account-page-for-woocommerce' ),
						'image'  => plugins_url( '/assets/images/avatar-right-aligned.svg', TGWC_PLUGIN_FILE ),
						'width'  => 100,
						'height' => 100,
					),
					'center'   => array(
						'name'   => esc_html__( 'Avatar center aligned', 'customize-my-account-page-for-woocommerce' ),
						'image'  => plugins_url( '/assets/images/avatar-center-aligned.svg', TGWC_PLUGIN_FILE ),
						'width'  => 100,
						'height' => 100,
					),
					'vertical' => array(
						'name'   => esc_html__( 'Avatar vertical aligned', 'customize-my-account-page-for-woocommerce' ),
						'image'  => plugins_url( '/assets/images/avatar-vertical-aligned.svg', TGWC_PLUGIN_FILE ),
						'width'  => 100,
						'height' => 100,
					),
				),
			),
		);

		$controls[] = array(
			'id'      => 'tgwc_customize[avatar][username]',
			'setting' => array(
				'default'           => true,
				'sanitize_callback' => 'sanitize_text_field',
			),
			'control' => array(
				'label'       => esc_html__( 'Avatar Username', 'customize-my-account-page-for-woocommerce' ),
				'description' => esc_html__( 'Enable and disble to hide the username', 'customize-my-account-page-for-woocommerce' ),
				'section'     => 'tgwc_customize[legacy]',
				'type'        => 'tgwc-toggle',
				'class'       => 'ThemeGrill\WoocommerceCustomizer\Customizer\Controls\Toggle',
			),
		);

		return $controls;
	}
}
