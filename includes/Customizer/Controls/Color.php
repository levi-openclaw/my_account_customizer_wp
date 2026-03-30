<?php
/**
 * Customize API: Color control class
 *
 * @package ThemeGrill\WoocommerceCustomizer\Customizer\Controls
 * @since   0.1.0
 */

namespace ThemeGrill\WoocommerceCustomizer\Customizer\Controls;

defined( 'ABSPATH' ) || exit;

/**
 * Customize Color Control class.
 *
 * @see WP_Customize_Color_Control
 */
class Color extends \WP_Customize_Color_Control {

	/**
	 * Type.
	 *
	 * @var string
	 */
	public $type = 'tgwc-color';

	/**
	 * Alpha.
	 *
	 * @var string
	 */
	public $alpha = false;
	/**
	 * Enqueue scripts/styles for the color picker.
	 */
	public function enqueue() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Enqueue control scripts.
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script(
			'wp-color-picker-alpha',
			plugins_url(
				"/assets/js/wp-color-picker/wp-color-picker-alpha{$suffix}.js",
				TGWC_PLUGIN_FILE
			),
			array( 'wp-color-picker' ),
			'2.1.4',
			true
		);
	}

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 * @uses WP_Customize_Control::to_json()
	 */
	public function to_json() {
		parent::to_json();
		$this->json['alpha'] = $this->alpha;
	}

	/**
	 * Render a JS template for control display.
	 *
	 * @see WP_Customize_Control::print_template()
	 */
	public function content_template() {
		?>
		<# var defaultValue = '#RRGGBB', defaultValueAttr = '',
			isHueSlider = data.mode === 'hue';
		if ( data.defaultValue && _.isString( data.defaultValue ) && ! isHueSlider ) {
			if ( '#' !== data.defaultValue.substring( 0, 1 ) && ! data.alpha ) {
				defaultValue = '#' + data.defaultValue;
			} else {
				defaultValue = data.defaultValue;
			}
			defaultValueAttr = ' data-default-color=' + defaultValue; // Quotes added automatically.
		} #>
		<# if ( data.label ) { #>
			<span class="customize-control-title">{{{ data.label }}}</span>
		<# } #>
		<div class="customize-control-content">
			<label><span class="screen-reader-text">{{{ data.label }}}</span>
			<# if ( isHueSlider ) { #>
				<input class="color-picker-hue" type="text" data-type="hue" />
			<# } else { #>
				<input class="color-picker-hex" type="text" data-alpha={{ data.alpha }} <# if ( ! data.alpha ) { #> maxlength="7"<# } #> placeholder="{{ defaultValue }}" {{ defaultValueAttr }} />
			<# } #>
			</label>
		</div>
		<# if ( data.description ) { #>
<span class="description <?php echo tgwc_is_astra_active() ? 'tgwc-customize-control-description' : 'customize-control-description'; ?>">{{{ data.description }}}</span>		<# } #>
		<?php
	}
}
