<?php
/**
 * Formatting functions.
 *
 * Functions for formatting data.
 *
 * @package ThemeGrill\WoocommerceCustomizer\Functions
 * @version 1.0.0
 */
if ( ! function_exists( 'tgwc_string_to_bool' ) ) {

	/**
	 * Converts a string (e.g. 'yes' or 'no') to a bool.
	 *
	 * @since 0.1.0
	 *
	 * @param string $string String to convert.
	 *
	 * @return bool
	 */
	function tgwc_string_to_bool( $string ) {
		return is_bool( $string ) ? $string : ( 'yes' === $string || 1 === $string || 'true' === $string || '1' === $string );
	}
}

if ( ! function_exists( 'tgwc_bool_to_string' ) ) {

	/**
	 * Converts a bool to a 'yes' or 'no'.
	 *
	 * @since 0.1.0
	 *
	 * @param bool $bool String to convert.
	 *
	 * @return string
	 */
	function tgwc_bool_to_string( $bool ) {
		if ( ! is_bool( $bool ) ) {
			$bool = tgwc_string_to_bool( $bool );
		}
		return true === $bool ? 'yes' : 'no';
	}
}

if ( ! function_exists( 'tgwc_suffix_array' ) ) {

	/**
	 * Add a suffix into an array.
	 *
	 * @since  0.1.0
	 *
	 * @param  array  $array  Raw array data.
	 * @param  string $suffix Suffix to be added.
	 *
	 * @return array Modified array with suffix added.
	 */
	function tgwc_suffix_array( $array = array(), $suffix = '' ) {
		return preg_filter( '/$/', $suffix, $array );
	}
}

if ( ! function_exists( 'tgwc_array_to_string' ) ) {

	/**
	 * Implode an array into a string by $glue and remove empty values.
	 *
	 * @since  0.1.0
	 * @param  array  $array Array to convert.
	 * @param  string $glue  Glue, defaults to ' '.
	 *
	 * @return string
	 */
	function tgwc_array_to_string( $array = array(), $glue = ' ' ) {
		return is_string( $array ) ? $array : implode( $glue, array_filter( $array ) );
	}
}

if ( ! function_exists( 'tgwc_string_to_array' ) ) {

	/**
	 * Explode a string into an array by $delimiter and remove empty values.
	 *
	 * @since 0.1.0
	 *
	 * @param  string $string    String to convert.
	 * @param  string $delimiter Delimiter, defaults to ','.
	 *
	 * @return array
	 */
	function tgwc_string_to_array( $string, $delimiter = ',' ) {
		return is_array( $string ) ? $string : array_filter( explode( $delimiter, $string ) );
	}
}
if ( ! function_exists( 'tgwc_sanitize_dimension_unit' ) ) {

	/**
	 * Format dimensions for display.
	 *
	 * @since  0.1.0
	 *
	 * @param  array $dimensions Array of dimensions.
	 * @param  array $unit       Unit, defaults to 'px'.
	 *
	 * @return string
	 */
	function tgwc_sanitize_dimension_unit( $dimensions = array(), $unit = 'px' ) {
		$dimensions = array_filter(
			$dimensions,
			function ( $dimension ) {
				return in_array( $dimension, array( 'left', 'right', 'top', 'bottom' ), true );
			},
			ARRAY_FILTER_USE_KEY
		);

		$dimensions = array_map(
			function ( $dimension ) {
				return empty( $dimension ) ? 0 : $dimension;
			},
			$dimensions
		);

		return tgwc_array_to_string( tgwc_suffix_array( $dimensions, $unit ) );
	}
}

if ( ! function_exists( 'tgwc_array_filter_numeric' ) ) {

	/**
	 * Check if array value is numeric or not.
	 *
	 * @param array $array Array.
	 * @return array
	 */
	function tgwc_array_filter_numeric( $array = array() ) {
		return array_filter(
			(array) $array,
			function ( $v ) {
				return is_numeric( $v );
			}
		);
	}
}
if ( ! function_exists( 'tgwc_sanitize_taxonomy_name' ) ) {

	/**
	 * Sanitize taxonomy names. Slug format (no spaces, lowercase).
	 * Urldecode is used to reverse munging of UTF8 characters.
	 *
	 * @since 0.1.0
	 *
	 * @param string $taxonomy Taxonomy name.
	 *
	 * @return string
	 */
	function tgwc_sanitize_taxonomy_name( $taxonomy ) {
		return apply_filters( 'sanitize_taxonomy_name', urldecode( sanitize_title( urldecode( $taxonomy ) ) ), $taxonomy );
	}
}

if ( ! function_exists( 'tgwc_sanitize_permalink' ) ) {

	/**
	 * Sanitize permalink values before insertion into DB.
	 *
	 * Cannot use tgwc_clean because it sometimes strips % chars and breaks the user's setting.
	 *
	 * @since 0.1.0
	 *
	 * @param  string $value Permalink.
	 *
	 * @return string
	 */
	function tgwc_sanitize_permalink( $value ) {
		global $wpdb;

		$value = $wpdb->strip_invalid_text_for_column( $wpdb->options, 'option_value', $value );

		if ( is_wp_error( $value ) ) {
			$value = '';
		}

		$value = esc_url_raw( trim( $value ) );
		$value = str_replace( 'http://', '', $value );
		return untrailingslashit( $value );
	}
}

if ( ! function_exists( 'tgwc_get_filename_from_url' ) ) {

	/**
	 * Gets the filename part of a download URL.
	 *
	 * @since 0.1.0
	 *
	 * @param string $file_url File URL.
	 *
	 * @return string
	 */
	function tgwc_get_filename_from_url( $file_url ) {
		$parts = wp_parse_url( $file_url );
		if ( isset( $parts['path'] ) ) {
			return basename( $parts['path'] );
		}
	}
}
if ( ! function_exists( 'tgwc_clean' ) ) {

	/**
	 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
	 * Non-scalar values are ignored.
	 *
	 * @since 0.1.0
	 *
	 * @param string|array $text Data to sanitize.
	 *
	 * @return string|array
	 */
	function tgwc_clean( $text ) {
		if ( is_array( $text ) ) {
			return array_map( 'tgwc_clean', $text );
		} else {
			return is_scalar( $text ) ? sanitize_text_field( $text ) : $text;
		}
	}
}

if ( ! function_exists( 'tgwc_sanitize_textarea' ) ) {

	/**
	 * Run tgwc_clean over posted textarea but maintain line breaks.
	 *
	 * @since 0.1.0
	 *
	 * @param  string $text Data to sanitize.
	 *
	 * @return string
	 */
	function tgwc_sanitize_textarea( $text ) {
		return implode( "\n", array_map( 'tgwc_clean', explode( "\n", $text ) ) );
	}

}
if ( ! function_exists( 'tgwc_sanitize_tooltip' ) ) {

	/**
	 * Sanitize a string destined to be a tooltip.
	 *
	 * Tooltips are encoded with htmlspecialchars to prevent XSS.
	 * Should not be used in conjunction with esc_attr().
	 *
	 * @since  1.0.0
	 *
	 * @param  string $text Data to sanitize.
	 * @return string
	 */
	function tgwc_sanitize_tooltip( $text ) {
		return htmlspecialchars(
			wp_kses(
				html_entity_decode(
					$text,
					ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401,
					'UTF-8'
				),
				array(
					'br'     => array(),
					'em'     => array(),
					'strong' => array(),
					'small'  => array(),
					'span'   => array(),
					'ul'     => array(),
					'li'     => array(),
					'ol'     => array(),
					'p'      => array(),
				)
			),
			ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401,
			'UTF-8'
		);
	}
}


if ( ! function_exists( 'tgwc_array_overlay' ) ) {

	/**
	 * Merge two arrays.
	 *
	 * @since 0.1.0
	 *
	 * @param array $a1 First array to merge.
	 * @param array $a2 Second array to merge.
	 *
	 * @return array
	 */
	function tgwc_array_overlay( $a1, $a2 ) {
		foreach ( $a1 as $k => $v ) {
			if ( ! array_key_exists( $k, $a2 ) ) {
				continue;
			}
			if ( is_array( $v ) && is_array( $a2[ $k ] ) ) {
				$a1[ $k ] = tgwc_array_overlay( $v, $a2[ $k ] );
			} else {
				$a1[ $k ] = $a2[ $k ];
			}
		}
		return $a1;
	}
}

if ( ! function_exists( 'tgwc_sanitize_array_combine' ) ) {

	/**
	 * Array combine.
	 *
	 * @since 0.1.0
	 *
	 * @param  array $array Array of data.
	 *
	 * @return array
	 */
	function tgwc_sanitize_array_combine( $array ) {
		if ( empty( $array ) || ! is_array( $array ) ) {
			return $array;
		}

		return array_map( 'sanitize_text_field', $array );
	}
}

if ( ! function_exists( 'tgwc_let_to_num' ) ) {


	/**
	 * Notation to numbers.
	 *
	 * This function transforms the php.ini notation for numbers (like '2M') to an integer.
	 *
	 * @since 0.1.0
	 *
	 * @param  string $size Size value.
	 *
	 * @return int
	 */
	function tgwc_let_to_num( $size ) {
		$l    = substr( $size, -1 );
		$ret  = substr( $size, 0, -1 );
		$byte = 1024;

		switch ( strtoupper( $l ) ) {
			case 'P':
				$ret *= 1024;
				// No break.
			case 'T':
				$ret *= 1024;
				// No break.
			case 'G':
				$ret *= 1024;
				// No break.
			case 'M':
				$ret *= 1024;
				// No break.
			case 'K':
				$ret *= 1024;
				// No break.
		}
		return $ret;
	}
}
if ( ! function_exists( 'tgwc_date_format' ) ) {

	/**
	 * TGWC Date Format - Allows to change date format for everything TGWC.
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	function tgwc_date_format() {
		return apply_filters( 'tgwc_date_format', get_option( 'date_format' ) );
	}
}
if ( ! function_exists( 'tgwc_time_format' ) ) {


	/**
	 * TGWC Time Format - Allows to change time format for everything TGWC.
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	function tgwc_time_format() {
		return apply_filters( 'tgwc_time_format', get_option( 'time_format' ) );
	}
}

if ( ! function_exists( 'tgwc_flatten_meta_callback' ) ) {

	/**
	 * Callback which can flatten post meta (gets the first value if it's an array).
	 *
	 * @since 0.1.0
	 *
	 * @param  array $value Value to flatten.
	 *
	 * @return mixed
	 */
	function tgwc_flatten_meta_callback( $value ) {
		return is_array( $value ) ? current( $value ) : $value;
	}
}
if ( ! function_exists( 'tgwc_rgb_from_hex' ) ) {

	/**
	 * Convert RGB to HEX.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed $color Color.
	 *
	 * @return array
	 */
	function tgwc_rgb_from_hex( $color ) {
		$color = str_replace( '#', '', $color );
		// Convert shorthand colors to full format, e.g. "FFF" -> "FFFFFF".
		$color = preg_replace( '~^(.)(.)(.)$~', '$1$1$2$2$3$3', $color );

		$rgb      = array();
		$rgb['R'] = hexdec( $color[0] . $color[1] );
		$rgb['G'] = hexdec( $color[2] . $color[3] );
		$rgb['B'] = hexdec( $color[4] . $color[5] );

		return $rgb;
	}
}

if ( ! function_exists( 'tgwc_hex_darker' ) ) {

	/**
	 * Make HEX color darker.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed $color  Color.
	 * @param int   $factor Darker factor.
	 *                      Defaults to 30.
	 * @return string
	 */
	function tgwc_hex_darker( $color, $factor = 30 ) {
		$base  = tgwc_rgb_from_hex( $color );
		$color = '#';

		foreach ( $base as $k => $v ) {
			$amount      = $v / 100;
			$amount      = round( $amount * $factor );
			$new_decimal = $v - $amount;

			$new_hex_component = dechex( $new_decimal );
			if ( strlen( $new_hex_component ) < 2 ) {
				$new_hex_component = '0' . $new_hex_component;
			}
			$color .= $new_hex_component;
		}

		return $color;
	}
}

if ( ! function_exists( 'tgwc_hex_lighter' ) ) {

	/**
	 * Make HEX color lighter.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed $color  Color.
	 * @param int   $factor Lighter factor.
	 *                      Defaults to 30.
	 * @return string
	 */
	function tgwc_hex_lighter( $color, $factor = 30 ) {
		$base  = tgwc_rgb_from_hex( $color );
		$color = '#';

		foreach ( $base as $k => $v ) {
			$amount      = 255 - $v;
			$amount      = $amount / 100;
			$amount      = round( $amount * $factor );
			$new_decimal = $v + $amount;

			$new_hex_component = dechex( $new_decimal );
			if ( strlen( $new_hex_component ) < 2 ) {
				$new_hex_component = '0' . $new_hex_component;
			}
			$color .= $new_hex_component;
		}

		return $color;
	}
}

if ( ! function_exists( 'tgwc_hex_is_light' ) ) {

	/**
	 * Determine whether a hex color is light.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed $color Color.
	 *
	 * @return bool  True if a light color.
	 */
	function tgwc_hex_is_light( $color ) {
		$hex = str_replace( '#', '', $color );

		$c_r = hexdec( substr( $hex, 0, 2 ) );
		$c_g = hexdec( substr( $hex, 2, 2 ) );
		$c_b = hexdec( substr( $hex, 4, 2 ) );

		$brightness = ( ( $c_r * 299 ) + ( $c_g * 587 ) + ( $c_b * 114 ) ) / 1000;

		return $brightness > 155;
	}
}

if ( ! function_exists( 'tgwc_light_or_dark' ) ) {

	/**
	 * Detect if we should use a light or dark color on a background color.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed  $color Color.
	 * @param string $dark  Darkest reference.
	 *                      Defaults to '#000000'.
	 * @param string $light Lightest reference.
	 *                      Defaults to '#FFFFFF'.
	 * @return string
	 */
	function tgwc_light_or_dark( $color, $dark = '#000000', $light = '#FFFFFF' ) {
		return tgwc_hex_is_light( $color ) ? $dark : $light;
	}
}
if ( ! function_exists( 'tgwc_format_hex' ) ) {

	/**
	 * Format string as hex.
	 *
	 * @since 0.1.0
	 *
	 * @param string $hex HEX color.
	 * @return string|null
	 */
	function tgwc_format_hex( $hex ) {
		$hex = trim( str_replace( '#', '', $hex ) );

		if ( strlen( $hex ) === 3 ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}

		return $hex ? '#' . $hex : null;
	}
}

if ( ! function_exists( 'tgwc_format_phone_number' ) ) {

	/**
	 * Format phone numbers.
	 *
	 * @since 0.1.0
	 *
	 * @param  string $phone Phone number.
	 *
	 * @return string
	 */
	function tgwc_format_phone_number( $phone ) {
		return str_replace( '.', '-', $phone );
	}
}

if ( ! function_exists( 'tgwc_strtoupper' ) ) {

	/**
	 * Wrapper for mb_strtoupper which see's if supported first.
	 *
	 * @since 0.1.0
	 *
	 * @param  string $string String to format.
	 *
	 * @return string
	 */
	function tgwc_strtoupper( $string ) {
		return function_exists( 'mb_strtoupper' ) ? mb_strtoupper( $string, 'UTF-8' ) : strtoupper( $string );
	}
}

if ( ! function_exists( 'tgwc_strtolower' ) ) {

	/**
	 * Make a string lowercase.
	 * Try to use mb_strtolower() when available.
	 *
	 * @since 0.1.0
	 *
	 * @param  string $string String to format.
	 *
	 * @return string
	 */
	function tgwc_strtolower( $string ) {
		return function_exists( 'mb_strtolower' ) ? mb_strtolower( $string, 'UTF-8' ) : strtolower( $string );
	}
}

if ( ! function_exists( 'tgwc_trim_string' ) ) {

	/**
	 * Trim a string and append a suffix.
	 *
	 * @since 0.1.0
	 *
	 * @param  string  $string String to trim.
	 * @param  integer $chars  Amount of characters.
	 *                         Defaults to 200.
	 * @param  string  $suffix Suffix.
	 *                         Defaults to '...'.
	 * @return string
	 */
	function tgwc_trim_string( $string, $chars = 200, $suffix = '...' ) {
		if ( strlen( $string ) > $chars ) {
			if ( function_exists( 'mb_substr' ) ) {
				$string = mb_substr( $string, 0, ( $chars - mb_strlen( $suffix, 'UTF-8' ) ), 'UTF-8' ) . $suffix;
			} else {
				$string = substr( $string, 0, ( $chars - strlen( $suffix ) ) ) . $suffix;
			}
		}
		return $string;
	}
}

if ( ! function_exists( 'tgwc_format_content' ) ) {

	/**
	 * Format content to display shortcodes.
	 *
	 * @since 0.1.0
	 *
	 * @param  string $raw_string Raw string.
	 *
	 * @return string
	 */
	function tgwc_format_content( $raw_string ) {
		return apply_filters( 'tgwc_format_content', apply_filters( 'tgwc_short_description', $raw_string ), $raw_string );
	}
}

if ( ! function_exists( 'tgwc_do_oembeds' ) ) {

	/**
	 * Process oEmbeds.
	 *
	 * @since 0.1.0
	 *
	 * @param  string $content Content.
	 *
	 * @return string
	 */
	function tgwc_do_oembeds( $content ) {
		global $wp_embed;

		$content = $wp_embed->autoembed( $content );

		return $content;
	}
}

if ( ! function_exists( 'tgwc_array_merge_recursive_numeric' ) ) {

	/**
	 * Array merge and sum function.
	 *
	 * Source:  https://gist.github.com/Nickology/f700e319cbafab5eaedc
	 *
	 * @since  0.1.0
	 *
	 * @return array
	 */
	function tgwc_array_merge_recursive_numeric() {
		$arrays = func_get_args();

		// If there's only one array, it's already merged.
		if ( 1 === count( $arrays ) ) {
			return $arrays[0];
		}

		// Remove any items in $arrays that are NOT arrays.
		foreach ( $arrays as $key => $array ) {
			if ( ! is_array( $array ) ) {
				unset( $arrays[ $key ] );
			}
		}

		// We start by setting the first array as our final array.
		// We will merge all other arrays with this one.
		$final = array_shift( $arrays );

		foreach ( $arrays as $b ) {
			foreach ( $final as $key => $value ) {
				// If $key does not exist in $b, then it is unique and can be safely merged.
				if ( ! isset( $b[ $key ] ) ) {
					$final[ $key ] = $value;
				} else {
					// If $key is present in $b, then we need to merge and sum numeric values in both.
					if ( is_numeric( $value ) && is_numeric( $b[ $key ] ) ) {
						// If both values for these keys are numeric, we sum them.
						$final[ $key ] = $value + $b[ $key ];
					} elseif ( is_array( $value ) && is_array( $b[ $key ] ) ) {
						// If both values are arrays, we recursively call ourself.
						$final[ $key ] = tgwc_array_merge_recursive_numeric( $value, $b[ $key ] );
					} else {
						// If both keys exist but differ in type, then we cannot merge them.
						// In this scenario, we will $b's value for $key is used.
						$final[ $key ] = $b[ $key ];
					}
				}
			}

			// Finally, we need to merge any keys that exist only in $b.
			foreach ( $b as $key => $value ) {
				if ( ! isset( $final[ $key ] ) ) {
					$final[ $key ] = $value;
				}
			}
		}

		return $final;
	}
}

if ( ! function_exists( 'tgwc_implode_html_attributes' ) ) {

	/**
	 * Implode and escape HTML attributes for output.
	 *
	 * @since 0.1.0
	 *
	 * @param array $raw_attributes Attribute name value pairs.
	 *
	 * @return string
	 */
	function tgwc_implode_html_attributes( $raw_attributes ) {
		$attributes = array();
		foreach ( $raw_attributes as $name => $value ) {
			$attributes[] = esc_attr( $name ) . '="' . esc_attr( $value ) . '"';
		}
		return implode( ' ', $attributes );
	}
}

if ( ! function_exists( 'tgwc_parse_relative_date_option' ) ) {

	/**
	 * Parse a relative date option from the settings API into a standard format.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed $raw_value Value stored in DB.
	 *
	 * @return array Nicely formatted array with number and unit values.
	 */
	function tgwc_parse_relative_date_option( $raw_value ) {
		$periods = array(
			'days'   => __( 'Day(s)', 'customize-my-account-page-for-woocommerce' ),
			'weeks'  => __( 'Week(s)', 'customize-my-account-page-for-woocommerce' ),
			'months' => __( 'Month(s)', 'customize-my-account-page-for-woocommerce' ),
			'years'  => __( 'Year(s)', 'customize-my-account-page-for-woocommerce' ),
		);

		$value = wp_parse_args(
			(array) $raw_value,
			array(
				'number' => '',
				'unit'   => 'days',
			)
		);

		$value['number'] = ! empty( $value['number'] ) ? absint( $value['number'] ) : '';

		if ( ! in_array( $value['unit'], array_keys( $periods ), true ) ) {
			$value['unit'] = 'days';
		}

		return $value;
	}
}

if ( ! function_exists( 'tgwc_flatten_array' ) ) {

	/**
	 * Callback which can flatten structure data (gets the value if it's a multidimensional array).
	 *
	 * @since  0.1.0
	 *
	 * @param  array $value Value to flatten.
	 *
	 * @return array
	 */
	function tgwc_flatten_array( $value = array() ) {
		$return = array();
		array_walk_recursive(
			$value,
			function ( $a ) use ( &$return ) {
				$return[] = $a;
			}
		);
		return $return;
	}
}

if ( ! function_exists( 'tgwc_array_splice_preserve_keys' ) ) {

	/**
	 * An `array_splice` which does preverse the keys of the replacement array
	 *
	 * The argument list is identical to `array_splice`
	 *
	 * @since 0.1.0
	 *
	 * @link https://github.com/lode/gaps/blob/master/src/gaps.php
	 *
	 * @param  array $input       The input array.
	 * @param  int   $offset      The offeset to start.
	 * @param  int   $length      Optional length.
	 * @param  array $replacement The replacement array.
	 *
	 * @return array the array consisting of the extracted elements.
	 */
	function tgwc_array_splice_preserve_keys( &$input, $offset, $length = null, $replacement = array() ) {
		if ( empty( $replacement ) ) {
			return array_splice( $input, $offset, $length );
		}

		$part_before  = array_slice( $input, 0, $offset, true );
		$part_removed = array_slice( $input, $offset, $length, true );
		$part_after   = array_slice( $input, $offset + $length, null, true );

		$input = $part_before + $replacement + $part_after;

		return $part_removed;
	}
}

if ( ! function_exists( 'tgwc_hyphen2dash' ) ) {

	/**
	 * Convert hyphen to dash.
	 *
	 * @since 0.1.0
	 *
	 * @param string $text Text to be converted.
	 *
	 * @return string
	 */
	function tgwc_hyphen2dash( $text ) {
		return str_replace( '_', '-', $text );
	}
}

if ( ! function_exists( 'tgwc_format_css_value' ) ) {

	/**
	 * Format CSS property value.
	 *
	 * @since 0.1.0
	 *
	 * @param string $value CSS property value.
	 * @param string $unit CSS property value unit.
	 * @param string $prop CSS propety.
	 *
	 * @return string Formatted CSS property value.
	 */
	function tgwc_format_css_value( $value = '', $unit = '', $prop = '' ) {
		$value = trim( $value );
		if ( '' === $value || 'inherit' === $value ) {
			$value = 'null';
		} else {
			$value .= $unit;
		}

		return apply_filters( 'tgwc_format_css_value', $value, $unit, $prop );
	}
}
