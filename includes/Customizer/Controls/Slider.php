<?php
/**
 * Customize API: Slider control class
 *
 * @package ThemeGrill\WoocommerceCustomizer\Customizer\Controls
 * @since   0.1.0
 */

namespace ThemeGrill\WoocommerceCustomizer\Customizer\Controls;

defined( 'ABSPATH' ) || exit;

/**
 * Customize Slider Control class.
 *
 * @see WP_Customize_Control
 */
class Slider extends \WP_Customize_Control {

	/**
	 * Type.
	 *
	 * @var string
	 */
	public $type = 'tgwc-slider';

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 * @uses WP_Customize_Control::to_json()
	 */
	public function to_json() {
		parent::to_json();
		$this->json['default'] = $this->setting->default;
		$this->json['id']      = $this->id;
		$this->json['value']   = $this->value();
		$this->json['link']    = $this->get_link();
		$this->json['choices'] = $this->choices;

		$this->json['inputAttrs'] = '';
		foreach ( $this->input_attrs as $attr => $value ) {
			$this->json['inputAttrs'] .= $attr . '="' . esc_attr( $value ) . '" ';
		}
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
		<# if ( data.label ) { #>
			<div class="customize-control-title-wrapper">
				<label class="customize-control-title">{{ data.label }}</label>
				<svg
					class="reset dashicons dashicons-image-rotate"
					xmlns="http://www.w3.org/2000/svg"
					viewBox="0 0 24 24"
				>
					<path d="M12 2A10 10 0 1 1 2 12a1 1 0 1 1 2 0 8 8 0 1 0 8.002-8 8.75 8.75 0 0 0-6.047 2.459L3.707 8.707a1 1 0 1 1-1.414-1.414l2.26-2.26.393-.363A10.75 10.75 0 0 1 11.996 2H12Z" />
					<path d="M2 3a1 1 0 0 1 2 0v4h4a1 1 0 0 1 0 2H3a1 1 0 0 1-1-1V3Z" />
				</svg>
			</div>
		<# } #>
		<div class="customize-control-content">
			<div class="tgwc-customizer-slider"></div>
			<div class="tgwc-customizer-slider-input">
				<input {{{ data.inputAttrs }}} type="number" class="slider-input" value="{{ data.value }}" {{{ data.link }}}/>
				<span class="tgwc-silder-input-text">
					<# if ( data.id === 'tgwc_customize[avatar][upload_size_limit]' ) { #>
						kb
					<# } else { #>
						px
					<# } #>
				</span>
			</div>
		</div>
		<# if ( data.description ) { #>
<span class="description <?php echo tgwc_is_astra_active() ? 'tgwc-customize-control-description' : 'customize-control-description'; ?>">{{{ data.description }}}</span>		<# } #>
		<?php
	}
}
