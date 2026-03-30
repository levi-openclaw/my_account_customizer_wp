<?php
/**
 * Frontend link item.
 *
 * @since 0.1.0
 */

defined( 'ABSPATH' ) || exit;

use ThemeGrill\WoocommerceCustomizer\Icon;

$default_endpoint = tgwc_get_default_endpoint();
$dashboard_url    = '';
if ( 'dashboard' !== $default_endpoint && 'dashboard' === $slug ) {
	$dashboard_url = 'dashboard';
}
?>
<li class="<?php echo esc_attr( wc_get_account_menu_item_classes( $slug ) ); ?>">
	<a href="<?php echo esc_url( wc_get_account_endpoint_url( $slug ) . $dashboard_url ); ?>"
		data-endpoint="<?php echo esc_attr( $slug ); ?>">
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
	</a>
</li>
<?php
