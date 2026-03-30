<?php
/**
 * Customize API: Image Radio control class
 *
 * @package ThemeGrill\WoocommerceCustomizer\Customizer\Controls
 * @since   0.1.0
 */

namespace ThemeGrill\WoocommerceCustomizer\Customizer\Controls;

defined( 'ABSPATH' ) || exit;

/**
 * Customize Image Radio Control class.
 *
 * @see WP_Customize_Control
 */
class ImageRadio extends \WP_Customize_Control {

	/**
	 * Type.
	 *
	 * @var string
	 */
	public $type = 'tgwc-image_radio';

	/**
	 * Display Label param.
	 *
	 * @var bool
	 */
	public $display_label = false;

	/**
	 * Columns.
	 *
	 * @var integer
	 */
	public $columns = 3;

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
		$this->json['columns']       = $this->columns;

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
			<# if ( data.label ) { #><span class="customize-control-title">{{{ data.label }}}</span><# } #>
		</label>
		<ul class="image-radio-wrapper" data-columns="{{{data.columns}}}">
			<# Object.keys( data.choices ).forEach( function( key ) { #>
			<li>
				<input id="image-radio-{{{data.id}}}-{{{key}}}" type="radio" name="image-radio-{{{data.id}}}" value = "{{{key}}}" {{{data.link}}} {{{ (data.value === key)? 'checked="checked"' : '' }}} />
				<label class="image-radio-item" title="{{{data.choices[key].name}}}" for="image-radio-{{{data.id}}}-{{{key}}}">
				<# if ( data.choices[key].image ) { #>
					<img src="{{{data.choices[key].image}}}" alt="{{{data.choices[key].name}}}" />
				<# } else { #>
					<span>{{ data.choices[key].name }}</span>
				<# } #>
				<# if ( data.display_label == true ) { #>
					<span class="image-radio-label">{{{data.choices[key].name}}}</span>
				<# } #>
				</label>
			</li>
			<# } ); #>
		</ul>
		<# if ( data.description ) { #>
			<span class="description <?php echo tgwc_is_astra_active() ? 'tgwc-customize-control-description' : 'customize-control-description'; ?>">{{{ data.description }}}</span>
			<# } #>
		<?php
	}
}
