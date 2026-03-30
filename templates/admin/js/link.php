<?php
/**
 * Link JS template.
 *
 * @since 0.1.0
 */

defined( 'ABSPATH' ) || exit;

use ThemeGrill\WoocommerceCustomizer\Icon;
?>
<script type="text/template" id="tmpl-tgwc-link">
	<div id="{{data.slug}}" class="tgwc-tabs-panel">
		<div class="tgwc-tabs-panel-header">
			<div class="header">
				<div class="header-left">
					<div class="type type-link">
						<?php Icon::get_svg_icon( 'tgwc-link', true ); ?>
					</div>
					<h2>{{data.text}}</h2>
				</div>
			</div>
		</div>
		<input type="hidden"
			name="tgwc_endpoints[{{data.slug}}][type]"
			value="{{data.type}}" />

		<div class="link settings-table">
		<div class="settings-table-wrapper">

				<!-- URL-->
				<div class="row">
					<div class="col-label">
						<p><?php esc_html_e( 'URL', 'customize-my-account-page-for-woocommerce' ); ?></p>
						<span data-toggle="tgwc-tooltip" title="<?php esc_attr_e( 'The web address that this tab opens.', 'customize-my-account-page-for-woocommerce' ); ?>">
							<?php Icon::get_svg_icon( 'tooltip', true ); ?>
						</span>
					</div>
					<div class="col-input">
						<input type="text" class="tgwc-link-url"
							value = "#"
							name="tgwc_endpoints[{{data.slug}}][url]" />
					</div>
				</div>
				<!-- ./ URL -->

				<!-- Label -->
				<div class="row">
					<div class="col-label">
						<p><?php esc_html_e( 'Link Label', 'customize-my-account-page-for-woocommerce' ); ?></p>
						<span data-toggle="tgwc-tooltip" title="<?php esc_attr_e( 'The display name shown for this link in the My Account menu.', 'customize-my-account-page-for-woocommerce' ); ?>">
							<?php Icon::get_svg_icon( 'tooltip', true ); ?>
						</span>
					</div>
					<div class="col-input">
						<input type="text"
							name="tgwc_endpoints[{{data.slug}}][label]"
							value="{{data.text}}" />
					</div>
				</div>
				<!-- ./ Label -->

				<!-- Choose Icon Type -->
				<div class="row">
					<div class="col-label">
						<p><?php esc_html_e( 'Link Icon', 'customize-my-account-page-for-woocommerce' ); ?></p>
						<span data-toggle="tgwc-tooltip" title="<?php esc_attr_e( 'Choose or upload an icon to display for this link on the My Account page.', 'customize-my-account-page-for-woocommerce' ); ?>">
							<?php Icon::get_svg_icon( 'tooltip', true ); ?>
						</span>
					</div>
					<div class="col-input">
						<div class="tgwc_choose_icon_type_outer_wrapper">
							<div class="tgwc_choose_icon_type_inner_wrapper active">
								<input type="radio" class="tgwc_choose_icon_type_radio_input" name="tgwc_endpoints[{{data.slug}}][choose_icon_type]" data-icon_type="choose_icon" value="choose_icon" data-endpoint_type="{{data.slug}}" checked
								>
								<span>Choose Icon</span>
							</div>
							<div class="tgwc_choose_icon_type_inner_wrapper">
								<input type="radio" class="tgwc_choose_icon_type_radio_input"  name="tgwc_endpoints[{{data.slug}}][choose_icon_type]" data-icon_type="custom_icon_upload" value="custom_icon_upload" data-endpoint_type="{{data.slug}}"
								> <span>Upload Icon</span>
							</div>
						</div>
					</div>
				</div>
				<!-- ./ Choose Icon Type -->

				<!-- Choose Icon -->
				<div class="row tgwc_choose_icon" id="tgwc_choose_icon_{{data.slug}}">
					<div class="col-label">
						<p><?php esc_html_e( 'Choose Icon', 'customize-my-account-page-for-woocommerce' ); ?></p>
					</div>
					<div class="col-input">
						<select
							data-selected="{{data.slug}}"
							name="tgwc_endpoints[{{data.slug}}][icon]">
						</select>
					</div>
				</div>
				<!-- ./ Choose Icon -->

				<!-- ./ Custom Icon Uploader -->
				<div class="tgwc_custom_icon_uploader_outer_wrapper row tgwc_hidden" id="tgwc_custom_icon_uploader_{{data.slug}}">
					<div class="col-label">
						<p><?php esc_html_e( 'Choose Icon', 'customize-my-account-page-for-woocommerce' ); ?></p>
					</div>
					<div class="col-input tgwc_custom_icon_uploader_inner_uploader_wrapper">
						<div class="tgwc_custom_icon_uploader_box" data-endpoint-key="{{data.slug}}">
							<span class="tgwc-loader" style="display: none;"></span>
							<input type="hidden"
								name="tgwc_endpoints[{{data.slug}}][custom_icon]"
								value=""
								class="tgwc-custom-icon-id">

							<div class="tgwc_custom_icon_uploader_inner_content_wrapper">
								<div class="tgwc_custom_icon_inner_content">
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="28" viewBox="0 0 20 28" fill="none">
										<path fill-rule="evenodd" clip-rule="evenodd" d="M5.71429 19.7238H14.2857V11.1523H20L10 1.15234L0 11.1523H5.71429V19.7238Z" fill="#929292"/>
										<path d="M20 22.5811H0V25.4382H20V22.5811Z" fill="#929292"/>
									</svg>
								</div>
								<div class="tgwc_inner_text">
									<p>Drag and Drop here</p>
									<p>or</p>
									<p class="tgwc_browse_files">Upload a file</p>
								</div>
							</div>
							<div class="tgwc-custom-icon-preview tgwc_hidden">
								<div class="tgwc_uploader_action_container">
									<div class="tgwc_uploader_edit_action">
										<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
										<path d="M19.014 6.368a1.384 1.384 0 0 0-.853-1.277 1.383 1.383 0 0 0-1.507.3L5.93 16.114l-.733 2.689 2.688-.734L18.61 7.346a1.384 1.384 0 0 0 .404-.978Zm1.8 0a3.185 3.185 0 0 1-.932 2.25L8.988 19.512a.9.9 0 0 1-.399.232l-4.438 1.21a.9.9 0 0 1-1.105-1.104l1.21-4.439.038-.11a.9.9 0 0 1 .195-.29L15.382 4.119a3.182 3.182 0 0 1 5.433 2.25Z"/>
										</svg>

									</div>
									<div class="tgwc_uploader_delete_action">
										<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25 25">
									<path d="M17.855 5.876a1 1 0 1 1 1.414 1.414l-12 12a1 1 0 1 1-1.414-1.414l12-12Z"/>
									<path d="M5.855 5.876a1 1 0 0 1 1.414 0l12 12 .068.076a1 1 0 0 1-1.406 1.406l-.076-.068-12-12a1 1 0 0 1 0-1.414Z"/>
									</svg>
									</div>
								</div>
							</div>
						</div>
						<div class="tgwc_custom_icon_uploader_inner_text">
							<span>Upload your custom icon.</span>
						</div>

					</div>
				</div>
				<!-- ./ Custom Icon Uploader -->

				<!-- Class -->
				<div class="row">
					<div class="col-label">
						<p><?php esc_html_e( 'CSS Class', 'customize-my-account-page-for-woocommerce' ); ?></p>
						<span data-toggle="tgwc-tooltip" title="<?php esc_attr_e( 'Add additional CSS classes for advanced styling.', 'customize-my-account-page-for-woocommerce' ); ?>">
							<?php Icon::get_svg_icon( 'tooltip', true ); ?>
						</span>
					</div>
					<div class="col-input">
						<input type="text"
							name="tgwc_endpoints[{{data.slug}}][class]" />
					</div>
				</div>
				<!-- ./ Class -->

				<!-- User roles -->
				<div class="row">
					<div class="col-label">
						<p><?php esc_html_e( 'User Roles', 'customize-my-account-page-for-woocommerce' ); ?></p>
						<span data-toggle="tgwc-tooltip" title="<?php esc_attr_e( 'Choose which types of users (e.g., customers, shop managers, or administrators) can view this link.', 'customize-my-account-page-for-woocommerce' ); ?>">
							<?php Icon::get_svg_icon( 'tooltip', true ); ?>
						</span>
					</div>
					<div class="col-input">
						<select name="tgwc_endpoints[{{data.slug}}][user_role]">
						</select>
					</div>
				</div>
				<!-- ./ User roles -->

				<!-- Open in new tab -->
				<div class="row">
					<div class="col-label">
						<p><?php esc_html_e( 'Open Link In a New Tab?', 'customize-my-account-page-for-woocommerce' ); ?></p>
					</div>
					<div class="col-input">
						<div class="tgwc-toggle-section">
							<span class="tgwc-toggle-form">
								<input type="checkbox"
							name="tgwc_endpoints[{{data.slug}}][new_tab]" style="min-width: 350px;" />
								<span class="slider round"></span>
							</span>
						</div>
					</div>
				</div>
				<!-- ./ Open in new tab -->
			</div>
		</div>
		</div>
	</div>
</script>
<?php
