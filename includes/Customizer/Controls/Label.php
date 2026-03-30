<?php
/**
 * Customize API: Label control class
 *
 * @package ThemeGrill\WoocommerceCustomizer\Customizer\Controls
 * @since   0.1.0
 */

namespace ThemeGrill\WoocommerceCustomizer\Customizer\Controls;

defined( 'ABSPATH' ) || exit;

/**
 * Customize Label Control class.
 *
 * @see WP_Customize_Control
 */
class Label extends \WP_Customize_Control {

	/**
	 * Type.
	 *
	 * @var string
	 */
	public $type = 'tgwc-label';

	/**
	 * Display Label param.
	 *
	 * @var bool
	 */
	public $display_label = false;

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 * @uses WP_Customize_Control::to_json()
	 */
	public function to_json() {
		parent::to_json();
		$this->json['default']       = $this->setting->default;
		$this->json['id']            = $this->id;
		$this->json['value']         = $this->value();
		$this->json['link']          = $this->get_link();
		$this->json['choices']       = $this->choices;
		$this->json['display_label'] = $this->display_label;

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
	protected function content_template() {
		?>
		<label>
			<# if ( data.label ) { #>
				<span class="customize-control-title">{{{ data.label }}}</span>
			<# } #>
			<# if ( data.description ) { #>
<span class="description <?php echo tgwc_is_astra_active() ? 'tgwc-customize-control-description' : 'customize-control-description'; ?>">{{{ data.description }}}</span>			<# } #>
		</label>
		<?php
	}
}
