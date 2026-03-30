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

				<?php
				$eligibility_access = \ThemeGrill\WoocommerceCustomizer\EligibilityAccess::instance();
				$eligibility_rules  = $eligibility_access->get_rules();
				$eligibility_saved  = $eligibility_access->get_eligibility_settings();
				?>
				<?php if ( ! empty( $eligibility_rules ) ) : ?>
				<div class="tgwc-settings-developer-section">
					<div class="tgwc-section-header"><?php esc_html_e( 'Eligibility Access', 'customize-my-account-page-for-woocommerce' ); ?></div>
					<div class="row tgwc-settings-row" style="margin-bottom: 0;">
						<div class="col-label tgwc-settings-col-label" style="flex: 0 0 100%; max-width: 100%; margin-bottom: 12px;">
							<span class="setting-help"><?php esc_html_e( 'Hide menu items from users who don\'t have relevant data. When enabled, the tab is only visible to users who qualify.', 'customize-my-account-page-for-woocommerce' ); ?></span>
						</div>
					</div>
					<?php foreach ( $eligibility_rules as $rule_key => $rule ) : ?>
					<div class="row tgwc-settings-row">
						<div class="col-label tgwc-settings-col-label">
							<p class="setting-label"><?php echo esc_html( $rule['label'] ); ?></p>
							<span class="setting-help"><?php echo esc_html( $rule['description'] ); ?></span>
						</div>
						<div class="col-input">
							<div class="tgwc-toggle-section">
								<span class="tgwc-toggle-form">
									<input type="checkbox"
										<?php checked( ! empty( $eligibility_saved[ $rule_key ] ) ); ?>
										name="tgwc_settings[eligibility][<?php echo esc_attr( $rule_key ); ?>]"
										value="1" />
									<span class="slider round"></span>
								</span>
							</div>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>

				<?php
				$custom_rules = isset( $settings['custom_endpoint_rules'] ) ? $settings['custom_endpoint_rules'] : array();
				// Ensure at least one empty row for adding.
				if ( empty( $custom_rules ) ) {
					$custom_rules[] = array( 'endpoint' => '', 'product_ids' => '', 'require_active' => false );
				}
				?>
				<div class="tgwc-settings-developer-section">
					<div class="tgwc-section-header"><?php esc_html_e( 'Subscription-Based Endpoint Rules', 'customize-my-account-page-for-woocommerce' ); ?></div>
					<div class="row tgwc-settings-row" style="margin-bottom: 0;">
						<div class="col-label tgwc-settings-col-label" style="flex: 0 0 100%; max-width: 100%; margin-bottom: 12px;">
							<span class="setting-help"><?php esc_html_e( 'Hide endpoints unless the user has a subscription containing specific product IDs. "Require Active" means only active or pending-cancellation subscriptions count.', 'customize-my-account-page-for-woocommerce' ); ?></span>
						</div>
					</div>
					<div id="tgwc-custom-rules-wrapper">
						<?php foreach ( $custom_rules as $i => $rule ) : ?>
						<div class="row tgwc-settings-row tgwc-custom-rule-row" style="align-items: flex-start; gap: 8px; flex-wrap: wrap;">
							<div style="flex: 1; min-width: 140px;">
								<label style="display: block; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 4px; color: rgba(23,25,21,0.5);"><?php esc_html_e( 'Endpoint Slug', 'customize-my-account-page-for-woocommerce' ); ?></label>
								<input type="text" name="tgwc_settings[custom_endpoint_rules][<?php echo esc_attr( $i ); ?>][endpoint]" value="<?php echo esc_attr( $rule['endpoint'] ); ?>" placeholder="e.g. choose-baseluts" style="width: 100%;" />
							</div>
							<div style="flex: 1; min-width: 140px;">
								<label style="display: block; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 4px; color: rgba(23,25,21,0.5);"><?php esc_html_e( 'Product IDs', 'customize-my-account-page-for-woocommerce' ); ?></label>
								<input type="text" name="tgwc_settings[custom_endpoint_rules][<?php echo esc_attr( $i ); ?>][product_ids]" value="<?php echo esc_attr( is_array( $rule['product_ids'] ) ? implode( ', ', $rule['product_ids'] ) : $rule['product_ids'] ); ?>" placeholder="e.g. 123, 456" style="width: 100%;" />
							</div>
							<div style="flex: 0 0 auto; padding-top: 22px;">
								<label style="display: flex; align-items: center; gap: 6px; font-size: 13px; white-space: nowrap;">
									<input type="checkbox" name="tgwc_settings[custom_endpoint_rules][<?php echo esc_attr( $i ); ?>][require_active]" value="1" <?php checked( ! empty( $rule['require_active'] ) ); ?> />
									<?php esc_html_e( 'Require Active', 'customize-my-account-page-for-woocommerce' ); ?>
								</label>
							</div>
							<div style="flex: 0 0 auto; padding-top: 20px;">
								<button type="button" class="button tgwc-remove-rule" onclick="this.closest('.tgwc-custom-rule-row').remove();" style="color: #a00;">&times;</button>
							</div>
						</div>
						<?php endforeach; ?>
					</div>
					<div style="margin-top: 8px; margin-bottom: 16px;">
						<button type="button" class="button" id="tgwc-add-rule"><?php esc_html_e( '+ Add Rule', 'customize-my-account-page-for-woocommerce' ); ?></button>
					</div>
					<script>
					document.getElementById('tgwc-add-rule').addEventListener('click', function() {
						var wrapper = document.getElementById('tgwc-custom-rules-wrapper');
						var idx = wrapper.querySelectorAll('.tgwc-custom-rule-row').length;
						var html = '<div class="row tgwc-settings-row tgwc-custom-rule-row" style="align-items: flex-start; gap: 8px; flex-wrap: wrap;">'
							+ '<div style="flex: 1; min-width: 140px;">'
							+ '<label style="display: block; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 4px; color: rgba(23,25,21,0.5);">Endpoint Slug</label>'
							+ '<input type="text" name="tgwc_settings[custom_endpoint_rules][' + idx + '][endpoint]" value="" placeholder="e.g. choose-baseluts" style="width: 100%;" />'
							+ '</div>'
							+ '<div style="flex: 1; min-width: 140px;">'
							+ '<label style="display: block; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 4px; color: rgba(23,25,21,0.5);">Product IDs</label>'
							+ '<input type="text" name="tgwc_settings[custom_endpoint_rules][' + idx + '][product_ids]" value="" placeholder="e.g. 123, 456" style="width: 100%;" />'
							+ '</div>'
							+ '<div style="flex: 0 0 auto; padding-top: 22px;">'
							+ '<label style="display: flex; align-items: center; gap: 6px; font-size: 13px; white-space: nowrap;">'
							+ '<input type="checkbox" name="tgwc_settings[custom_endpoint_rules][' + idx + '][require_active]" value="1" />'
							+ ' Require Active'
							+ '</label>'
							+ '</div>'
							+ '<div style="flex: 0 0 auto; padding-top: 20px;">'
							+ '<button type="button" class="button tgwc-remove-rule" onclick="this.closest(\'.tgwc-custom-rule-row\').remove();" style="color: #a00;">&times;</button>'
							+ '</div>'
							+ '</div>';
						wrapper.insertAdjacentHTML('beforeend', html);
					});
					</script>
				</div>

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
