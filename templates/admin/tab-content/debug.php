<?php
/**
 * Debug tab content page.
 *
 * @since 0.1.0
 */

defined( 'ABSPATH' ) || exit;

use ThemeGrill\WoocommerceCustomizer\Icon;

?>
<div class="debug-content-wrapper">
		<div class="debug settings-table">
			<div class="settings-table-wrapper">
			<!-- Enable debug -->
				<div class="row">
					<div class="col-label">
						<p><?php esc_html_e( 'Enable Debug', 'customize-my-account-page-for-woocommerce' ); ?></p>
						<span data-toggle="tgwc-tooltip"
							title="<?php esc_attr_e( 'Enabling this option will load unminified JS scripts and CSS styles.', 'customize-my-account-page-for-woocommerce' ); ?>">
							<?php Icon::get_svg_icon( 'tooltip', true ); ?>
						</span>
					</div>
					<div class="col-input">
						<div class="tgwc-toggle-section">
							<span class="tgwc-toggle-form">
								<input type="checkbox" value="1"
							<?php checked( $debug['enable_debug'] ); ?>
							name="tgwc_debug_settings[enable_debug]" style="min-width: 350px;" />
								<span class="slider round"></span>
							</span>
						</div>
						<label class="enable">
							<?php esc_html_e( 'Enable debug mode.', 'customize-my-account-page-for-woocommerce' ); ?>
						</label>
					</div>
				</div>
				<div class="row">
					<div class="col-label">
						<div class="tgwc-frontend-libraries">
							<h2><?php esc_html_e( 'Frontend Libraries', 'customize-my-account-page-for-woocommerce' ); ?></h2>
							<p><?php esc_html_e( 'You can enable/disable javascript libraries which are loaded in WooCommerce MyAccount Page to resolve library conflict with other plugins.', 'customize-my-account-page-for-woocommerce' ); ?></p>
						</div>
					</div>
				</div>
				<!-- Dropzone -->
				<div class="row">
					<div class="col-label">
						<p><?php esc_html_e( 'Dropzone', 'customize-my-account-page-for-woocommerce' ); ?></p>
					</div>
					<div class="col-input">
						<label for="tgwc-frontend-dropzone-css">
							<input type="checkbox" id="tgwc-frontend-dropzone-css" <?php checked( $debug['frontend']['dropzone']['css'] ); ?> name="tgwc_debug_settings[frontend][dropzone][css]" />
							<span style="margin-right: 25px;">
										<?php esc_html_e( 'CSS', 'customize-my-account-page-for-woocommerce' ); ?>
									</span>
						</label>

						<label for="tgwc-frontend-dropzone-js">
							<input type="checkbox" id="tgwc-frontend-dropzone-js" <?php checked( $debug['frontend']['dropzone']['js'] ); ?> name="tgwc_debug_settings[frontend][dropzone][js]" />
							<span>
										<?php esc_html_e( 'JS', 'customize-my-account-page-for-woocommerce' ); ?>
									</span>
						</label>
					</div>
				</div>
				<!-- jQuery Scroll Tabs -->
				<div class="row">
					<div class="col-label">
						<p><?php esc_html_e( 'jQuery Scroll Tabs', 'customize-my-account-page-for-woocommerce' ); ?></p>
					</div>
					<div class="col-input">
						<label for="tgwc-frontend-jqueryscrolltabs-css">
							<input type="checkbox" id="tgwc-frontend-jqueryscrolltabs-css" <?php checked( $debug['frontend']['jqueryscrolltabs']['css'] ); ?> name="tgwc_debug_settings[frontend][jqueryscrolltabs][css]" />
							<span style="margin-right: 25px;">
										<?php esc_html_e( 'CSS', 'customize-my-account-page-for-woocommerce' ); ?>
									</span>
						</label>

						<label for="tgwc-frontend-jqueryscrolltabs-js">
							<input type="checkbox" id="tgwc-frontend-jqueryscrolltabs-js" <?php checked( $debug['frontend']['jqueryscrolltabs']['js'] ); ?> name="tgwc_debug_settings[frontend][jqueryscrolltabs][js]" />
							<span>
										<?php esc_html_e( 'JS', 'customize-my-account-page-for-woocommerce' ); ?>
									</span>
						</label>
					</div>
				</div>
			</div>
		</div>
</div>

<?php
