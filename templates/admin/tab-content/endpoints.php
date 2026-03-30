<?php
/**
 * Endpoints tab content page.
 *
 * @since 0.1.0
 */

use ThemeGrill\WoocommerceCustomizer\Icon;

defined( 'ABSPATH' ) || exit;
?>

<div id="tgwc-endpoints">
	<div id="tgwc-tabs" class="tgwc-tabs-with-sidenav">
		<div class="dd tgwc-sidenav background-white default-border-8 tgwc-sidenav--collapsed">
			<ul class="dd-list">
			<?php
			foreach ( $endpoints as $slug => $endpoint ) {
				$endpoint['slug'] = $slug;
				wc_get_template(
					'admin/endpoint-tab.php',
					$endpoint,
					TGWC_TEMPLATE_PATH,
					TGWC_TEMPLATE_PATH
				);
			}
			?>
			</ul>
			<div class="tgwc-add-tab">
				<button type="button" class="tgwc-add-tab-btn">
					<?php Icon::get_svg_icon( 'tgwc-circle-plus', true ); ?>
					<span class="label"><?php echo esc_html__( 'Add Tab', 'customize-my-account-page-for-woocommerce' ); ?></span>
				</button>
			</div>
			<svg class="tgwc-sidenav-toggle tgwc-sidenav-toggle--collapsed" style="display: none;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 8 12">
				<path fill-rule="evenodd" d="M.91.41a.833.833 0 0 1 1.18 0l5 5a.833.833 0 0 1 0 1.18l-5 5a.833.833 0 1 1-1.18-1.18L5.323 6 .91 1.59a.833.833 0 0 1 0-1.18Z" clip-rule="evenodd"/>
			</svg>
		</div>
		<div class="tgwc-sidecontent background-white default-border-8">
		<?php
		$initial = current( array_keys( $endpoints ) );
		foreach ( $endpoints as $slug => $endpoint ) {
			do_action( "tgwc_endpoints_content_{$endpoint['type']}", $slug, $endpoint, $initial );
			do_action( 'tgwc_endpoints_content', $slug, $endpoint, $initial );

			if ( isset( $endpoint['children'] ) ) {
				foreach ( $endpoint['children'] as $slug => $child ) {
					do_action( "tgwc_endpoints_content_{$child['type']}", $slug, $child, $initial );
					do_action( 'tgwc_endpoints_content', $slug, $child, $initial );
				}
			}
		}
		?>
		</div>
	</div>
</div>
<div id="tgwc-active-endpoint"><input name="tgwc_active_endpoint" type="hidden" value=""/></div>
<div id="tgwc-free-endpoint-state"><input name="tgwc_free_endpoint_state" type="hidden" value=""/></div>
<div id="tgwc-dialog-delete" style="display: none;"
	title="<?php esc_html_e( 'Confirm Delete', 'customize-my-account-page-for-woocommerce' ); ?>">
	<div class="tgwc-dialog-content">
		<p>
		<?php esc_html_e( 'It will be permanently deleted and cannot be recovered. Are you sure?', 'customize-my-account-page-for-woocommerce' ); ?>
		</p>
	</div>
</div>
<div id="tgwc-dialog-save-changes" style="display: none;"
	title="<?php esc_html_e( 'Do you want to continue?', 'customize-my-account-page-for-woocommerce' ); ?>">
	<div class="tgwc-dialog-content">
		<p>
			<?php esc_html_e( 'Unsaved changes will be permanently lost. Discard anyway.', 'customize-my-account-page-for-woocommerce' ); ?>
		</p>
	</div>
</div>
<div id="tgwc-dialog-add-tab" style="display: none;"
	title="<?php esc_html_e( 'Select Your Tab Type', 'customize-my-account-page-for-woocommerce' ); ?>">
	<div class="tgwc-dialog-content">
		<div class="tgwc-tab-list">
			<?php
			if ( 'endpoints' === $tab_selected ) :
				$new_adds = array(
					'endpoint' => __( 'Add endpoint', 'customize-my-account-page-for-woocommerce' ),
					'group'    => __( 'Add group', 'customize-my-account-page-for-woocommerce' ),
					'link'     => __( 'Add link', 'customize-my-account-page-for-woocommerce' ),
				);
				?>
			<div class="actions tgwc-endpoint-actions">
				<?php foreach ( $new_adds as $key => $new_add ) : ?>
				<button type="button" class="button" data-type="<?php echo esc_attr( $key ); ?>">
					<?php Icon::get_svg_icon( 'tgwc-' . $key, true ); ?>
					<p class="btn-text"><?php echo esc_html( $new_add ); ?></p>
				</button>
				<?php endforeach ?>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php
