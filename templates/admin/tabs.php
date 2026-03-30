<?php
/**
 * Customize page tabs list.g
 *
 * @since 0.1.0
 */

use ThemeGrill\WoocommerceCustomizer\Icon;

defined( 'ABSPATH' ) || exit;
?>

<div class="tgwc-header">
	<div class="nav-tab-wrapper">
		<div class="tgwc-brand">
			<?php printf( '<img src="%s" />', esc_url( TGWC()->plugin_url() . '/assets/images/wooCommerce-customize-my-account-logo.png' ) ); ?>
		</div>
		<div class="nav-tabs">
		<?php
		foreach ( $tabs as $tab_slug => $tab_name ) {
			$class = ( $tab_selected === $tab_slug ) ? ' tab-active' : '';
			printf(
				'<div class="tab-wrap"><a class="tab%1$s" href="%2$s"  id="%3$s"><p>%4$s</p></a></div>',
				esc_attr( $class ),
				'customizer' !== $tab_slug ? '?page=tgwc-customize-my-account-page&tab=' . esc_attr( $tab_slug ) : esc_url( $customize_url ),
				esc_attr( $tab_slug ) . '_tab',
				esc_html( $tab_name ),
			);
		}
		?>
		</div>
	</div>
	<?php
	if ( 'endpoints' === $tab_selected ) :
		$new_adds = array(
			'endpoint' => __( 'Add endpoint', 'customize-my-account-page-for-woocommerce' ),
			'group'    => __( 'Add group', 'customize-my-account-page-for-woocommerce' ),
			'link'     => __( 'Add link', 'customize-my-account-page-for-woocommerce' ),
		);
		?>
	<div class="actions tgwc-endpoint-actions">
		<?php echo render_view_my_account(); // Phpcs:ignore ?>
		<?php foreach ( $new_adds as $key => $new_add ) : ?>
		<button type="button" class="button" data-type="<?php echo esc_attr( $key ); ?>">
			<?php Icon::get_svg_icon( 'tgwc-' . $key, true ); ?>
			<p class="btn-text"><?php echo esc_html( $new_add ); ?></p>
		</button>
		<?php endforeach ?>
		<div class="tgwc-add-tab" style="display: none;">
			<button type="button" class="tgwc-add-tab-btn">
				<?php Icon::get_svg_icon( 'tgwc-circle-plus', true ); ?>
				<span class="label"><?php echo esc_html__( 'Add Tab', 'customize-my-account-page-for-woocommerce' ); ?></span>
			</button>
		</div>
	</div>
	<?php endif; ?>
</div>
<?php
