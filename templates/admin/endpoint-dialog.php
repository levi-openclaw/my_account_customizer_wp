<?php
/**
 * Endpoint dialog.
 *
 * @since 0.1.0
 */

defined( 'ABSPATH' ) || exit;
?>

<div id="tgwc-endpoint-dialog" style="display: none;">
	<form>
		<label for="label">
			<?php esc_html_e( 'Name', 'customize-my-account-page-for-woocommerce' ); ?>
		</label>

		<input type="text"
			id="tgwc-endpoint-dialog-name" />
		<input type="hidden"
			id="tgwc-endpoint-dialog-type" />

		<div class="tgwc-error-message">
		</div>
	</form>
</div>
<?php
