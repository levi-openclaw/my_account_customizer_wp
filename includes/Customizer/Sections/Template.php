<?php
/**
 * Customize API: Template class
 *
 * @package ThemeGrill\WoocommerceCustomizer\Customizer\Sections
 * @since   0.1.0
 */

namespace ThemeGrill\WoocommerceCustomizer\Customizer\Sections;

defined( 'ABSPATH' ) || exit;

/**
 * Customize Templates Section class.
 *
 * A UI container for form templates controls, which are displayed within sections.
 *
 * @see WP_Customize_Section
 */
class Template extends \WP_Customize_Section {

	/**
	 * Section Type.
	 *
	 * @var string
	 */
	public $type = 'tgwc-templates';

	/**
	 * An Underscore (JS) template for rendering this panel's container.
	 *
	 * The templates panel renders a custom section heading with the current template and a switch template button.
	 *
	 * @see WP_Customize_Panel::print_template()
	 *
	 * @since 4.9.0
	 */
	protected function render_template() {
		?>
		<li id="accordion-section-{{ data.id }}" class="accordion-section control-section-tgwc-templates">
			<h3 class="accordion-section-title">
				<span class="customize-action"><?php esc_html_e( 'Active template', 'customize-my-account-page-for-woocommerce' ); ?></span> <span class="customize-template-name">{{ data.title }}</span>

				<?php if ( current_user_can( 'manage_options' ) ) : ?>
					<button type="button" class="button change-template" aria-label="<?php esc_attr_e( 'Change template', 'customize-my-account-page-for-woocommerce' ); ?>"><?php echo esc_html_x( 'Change', 'template', 'customize-my-account-page-for-woocommerce' ); ?></button>
				<?php endif; ?>
			</h3>
			<ul class="accordion-section-content">
				<li class="customize-section-description-container section-meta <# if ( data.description ) { #>customize-info<# } #>">
					<div class="customize-section-title">
						<button class="customize-section-back" tabindex="-1">
							<span class="screen-reader-text"><?php esc_html_e( 'Back', 'customize-my-account-page-for-woocommerce' ); ?></span>
						</button>
						<h3>
							<span class="customize-action">
								<?php esc_html_e( 'You are browsing', 'customize-my-account-page-for-woocommerce' ); ?>
							</span>
							<?php esc_html_e( 'Templates', 'customize-my-account-page-for-woocommerce' ); ?>
						</h3>
						<# if ( data.description ) { #>
							<button type="button" class="customize-help-toggle dashicons dashicons-editor-help" aria-expanded="false"><span class="screen-reader-text"><?php esc_html_e( 'Help', 'customize-my-account-page-for-woocommerce' ); ?></span></button>
							<div class="description customize-section-description">
								{{{ data.description }}}
							</div>
						<# } #>

						<div class="customize-control-notifications-container"></div>
					</div>
				</li>
			</ul>
		</li>
		<?php
	}
}
