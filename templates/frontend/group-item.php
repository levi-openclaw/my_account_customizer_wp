<?php
/**
 * Frontend group item.
 *
 * @since 0.1.0
 */

defined( 'ABSPATH' ) || exit;

use ThemeGrill\WoocommerceCustomizer\Icon;

?>
<li <?php printf( '%s', 'vertical-left' === tgwc_get_menu_style() && 'collapsed' === tgwc_get_group_accordion_default_state() ? esc_attr( 'data-collapsed=true' ) : '' ); ?> class="<?php echo esc_attr( wc_get_account_menu_item_classes( $slug ) ); ?>" data-is_loaded="yes">
	<a href="#"
		data-endpoint="<?php echo esc_attr( $slug ); ?>"
	>
		<span>
			<?php
				$customize    = get_option( 'tgwc_customize' );
				$is_show_icon = isset( $customize['navigation']['show_icon'] ) ? $customize['navigation']['show_icon'] : false;

			if ( $is_show_icon || is_customize_preview() ) {
				if ( 'choose_icon' === $choose_icon_type ) {
					$icon = str_replace( 'fas fa-', '', $icon );
					Icon::get_svg_icon( $icon, true );
				} elseif ( ! empty( $custom_icon ) ) {
					if ( wp_attachment_is_image( $custom_icon ) ) {
						echo wp_get_attachment_image( $custom_icon, 'thumbnail' );
					}
				}
			}
				echo esc_html( $label );
			?>
		</span>
		<?php
		if ( isset( $children ) && 'tab' !== tgwc_get_menu_style() ) {
			if ( 'collapsed' === tgwc_get_group_accordion_default_state() ) {
				Icon::get_svg_icon( 'chevron-right', true );
			} else {
				Icon::get_svg_icon( 'chevron-down', true );
			}
		}

		if ( isset( $children ) && 'tab' === tgwc_get_menu_style() ) {
			Icon::get_svg_icon( 'chevron-down', true );
		}
		?>
	</a>

<?php if ( isset( $children ) ) : ?>
	<ul style="<?php echo 'tab' === tgwc_get_menu_style() ? 'display: none;' : ( 'collapsed' === tgwc_get_group_accordion_default_state() ? 'display:none' : '' ); ?>">
		<?php
		$children = tgwc_get_account_menu_items( $children );
		foreach ( $children as $child_slug => $child ) {
			do_action( "tgwc_myaccount_menu_item_{$child_slug}", $child_slug );
			do_action( 'tgwc_my_account_menu_item', $child_slug );
		}
		?>
	</ul>
<?php endif; ?>
</li>
<?php
