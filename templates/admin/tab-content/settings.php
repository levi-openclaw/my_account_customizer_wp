<?php
/**
 * Settings tab content page.
 *
 * @since 0.1.0
 */

defined( 'ABSPATH' ) || exit;

use ThemeGrill\WoocommerceCustomizer\Icon;
?>
<div class="settings-content-wrapper">

		<div class="settings settings-table">
			<div class="settings-table-wrapper">
								<!-- Default endpoint -->
				<div class="row tgwc-settings-row">
					<div class="col-label tgwc-settings-col-label">
						<p class="setting-label"><?php esc_html_e( 'Default Endpoint', 'customize-my-account-page-for-woocommerce' ); ?></p>
						<span class="setting-help"><?php esc_html_e( 'Select which tab users will see first when they visit their account page.', 'customize-my-account-page-for-woocommerce' ); ?></span>
					</div>
					<div class="col-input">
						<select name="tgwc_settings[default_endpoint]" class="tgwc_settings_default_endpoints">
						<?php
						foreach ( tgwc_get_endpoints_by_type( 'endpoint' ) as $account_key => $account_item ) {
							$selected = selected( $account_key, $settings['default_endpoint'], false );
							printf(
								'<option %s value=%s>%s</option>',
								esc_attr( $selected ),
								esc_attr( $account_key ),
								esc_html( $account_item['label'] )
							);
						}
						?>
						</select>
					</div>
				</div>
				<!-- ./ Default endpoint -->

				<!-- Custom avatar -->
				<div class="row tgwc-settings-row">
					<div class="col-label tgwc-settings-col-label">
						<p class="setting-label"><?php esc_html_e( 'Account Profile Picture', 'customize-my-account-page-for-woocommerce' ); ?></p>
						<span class="setting-help"><?php esc_html_e( 'Allow customers to upload a profile picture on their WooCommerce account page.', 'customize-my-account-page-for-woocommerce' ); ?></span>
					</div>
					<div class="col-input">
						<div class="tgwc-toggle-section">
							<span class="tgwc-toggle-form">
								<input type="checkbox"
							<?php checked( $settings['custom_avatar'] ); ?>
							name="tgwc_settings[custom_avatar]" style="min-width: 350px;" />
								<span class="slider round"></span>
							</span>
						</div>
					</div>
				</div>
				<!-- ./ Custom avatar -->
				<!-- Ajax navigation  -->
				<div class="row tgwc-settings-row" style="margin-bottom: 0;">
					<div class="col-label tgwc-settings-col-label">
						<p class="setting-label"><?php esc_html_e( 'AJAX Account Navigation', 'customize-my-account-page-for-woocommerce' ); ?></p>
						<span class="setting-help"><?php esc_html_e( 'Load account page tabs without full page reload for smoother navigation experience.', 'customize-my-account-page-for-woocommerce' ); ?></span>
					</div>
					<div class="col-input">
						<div class="tgwc-toggle-section">
							<span class="tgwc-toggle-form">
								<input type="checkbox"
							<?php checked( $settings['enable_ajax_navigation'] ); ?>
							name="tgwc_settings[enable_ajax_navigation]" style="min-width: 350px;" />
								<span class="slider round"></span>
							</span>
						</div>
					</div>
				</div>
				<!-- ./ End Ajax navigation -->

				<div class="tgwc-settings-developer-section">
					<div class="tgwc-section-header">Developer Options</div>
					<!-- Enable debug -->
					<div class="row tgwc-settings-row">
						<div class="col-label tgwc-settings-col-label">
							<p class="setting-label"><?php esc_html_e( 'Load Unminified Assets', 'customize-my-account-page-for-woocommerce' ); ?></p>
							<span class="setting-help"><?php esc_html_e( 'Load uncompressed CSS and JS files to help with debugging and development.', 'customize-my-account-page-for-woocommerce' ); ?></span>
						</div>
						<div class="col-input">
							<div class="tgwc-toggle-section">
								<span class="tgwc-toggle-form">
									<input type="checkbox" value="1"
								<?php checked( $settings['enable_debug'] ); ?>
								name="tgwc_settings[enable_debug]" style="min-width: 350px;" />
									<span class="slider round"></span>
								</span>
							</div>
						</div>
					</div>
					<!-- Account Page Libraries -->
					<div class="row tgwc-settings-row">
						<div class="col-label tgwc-settings-col-label">
							<p class="setting-label"><?php esc_html_e( 'Account Page Libraries', 'customize-my-account-page-for-woocommerce' ); ?></p>
							<span class="setting-help"><?php esc_html_e( 'Control which CSS and JS libraries are loaded on the WooCommerce account page. Uncheck to test for theme or plugin conflicts.', 'customize-my-account-page-for-woocommerce' ); ?></span>
						</div>
						<div class="col-input tgwc-settings-developer-col-input">
							<label for="tgwc-frontend-dropzone-css">
								<input type="checkbox" id="tgwc-frontend-dropzone-css" <?php checked( $settings['frontend']['dropzone']['css'] ); ?> name="tgwc_settings[frontend][dropzone][css]" />
								<span style="margin-left: 8px;">
											<?php esc_html_e( 'Dropzone - CSS', 'customize-my-account-page-for-woocommerce' ); ?>
										</span>
							</label>

							<label for="tgwc-frontend-dropzone-js">
								<input type="checkbox" id="tgwc-frontend-dropzone-js" <?php checked( $settings['frontend']['dropzone']['js'] ); ?> name="tgwc_settings[frontend][dropzone][js]" />
								<span style="margin-left: 8px;">
											<?php esc_html_e( 'Dropzone - JS', 'customize-my-account-page-for-woocommerce' ); ?>
										</span>
							</label>
							<label for="tgwc-frontend-jqueryscrolltabs-css">
								<input type="checkbox" id="tgwc-frontend-jqueryscrolltabs-css" <?php checked( $settings['frontend']['jqueryscrolltabs']['css'] ); ?> name="tgwc_settings[frontend][jqueryscrolltabs][css]" />
								<span style="margin-left: 8px;">
											<?php esc_html_e( 'jQuery Scroll Tabs - CSS', 'customize-my-account-page-for-woocommerce' ); ?>
										</span>
							</label>

							<label for="tgwc-frontend-jqueryscrolltabs-js">
								<input type="checkbox" id="tgwc-frontend-jqueryscrolltabs-js" <?php checked( $settings['frontend']['jqueryscrolltabs']['js'] ); ?> name="tgwc_settings[frontend][jqueryscrolltabs][js]" />
								<span style="margin-left: 8px;">
											<?php esc_html_e( 'jQuery Scroll Tabs - JS', 'customize-my-account-page-for-woocommerce' ); ?>
										</span>
							</label>
						</div>
					</div>
				</div>
			</div>
		</div>
</div>
<?php
