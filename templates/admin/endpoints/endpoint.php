<?php
/**
 * Endpoint template.
 *
 * @since 0.1.0
 */

defined( 'ABSPATH' ) || exit;

use ThemeGrill\WoocommerceCustomizer\Icon;
?>

<div id="<?php echo esc_attr( $key ); ?>" class="tgwc-tabs-panel"
	style="<?php echo esc_attr( $initial === $key ? '' : 'display: none;' ); ?> ">
	<div class="tgwc-tabs-panel-header">
		<div class="header">
			<div class="header-left">
				<div class="type type-endpoint" >
					<?php Icon::get_svg_icon( 'tgwc-endpoint', true ); ?>
				</div>
				<h2><?php echo esc_html( $endpoint['label'] ); ?></h2>
			</div>
		</div>
	</div>

	<input type="hidden"
		name="tgwc_endpoints[<?php echo esc_attr( $key ); ?>][type]"
		value="<?php echo esc_attr( $endpoint['type'] ); ?>" />

	<div class="endpoint settings-table">
		<div class="settings-table-wrapper">
			<?php
			if ( 'customer-logout' === $key && '' !== get_option( 'tgwc_override_endpoint_logout_button', '' ) && ! empty( $endpoint['enable'] ) ) :
				$settings_url = admin_url( 'customize.php' );

				$customizer_link = add_query_arg(
					array(
						'tgwc-customizer' => true,
						'return'          => rawurlencode( $settings_url ),
						'url'             => rawurlencode( wc_get_page_permalink( 'myaccount' ) ),
					),
					admin_url( 'customize.php' )
				);

				$notice = sprintf(
				/* translators: %s is the link to the Customizer > Navigation Settings */
					esc_html__( 'This logout link is disabled because logout button is active. To enable this link, disable logout button on %s first', 'customize-my-account-for-woocommerce-lite' ),
					'<a href="' . esc_url( $customizer_link ) . '" target="_blank"> Customizer > Navigation Menu > Logout Button</a>'
				);
				?>
			<div class="tgwc-notice tgwc-notice-error customer-logout-endpoint-notice">
					<span ><?php echo wp_kses_post( $notice ); ?></span>
			</div>
			<?php endif; ?>
			<!-- Label -->
			<div class="row">
				<div class="col-label">
					<p><?php esc_html_e( 'Endpoint Label', 'customize-my-account-page-for-woocommerce' ); ?></p>
					<span data-toggle="tgwc-tooltip" title="<?php esc_attr_e( 'The display name shown for this tab in the My Account menu.', 'customize-my-account-page-for-woocommerce' ); ?>">
						<?php Icon::get_svg_icon( 'tooltip', true ); ?>
					</span>
				</div>
				<div class="col-input">
					<input type="text"
						name="tgwc_endpoints[<?php echo esc_attr( $key ); ?>][label]"
						value="<?php echo esc_attr( $endpoint['label'] ); ?>" />
				</div>
			</div>
			<!-- ./ Label -->

			<!-- Slug -->
		<?php if ( ! empty( $endpoint['slug'] ) ) : ?>
			<div class="row">
				<div class="col-label">
				<p>	<?php esc_html_e( 'Slug', 'customize-my-account-page-for-woocommerce' ); ?></p>
					<span data-toggle="tgwc-tooltip" title="<?php esc_attr_e( 'The text that appears in the URL for this tab.', 'customize-my-account-page-for-woocommerce' ); ?>">
						<?php Icon::get_svg_icon( 'tooltip', true ); ?>
					</span>
				</div>
				<div class="col-input">
					<input type="text"
						class="tgwc-endpoint-slug"
						name="tgwc_endpoints[<?php echo esc_attr( $key ); ?>][slug]"
						value="<?php echo esc_attr( $key ); ?>" />

				</div>
			</div>
			<!-- ./ Slug -->
		<?php endif; ?>

			<!-- Choose Icon Type -->
			<div class="row">
				<div class="col-label">
					<p><?php esc_html_e( 'Endpoint Icon', 'customize-my-account-page-for-woocommerce' ); ?></p>
					<span data-toggle="tgwc-tooltip" title="<?php esc_attr_e( 'Choose or upload an icon to display for this tab on the My Account page.', 'customize-my-account-page-for-woocommerce' ); ?>">
						<?php Icon::get_svg_icon( 'tooltip', true ); ?>
					</span>
				</div>
				<div class="col-input">
					<?php
					$choose_icon_type = ! empty( $endpoint['choose_icon_type'] ) ? $endpoint['choose_icon_type'] : 'choose_icon';
					?>
					<div class="tgwc_choose_icon_type_outer_wrapper">
						<div class="tgwc_choose_icon_type_inner_wrapper <?php echo 'choose_icon' === $choose_icon_type ? esc_attr( 'active' ) : ''; ?>">
							<input type="radio" class="tgwc_choose_icon_type_radio_input" name="tgwc_endpoints[<?php echo esc_attr( $key ); ?>][choose_icon_type]" data-icon_type="choose_icon" value="choose_icon" data-endpoint_type="<?php echo esc_attr( $key ); ?>"
								<?php checked( $choose_icon_type, 'choose_icon' ); ?>
							>
							<span>Choose Icon</span>
						</div>
						<div class="tgwc_choose_icon_type_inner_wrapper <?php echo 'custom_icon_upload' === $choose_icon_type ? esc_attr( 'active' ) : ''; ?>">
							<input type="radio" class="tgwc_choose_icon_type_radio_input"  name="tgwc_endpoints[<?php echo esc_attr( $key ); ?>][choose_icon_type]" data-icon_type="custom_icon_upload" value="custom_icon_upload" data-endpoint_type="<?php echo esc_attr( $key ); ?>"
								<?php checked( $choose_icon_type, 'custom_icon_upload' ); ?>
							> <span>Upload Icon</span>
						</div>
					</div>
				</div>
			</div>
			<!-- ./ Choose Icon Type -->

			<!-- Choose Icon -->
			<div class="row tgwc_choose_icon <?php echo 'choose_icon' !== $choose_icon_type ? esc_attr( 'tgwc_hidden' ) : ''; ?>" id="tgwc_choose_icon_<?php echo esc_attr( $key ); ?>">
				<div class="col-label">
					<p><?php esc_html_e( 'Choose Icon', 'customize-my-account-page-for-woocommerce' ); ?></p>
				</div>
				<div class="col-input">
					<select
						data-selected="<?php echo esc_attr( $endpoint['icon'] ); ?>"
						name="tgwc_endpoints[<?php echo esc_attr( $key ); ?>][icon]">
					</select>
				</div>
			</div>
			<!-- ./ Choose Icon -->

			<!-- ./ Custom Icon Uploader -->
			<div class="tgwc_custom_icon_uploader_outer_wrapper row <?php echo 'custom_icon_upload' !== $choose_icon_type ? esc_attr( 'tgwc_hidden' ) : ''; ?>" id="tgwc_custom_icon_uploader_<?php echo esc_attr( $key ); ?>">
				<div class="col-label">
					<p><?php esc_html_e( 'Choose Icon', 'customize-my-account-page-for-woocommerce' ); ?></p>
				</div>
				<div class="col-input tgwc_custom_icon_uploader_inner_uploader_wrapper">
					<div class="tgwc_custom_icon_uploader_box <?php echo esc_attr( ! empty( $endpoint['custom_icon'] ) ? 'has_icon' : '' ); ?>" data-endpoint-key="<?php echo esc_attr( $key ); ?>">
						<span class="tgwc-loader" style="display: none;"></span>
						<input type="hidden"
							name="tgwc_endpoints[<?php echo esc_attr( $key ); ?>][custom_icon]"
							value="<?php echo esc_attr( isset( $endpoint['custom_icon'] ) ? $endpoint['custom_icon'] : '' ); ?>"
							class="tgwc-custom-icon-id">

						<div class="tgwc_custom_icon_uploader_inner_content_wrapper <?php echo esc_attr( ! empty( $endpoint['custom_icon'] ) ? 'has_icon' : '' ); ?>">
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
						<div class="tgwc-custom-icon-preview <?php echo esc_attr( empty( $endpoint['custom_icon'] ) ? 'tgwc_hidden' : '' ); ?>">
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
							<?php if ( ! empty( $endpoint['custom_icon'] ) ) : ?>
								<?php echo wp_get_attachment_image( $endpoint['custom_icon'], 'thumbnail' ); ?>
							<?php endif; ?>
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
						name="tgwc_endpoints[<?php echo esc_attr( $key ); ?>][class]"
						value="<?php echo esc_attr( $endpoint['class'] ); ?>" />
				</div>
			</div>
			<!-- ./ Class -->

			<!-- User Roles -->
			<div class="row">
				<div class="col-label">
					<p><?php esc_html_e( 'User Roles', 'customize-my-account-page-for-woocommerce' ); ?></p>
					<span data-toggle="tgwc-tooltip" title="<?php esc_attr_e( 'Choose which types of users (e.g., customers, shop managers, or administrators) can view this tab.', 'customize-my-account-page-for-woocommerce' ); ?>">
						<?php Icon::get_svg_icon( 'tooltip', true ); ?>
					</span>
				</div>
				<div class="col-input">
					<select name="tgwc_endpoints[<?php echo esc_attr( $key ); ?>][user_role][]"
						data-selected="<?php echo esc_attr( wp_json_encode( $endpoint['user_role'] ) ); ?>">
					</select>
				</div>
			</div>
			<!-- ./ User Roles -->

			<!-- Custom content -->
			<div class="row">
				<div class="col-label">
					<p><?php esc_html_e( 'Custom Content', 'customize-my-account-page-for-woocommerce' ); ?></p>
					<span data-toggle="tgwc-tooltip" title="<?php esc_attr_e( 'Add content to show on this tab on My Account page. Supports text, shortcodes, media, and HTML.', 'customize-my-account-page-for-woocommerce' ); ?>">
						<?php Icon::get_svg_icon( 'tooltip', true ); ?>
					</span>
				</div>
				<div class="col-input">
				<?php
					$content = $endpoint['content'];

				if ( '' === trim( $content ) ) {
					$content = '<p></p>';
				}

					wp_editor(
						$content,
						"tgwc_endpoints_{$key}_content",
						array(
							'textarea_rows' => 5,
							'textarea_name' => "tgwc_endpoints[$key][content]",
							'wpautop'       => true,
						)
					);
					?>
				</div>
			</div>
			<!-- ./ Custom content -->

			<?php if ( tgwc_is_default_endpoint( $key ) ) : ?>
			<!-- Custom content position -->
			<div class="row">
				<div class="col-label">
					<p><?php esc_html_e( 'Custom Content Position', 'customize-my-account-page-for-woocommerce' ); ?></p>
					<span data-toggle="tgwc-tooltip" title="<?php esc_attr_e( 'Choose the location where custom content should appear.', 'customize-my-account-page-for-woocommerce' ); ?>">
						<?php Icon::get_svg_icon( 'tooltip', true ); ?>
					</span>
				</div>
				<div class="col-input">
					<select name="tgwc_endpoints[<?php echo esc_attr( $key ); ?>][content_position]" class="tgwc_custom_content_position">
						<option value="before" <?php selected( 'before', $endpoint['content_position'] ); ?>><?php esc_html_e( 'Before default content', 'customize-my-account-page-for-woocommerce' ); ?></option>
						<option value="main" <?php selected( 'main', $endpoint['content_position'] ); ?>><?php esc_html_e( 'Override default content', 'customize-my-account-page-for-woocommerce' ); ?></option>
						<option value="after" <?php selected( 'after', $endpoint['content_position'] ); ?>><?php esc_html_e( 'After default content', 'customize-my-account-page-for-woocommerce' ); ?></option>
					</select>
				</div>
			</div>
			<!-- ./ Custom content position -->
			<?php endif; ?>
		</div>
	</div>
</div>
<?php
