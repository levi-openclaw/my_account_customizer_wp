<?php
/**
 * Customize API: Toggle control class
 *
 * @package ThemeGrill\WoocommerceCustomizer\Customizer\Controls
 * @since   0.1.0
 */

namespace ThemeGrill\WoocommerceCustomizer\Customizer\Controls;

defined( 'ABSPATH' ) || exit;

/**
 * Customize Toggle Control class.
 *
 * @see WP_Customize_Control
 */
class Toggle extends \WP_Customize_Control {

	/**
	 * Type.
	 *
	 * @var string
	 */
	public $type = 'tgwc-toggle';

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 * @uses WP_Customize_Control::to_json()
	 */
	public function to_json() {
		parent::to_json();
		$this->json['id']           = $this->id;
		$this->json['value']        = $this->value();
		$this->json['link']         = $this->get_link();
		$this->json['defaultValue'] = $this->setting->default;
	}

	/**
	 * Don't render the control content from PHP, as it's rendered via JS on load.
	 */
	public function render_content() {}

	/**
	 * Render a JS template for control display.
	 *
	 * @see WP_Customize_Control::print_template()
	 */
	public function content_template() {
		?>
		<div>
			<label class="customize-control-title" for="toggle_{{ data.id }}">{{{ data.label }}}</label>
			<label class="switch-wrapper" for="toggle_{{ data.id }}">
				<input id="toggle_{{ data.id }}" type="checkbox" class="screen-reader-text" value="{{ data.value }}" {{{ data.link }}} <# if ( true === data.value ) { #> checked="checked"<# } #> />
				<button type="button" class="switch">
					<span class="switch-thumb"></span>
				</button>
			</div>
		</div>
		<# if ( data.description ) { #>
<span class="description <?php echo tgwc_is_astra_active() ? 'tgwc-customize-control-description' : 'customize-control-description'; ?>">{{{ data.description }}}</span>		<# } #>
		<?php
	}
}
