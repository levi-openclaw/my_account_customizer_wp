<?php
/**
 * Icon class.
 *
 * @package ThemeGrill\WoocommerceCustomizer
 */

namespace ThemeGrill\WoocommerceCustomizer;

defined( 'ABSPATH' ) || exit;

class Icon {

	/**
	 * Svgs.
	 *
	 * @var null
	 */
	private static $svgs = null;

	/**
	 * Allowed HTML.
	 *
	 * @var bool[][]
	 */
	private static $allowed_svg_html = array(
		'svg'     => array(
			'class'       => true,
			'xmlns'       => true,
			'width'       => true,
			'height'      => true,
			'viewbox'     => true,
			'aria-hidden' => true,
			'role'        => true,
			'focusable'   => true,
		),
		'path'    => array(
			'fill'      => true,
			'fill-rule' => true,
			'd'         => true,
			'transform' => true,
		),
		'circle'  => array(
			'cx' => true,
			'cy' => true,
			'r'  => true,
		),
		'polygon' => array(
			'fill'      => true,
			'fill-rule' => true,
			'points'    => true,
			'transform' => true,
			'focusable' => true,
		),
	);

	/**
	 * Get SVG icon.
	 *
	 * @param $icon
	 * @param bool $echo
	 *
	 * @return mixed|void
	 */
	public static function get_svg_icon( $icon, $echo = false ) {

		if ( is_null( self::$svgs ) ) {
			ob_start();
			include_once dirname( TGWC_PLUGIN_FILE ) . '/assets/svg/svgs.json';
			self::$svgs = json_decode( ob_get_clean(), true );
			self::$svgs = apply_filters( 'tgwc_svg_icons', self::$svgs );
		}

		$svg_icon = isset( self::$svgs[ $icon ] ) ? self::$svgs[ $icon ] : '';

		if ( ! $echo ) {
			return $svg_icon;
		}

		echo wp_kses( $svg_icon, self::$allowed_svg_html );
	}
}
