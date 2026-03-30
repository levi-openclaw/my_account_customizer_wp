<?php
/**
 * TG WooCommerce My Account Customizer SCSS
 *
 * @package Woocommerce_My_Account_Style_Customizer
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

// Get values.
$values = tgwc_get_customizer_values(); // phpcs:ignore PHPCompatibility.PHP.NewFunctions.array_replace_recursiveFound

$active_menu_style  = isset( $values['layout']['menu_style'] ) ? $values['layout']['menu_style'] : 'minimal';
$active_palette_key = isset( $values['layout']['color_palette']['activePalette'][ $active_menu_style ] )
	? $values['layout']['color_palette']['activePalette'][ $active_menu_style ]
	: 'minimal_brick_red';

$active_palette = isset( $values['layout']['color_palette']['palettes'][ $active_palette_key ] )
	? $values['layout']['color_palette']['palettes'][ $active_palette_key ]
	: array();

$active_colors = isset( $active_palette['colors'] )
	? $active_palette['colors']
	: array();

// Font styles.
$font_styles         = array(
	'font-weight'     => 'bold',
	'font-style'      => 'italic',
	'text-decoration' => 'underline',
	'text-transform'  => 'uppercase',
);
$font_styles_default = array(
	'font-weight'     => 'normal',
	'font-style'      => 'normal',
	'text-decoration' => 'none',
	'text-transform'  => 'none',
);

?>

// Wrapper variables.
$wrapper_font_family: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $values['wrapper']['font_family'] ) ) ); ?>;
$wrapper_font_size: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $values['wrapper']['font_size'] ), 'px' ) ); ?>;
$wrapper_background_color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( isset( $active_colors['wrapper_background']['normal'] ) ? $active_colors['wrapper_background']['normal'] : '' ) ) ); ?>;

// Navigation variables.
$nav_border_style: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $values['navigation']['normal']['border_style'] ) ) ); ?>;
$nav_font_color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( isset( $active_colors['item_text']['normal'] ) ? $active_colors['item_text']['normal'] : '' ) ) ); ?>;
$nav_background_color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( isset( $active_colors['item_background']['normal'] ) ? $active_colors['item_background']['normal'] : '' ) ) ); ?>;
$nav_border_color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( isset( $active_colors['item_border']['normal'] ) ? $active_colors['item_border']['normal'] : '' ) ) ); ?>;
//hover
$nav_hover_border_style: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $values['navigation']['hover']['border_style'] ) ) ); ?>;
$nav_hover_font_color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( isset( $active_colors['item_text']['hover'] ) ? $active_colors['item_text']['hover'] : '' ) ) ); ?>;
$nav_hover_background_color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( isset( $active_colors['item_background']['hover'] ) ? $active_colors['item_background']['hover'] : '' ) ) ); ?>;
$nav_hover_border_color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( isset( $active_colors['item_border']['hover'] ) ? $active_colors['item_border']['hover'] : '' ) ) ); ?>;
//active
$nav_active_border_style: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $values['navigation']['active']['border_style'] ) ) ); ?>;
$nav_active_font_color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( isset( $active_colors['item_text']['active'] ) ? $active_colors['item_text']['active'] : '' ) ) ); ?>;
$nav_active_background_color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( isset( $active_colors['item_background']['active'] ) ? $active_colors['item_background']['active'] : '' ) ) ); ?>;
$nav_active_border_color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( isset( $active_colors['item_border']['active'] ) ? $active_colors['item_border']['active'] : '' ) ) ); ?>;

//New - Navigation colors.
$nav_font_size : <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $values['navigation']['font_size'] ), 'px' ) ); ?>;

// Content variables.
$content_background_color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( isset( $active_colors['content_background']['normal'] ) ? $active_colors['content_background']['normal'] : '' ) ) ); ?>;

// Form variables.
//Variables for input
$input_font_color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( isset( $active_colors['input_text']['normal'] ) ? $active_colors['input_text']['normal'] : '' ) ) ); ?>;
$input_background_color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( isset( $active_colors['input_background']['normal'] ) ? $active_colors['input_background']['normal'] : '' ) ) ); ?>;
$input_border_style: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $values['input_field']['normal']['border_style'] ) ) ); ?>;
$input_border_color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( isset( $active_colors['input_border']['normal'] ) ? $active_colors['input_border']['normal'] : '' ) ) ); ?>;
$input_focus_font_color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( isset( $active_colors['input_text']['focus'] ) ? $active_colors['input_text']['focus'] : '' ) ) ); ?>;
$input_focus_background_color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( isset( $active_colors['input_background']['focus'] ) ? $active_colors['input_background']['focus'] : '' ) ) ); ?>;
$input_focus_border_style: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $values['input_field']['focus']['border_style'] ) ) ); ?>;
$input_focus_border_color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( isset( $active_colors['input_border']['focus'] ) ? $active_colors['input_border']['focus'] : '' ) ) ); ?>;

// Button styles variables.
$button_font_size: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $values['button']['general']['font_size'] ), 'px' ) ); ?>;
$button_line_height: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $values['button']['general']['line_height'] ) ) ); ?>;
$button_font_color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( isset( $active_colors['button_text']['normal'] ) ? $active_colors['button_text']['normal'] : '' ) ) ); ?>;
$button_background_color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( isset( $active_colors['button_background']['normal'] ) ? $active_colors['button_background']['normal'] : '' ) ) ); ?>;
$button_border_style: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $values['button']['normal']['border_style'] ) ) ); ?>;
$button_border_color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( isset( $active_colors['button_border']['normal'] ) ? $active_colors['button_border']['normal'] : '' ) ) ); ?>;
//hover
$button_hover_font_color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( isset( $active_colors['button_text']['hover'] ) ? $active_colors['button_text']['hover'] : '' ) ) ); ?>;
$button_hover_border_style: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $values['button']['normal']['border_style'] ) ) ); ?>;
$button_hover_border_color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( isset( $active_colors['button_border']['hover'] ) ? $active_colors['button_border']['hover'] : '' ) ) ); ?>;
$button_hover_background_color: <?php echo esc_attr( tgwc_format_css_value( isset( $active_colors['button_background']['hover'] ) ? $active_colors['button_background']['hover'] : '' ) ); ?>;


/**
 * Imports.
 */
@import "bourbon";

/**
 * Responsive.
 */
@mixin responsive-media( $property, $device, $values ) {
	@if $device == "desktop" {
		@include _directional-property( $property, null, $values );
	} @else if $device == "tablet" {
		@media only screen and (max-width: 768px) {
			@include _directional-property( $property, null, $values);
		}
	} @else if $device == "mobile" {
		@media only screen and (max-width: 500px) {
			@include _directional-property( $property, null, $values);
		}
	}
}

/**
 * Styling begins.
 */
.logged-in.woocommerce-account.tgwc-woocommerce-customize-my-account {
	#tgwc-woocommerce.woocommerce {
		$self: &;
		background-color: $wrapper_background_color;
		<?php foreach ( array( 'margin', 'padding' ) as $separator_type ) : ?>
			<?php foreach ( $values['spacing']['page_container'][ $separator_type ] as $device => $value ) : ?>
				<?php if ( in_array( $device, array( 'desktop', 'tablet', 'mobile' ), true ) && tgwc_array_filter_numeric( $value ) ) : ?>
					<?php
						printf(
							'@include responsive-media(%s, %s, %s);',
							esc_attr( $separator_type ),
							esc_attr( $device ),
							esc_attr( tgwc_sanitize_dimension_unit( $value ) )
						);
					?>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php endforeach; ?>

		.tgwc-user-avatar {
			<?php foreach ( array( 'padding' ) as $separator_type ) : ?>
				<?php foreach ( $values['spacing']['avatar'][ $separator_type ] as $device => $value ) : ?>
					<?php if ( in_array( $device, array( 'desktop', 'tablet', 'mobile' ), true ) && tgwc_array_filter_numeric( $value ) ) : ?>
						<?php
							printf(
								'@include responsive-media(%s, %s, %s);',
								esc_attr( $separator_type ),
								esc_attr( $device ),
								esc_attr( tgwc_sanitize_dimension_unit( $value, 'px' ) )
							);
						?>
					<?php endif; ?>
				<?php endforeach; ?>
			<?php endforeach; ?>
		}

		.tgwc-woocommerce-MyAccount-navigation {
				font-size: $nav_font_size;

				<?php foreach ( $values['spacing']['menu_container']['padding'] as $device => $value ) : ?>
					<?php if ( in_array( $device, array( 'desktop', 'tablet', 'mobile' ), true ) && tgwc_array_filter_numeric( $value ) ) : ?>
						<?php
						printf(
							'@include responsive-media(%s, %s, %s);',
							'padding',
							esc_attr( $device ),
							esc_attr( tgwc_sanitize_dimension_unit( $value ) )
						);
						?>
				<?php endif; ?>
			<?php endforeach; ?>
		}
	}
	<?php
	if ( tgwc_is_old_user() ) :
		?>

	#tgwc-woocommerce.woocommerce[data-menu-layout="legacy"] {
		font-family: $wrapper_font_family;
		font-size: $wrapper_font_size;

		* {
			<?php
			if ( ! empty( $values['wrapper']['font_family'] ) ) :
				print( 'font-family: inherit;' );
			endif;
			?>
		}


		.woocommerce-MyAccount-content {
			background-color: $content_background_color;
			<?php foreach ( array( 'margin', 'padding' ) as $separator_type ) : ?>
				<?php foreach ( $values['content'][ $separator_type ] as $device => $value ) : ?>
					<?php if ( in_array( $device, array( 'desktop', 'tablet', 'mobile' ), true ) && tgwc_array_filter_numeric( $value ) ) : ?>
						<?php
						printf(
							'@include responsive-media(%s, %s, %s);',
							esc_attr( $separator_type ),
							esc_attr( $device ),
							esc_attr( tgwc_sanitize_dimension_unit( $value ) )
						);
						?>
					<?php endif; ?>
				<?php endforeach; ?>
			<?php endforeach; ?>

			input,
			.woocommerce-Input {
				color: $input_font_color;
				background: $input_background_color;
				<?php if ( isset( $values['input_field']['normal']['border_style'] ) ) : ?>
					border-style: $input_border_style;
					<?php if ( 'none' !== $values['input_field']['normal']['border_style'] ) : ?>
						border-color: $input_border_color;
						<?php tgwc_array_filter_numeric( $values['input_field']['normal']['border_width'] ) && printf( '@include border-width(%s);', esc_attr( tgwc_sanitize_dimension_unit( $values['input_field']['normal']['border_width'], 'px' ) ) ); ?>
					<?php endif; ?>
				<?php endif; ?>
				<?php foreach ( array( 'padding' ) as $separator_type ) : ?>
					<?php foreach ( $values['input_field']['general'][ $separator_type ] as $device => $value ) : ?>
						<?php if ( in_array( $device, array( 'desktop', 'tablet', 'mobile' ), true ) && tgwc_array_filter_numeric( $value ) ) : ?>
							<?php
								printf(
									'@include responsive-media(%s, %s, %s);',
									esc_attr( $separator_type ),
									esc_attr( $device ),
									esc_attr( tgwc_sanitize_dimension_unit( $value, 'px' ) )
								);
							?>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endforeach; ?>

				&:focus {
					color: $input_focus_font_color;
					background: $input_focus_background_color;
					<?php if ( isset( $values['input_field']['focus']['border_style'] ) ) : ?>
						border-style: $input_focus_border_style;
						<?php if ( 'none' !== $values['input_field']['focus']['border_style'] ) : ?>
							border-color: $input_focus_border_color;
							<?php tgwc_array_filter_numeric( $values['input_field']['focus']['border_width'] ) && printf( '@include border-width(%s);', esc_attr( tgwc_sanitize_dimension_unit( $values['input_field']['focus']['border_width'], 'px' ) ) ); ?>
						<?php endif; ?>
					<?php endif; ?>
				}
			}
		}

		button,
		button[type='button'],
		button[type='submit'],
		input[type='button'],
		input[type='submit'],
		a.woocommerce-Button,
		a.button {
			color: $button_font_color;
			font-size: $button_font_size;
			line-height: $button_line_height;
			background-color: $button_background_color;
			<?php if ( 'none' !== $values['button']['normal']['border_style'] ) : ?>
				border-style: $button_border_style;
				border-color: $button_border_color;
				<?php tgwc_array_filter_numeric( $values['button']['normal']['border_width'] ) && printf( '@include border-width(%s);', esc_attr( tgwc_sanitize_dimension_unit( $values['button']['normal']['border_width'], 'px' ) ) ); ?>
			<?php endif; ?>
			<?php
			foreach ( array( 'margin', 'padding' ) as $space ) {
				foreach ( $values['button']['general'][ $space ] as $device => $value ) {
					if ( in_array( $device, array( 'desktop', 'tablet', 'mobile' ), true ) && tgwc_array_filter_numeric( $value ) ) {
						printf(
							'@include responsive-media(%s, %s, %s);',
							esc_attr( $space ),
							esc_attr( $device ),
							esc_attr( tgwc_sanitize_dimension_unit( $value, 'px' ) )
						);
					}
				}
			}
			?>

			&:hover,
			&:active {
				color: $button_hover_font_color;
				background-color: $button_hover_background_color;
				<?php if ( 'none' !== $values['button']['hover']['border_style'] ) : ?>
					border-style: $button_hover_border_style;
					border-color: $button_hover_border_color;
					<?php tgwc_array_filter_numeric( $values['button']['hover']['border_width'] ) && printf( '@include border-width(%s);', esc_attr( tgwc_sanitize_dimension_unit( $values['button']['hover']['border_width'], 'px' ) ) ); ?>
				<?php endif; ?>
			}
		}
	}

	#tgwc-woocommerce.woocommerce {
		&[data-menu-layout="<?php echo esc_attr( $active_menu_style ); ?>"] {

			.tgwc-woocommerce-MyAccount-navigation {
				.woocommerce-MyAccount-navigation-link {
					a {
						color: $nav_font_color;
						background: $nav_background_color;
						<?php foreach ( array( 'padding' ) as $separator_type ) : ?>
							<?php foreach ( $values['navigation']['general'][ $separator_type ] as $device => $value ) : ?>
								<?php if ( in_array( $device, array( 'desktop', 'tablet', 'mobile' ), true ) && tgwc_array_filter_numeric( $value ) ) : ?>
									<?php
										printf(
											'@include responsive-media(%s, %s, %s);',
											esc_attr( $separator_type ),
											esc_attr( $device ),
											esc_attr( tgwc_sanitize_dimension_unit( $value, 'px' ) )
										);
									?>
								<?php endif; ?>
							<?php endforeach; ?>
						<?php endforeach; ?>
						<?php if ( isset( $values['navigation']['normal']['border_style'] ) ) : ?>
							border-style: $nav_border_style;
							<?php if ( 'none' !== $values['navigation']['normal']['border_style'] ) : ?>
								border-color: $nav_border_color;
								<?php tgwc_array_filter_numeric( $values['navigation']['normal']['border_width'] ) && printf( '@include border-width(%s);', esc_attr( tgwc_sanitize_dimension_unit( $values['navigation']['normal']['border_width'], 'px' ) ) ); ?>
							<?php endif; ?>
						<?php endif; ?>

						&:hover {
							color: $nav_hover_font_color;
							background: $nav_hover_background_color;
							<?php if ( isset( $values['navigation']['hover']['border_style'] ) ) : ?>
								border-style: $nav_hover_border_style;
								<?php if ( 'none' !== $values['navigation']['hover']['border_style'] ) : ?>
									border-color: $nav_hover_border_color;
									<?php tgwc_array_filter_numeric( $values['navigation']['hover']['border_width'] ) && printf( '@include border-width(%s);', esc_attr( tgwc_sanitize_dimension_unit( $values['navigation']['hover']['border_width'], 'px' ) ) ); ?>
								<?php endif; ?>
							<?php endif; ?>
						}
					}

					&.is-active {
						a {
							color: $nav_active_font_color;
							background: $nav_active_background_color;
							<?php if ( isset( $values['navigation']['active']['border_style'] ) ) : ?>
								border-style: $nav_active_border_style;
								<?php if ( 'none' !== $values['navigation']['active']['border_style'] ) : ?>
									border-color: $nav_active_border_color;
									<?php tgwc_array_filter_numeric( $values['navigation']['active']['border_width'] ) && printf( '@include border-width(%s);', esc_attr( tgwc_sanitize_dimension_unit( $values['navigation']['active']['border_width'], 'px' ) ) ); ?>
								<?php endif; ?>
							<?php endif; ?>
						}
					}
				}
			}
		}
	}


		<?php
		endif;
	?>

}


<?php

if ( 'minimal' === $active_menu_style ) {

	?>
	$button-background-color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $active_colors['button_background']['normal'] ) ) ); ?>;
	$button-hover-background-color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $active_colors['button_background']['hover'] ) ) ); ?>;
	$button-text-color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $active_colors['button_text']['normal'] ) ) ); ?>;
	$navigation-border-color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $active_colors['navigation_border']['normal'] ) ) ); ?>;
	$item-normal-text-color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $active_colors['item_text']['normal'] ) ) ); ?>;
	$item-hover-text-color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $active_colors['item_text']['hover'] ) ) ); ?>;
	$item-active-text-color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $active_colors['item_text']['active'] ) ) ); ?>;
	.logged-in.woocommerce-account.tgwc-woocommerce-customize-my-account {
	#tgwc-woocommerce.woocommerce {
		&[data-menu-layout="minimal"] {
			&[data-menu-position="vertical-left"], &[data-menu-position="vertical-right"] {
				.tgwc-woocommerce-MyAccount-navigation {
					border-right-color: $navigation-border-color;
					border-left-color: $navigation-border-color;


					.tgwc-user-avatar {
						.tgwc-user-info {
							.button {
								background: $button-background-color;
								color: $button-text-color;

								&:hover {
									background: $button-hover-background-color !important;
								}
							}
						}
					}

					&-wrap {
						ul {
							border: 0;

							li {
								a {
									color: $item-normal-text-color;

									&:hover {
										color: $item-hover-text-color;
									}
								}

								&.is-active {
									a {
										color: $item-active-text-color;
									}
								}
							}
						}
					}
				}
			}

			&[data-menu-position="tab"] {

				.tgwc-woocommerce-MyAccount-navigation {
					border-bottom-color: $navigation-border-color;

					.tgwc-user-avatar {

						.tgwc-user-info {
							.button {
								background: $button-background-color;
								color: $button-text-color;

								&:hover {
									background: $button-hover-background-color;
								}
							}
						}
					}

					&-wrap {

						ul {

							li {
								a {
									color: $item-normal-text-color;

									&:hover {
										color: $item-hover-text-color;
									}
								}

								&.is-active {
									a {
										color: $item-active-text-color;
									}
								}
							}
						}
					}
				}
			}
		}
	}
}
	<?php
} elseif ( 'modern' === $active_menu_style ) {

	?>
	$button-background-color:<?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $active_colors['button_background']['normal'] ) ) ); ?>;
	$button-hover-background-color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $active_colors['button_background']['hover'] ) ) ); ?>;
	$button-text-color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $active_colors['button_text']['normal'] ) ) ); ?>;
	$navigation-background-color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $active_colors['navigation_background']['normal'] ) ) ); ?>;
	$navigation-border-color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $active_colors['navigation_border']['normal'] ) ) ); ?>;
	$item-hover-background-color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $active_colors['item_background']['hover'] ) ) ); ?>;
	$item-active-background-color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $active_colors['item_background']['active'] ) ) ); ?>;
	$item-normal-text-color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $active_colors['item_text']['normal'] ) ) ); ?>;
	$item-hover-text-color:<?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $active_colors['item_text']['hover'] ) ) ); ?>;
	$item-active-text-color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $active_colors['item_text']['active'] ) ) ); ?>;
.logged-in.woocommerce-account.tgwc-woocommerce-customize-my-account {
	#tgwc-woocommerce.woocommerce {

		&[data-menu-layout="modern"] {

			&[data-menu-position="vertical-left"], &[data-menu-position="vertical-right"] {
				.tgwc-woocommerce-MyAccount-navigation {
					border-color: $navigation-border-color;

					.tgwc-user-avatar {
						background: $navigation-background-color;

						.tgwc-user-info {
							.button {
								background: $button-background-color;
								color: $button-text-color;

								&:hover {
									background: $button-hover-background-color;
								}
							}
						}
					}

					&-wrap {
						background: $navigation-background-color;

						ul {

							li {
								a {
									color: $item-normal-text-color;

									&:hover {
										background: $item-hover-background-color;
										color: $item-hover-text-color;
									}
								}

								&.is-active {
									a {
										background: $item-active-background-color;
										color: $item-active-text-color;
									}
								}
							}
						}
					}
				}
			}

			&[data-menu-position="tab"] {

				.tgwc-woocommerce-MyAccount-navigation {
					border-color: $navigation-border-color;

					.tgwc-user-avatar {
						background: $navigation-background-color;

						.tgwc-user-info {
							.button {
								background: $button-background-color;
								color: $button-text-color;

								&:hover {
									background: $button-hover-background-color;
								}
							}
						}
					}

					&-wrap {
						background: $navigation-background-color;

						ul {

							li {
								a {
									color: $item-normal-text-color;

									&:hover {
										background: $item-hover-background-color;
										color: $item-hover-text-color;
									}
								}

								&.is-active {
									a {
										background: $item-active-background-color;
										color: $item-active-text-color;
									}
								}
							}
						}
					}
				}
			}
		}
	}
}
	<?php
} elseif ( 'classic' === $active_menu_style ) {
	?>
	$button-background-color:<?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $active_colors['button_background']['normal'] ) ) ); ?>;
	$button-hover-background-color:<?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $active_colors['button_background']['hover'] ) ) ); ?>;
	$button-text-color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $active_colors['button_text']['normal'] ) ) ); ?>;
	$navigation-background-color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $active_colors['navigation_background']['normal'] ) ) ); ?>;
	$navigation-border-color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $active_colors['navigation_border']['normal'] ) ) ); ?>;
	$navigation-box-shadow:<?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $active_colors['navigation_box_shadow']['normal'] ) ) ); ?> 0px 0px 16px;
	$item-hover-background-color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $active_colors['item_background']['hover'] ) ) ); ?>;
	$item-active-background-color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $active_colors['item_background']['active'] ) ) ); ?>;
	$item-border-color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $active_colors['item_border']['active'] ) ) ); ?>;
	$item-normal-text-color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $active_colors['item_text']['normal'] ) ) ); ?>;
	$item-active-text-color: <?php echo esc_attr( tgwc_format_css_value( tgwc_clean( $active_colors['item_text']['active'] ) ) ); ?>;

.logged-in.woocommerce-account.tgwc-woocommerce-customize-my-account {
	#tgwc-woocommerce.woocommerce {
		&[data-menu-layout="classic"] {

			&[data-menu-position="vertical-left"], &[data-menu-position="vertical-right"] {
				.tgwc-woocommerce-MyAccount-navigation {
					background: $navigation-background-color;
					border-color: $navigation-border-color;
					box-shadow: $navigation-box-shadow;

					.tgwc-user-avatar {

						.tgwc-user-info {
							.button {
								background: $button-background-color;
								color: $button-text-color;

								&:hover {
									background: $button-hover-background-color;
								}
							}
						}
					}

					&-wrap {
						ul {

							li {
								a {
									color: $item-normal-text-color;

									&:hover {
										background: $item-hover-background-color;
									}
								}

							}
							.is-active a {
								border-left-color: $item-border-color;
								background: $item-active-background-color;
								color: $item-active-text-color;
							}
						}
					}
				}
			}

			&[data-menu-position="tab"] {

				.tgwc-woocommerce-MyAccount-navigation {

					background: $navigation-background-color;
					border-color: $navigation-border-color;
					box-shadow: $navigation-box-shadow !important;

					.tgwc-user-avatar {

						.tgwc-user-info {
							.button {
								background: $button-background-color;
								color: $button-text-color;

								&:hover {
									background: $button-hover-background-color;
								}
							}
						}
					}

					&-wrap {

						ul {

							li {

								a {
									color: $item-normal-text-color;

									&:hover {
										background: $item-hover-background-color;
									}
								}

							}
							.is-active {
								a {
									color: $item-active-text-color;
									border-bottom-color: $item-border-color;
									background: $item-active-background-color;
								}
							}
						}
					}
				}
			}
		}
	}
}
	<?php
}
?>
<?php
