<?php
/**
 * Restore defaults modal dialog.
 *
 * @since 0.1.0
 */

use ThemeGrill\WoocommerceCustomizer\Icon;

defined( 'ABSPATH' ) || exit;
?>

<div id="tgwc-dialog-restore-defaults" style="display: none;" title="<?php esc_html_e( 'Confirm Restore', 'customize-my-account-page-for-woocommerce' ); ?>">
	<div class="tgwc-dialog-content">
		<p>
			<?php esc_html_e( 'All changes will be reverted to default, and custom endpoints will be deleted.', 'customize-my-account-page-for-woocommerce' ); ?>
		</p>
		<form style="display:none">
			<div>
				<input type="checkbox" checked="checked" id="tgwc-restore-defaults-endpoints" />
				<input type="checkbox" checked="checked" id="tgwc-restore-defaults-endpoints" />
				<label for="tgwc-restore-defaults-endpoints">
					<?php esc_html_e( 'Endpoint', 'customize-my-account-page-for-woocommerce' ); ?>
				</label>
			</div>
			<div>
				<input type="checkbox" id="tgwc-restore-defaults-settings" />
				<label for="tgwc-restore-defaults-settings">
					<?php esc_html_e( 'Settings', 'customize-my-account-page-for-woocommerce' ); ?>
				</label>
			</div>
			<div>
				<input type="checkbox" id="tgwc-restore-defaults-customization" />
				<label for="tgwc-restore-defaults-customization">
					<?php esc_html_e( 'Design Customization', 'customize-my-account-page-for-woocommerce' ); ?>
				</label>
			</div>
		</form>
	</div>
	<div class="tgwc-dialog-notice"></div>
</div>
