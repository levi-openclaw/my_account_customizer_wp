<?php
/**
 * Frontend custom item.
 *
 * @since 0.1.0
 */

defined( 'ABSPATH' ) || exit;

$class = is_array( $class ) ? implode( ' ', $class ) : $class;
?>
<li class="<?php echo esc_attr( wc_get_account_menu_item_classes( $slug ) . ' ' . $class ); ?>">
	<a href="<?php echo esc_url( $url ); ?>" data-endpoint="<?php echo esc_attr( $slug ); ?>">
		<?php echo esc_html( $label ); ?>
	</a>
</li>
