<?php
/**
 * Customize API: Color Palette control class
 *
 * @package ThemeGrill\WoocommerceCustomizer\Customizer\Controls
 * @since   0.1.0
 */

namespace ThemeGrill\WoocommerceCustomizer\Customizer\Controls;

defined( 'ABSPATH' ) || exit;

/**
 * Customize Color Palette Control class.
 */
class ColorPalette extends \WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'tgwc-color_palette';

	/**
	 * Refresh the parameters passed to the JavaScript via JSON.
	 */
	public function to_json() {
		parent::to_json();
		$this->json['defaultValues'] = $this->setting->default;
		$this->json['id']            = $this->id;
		$this->json['inputAttrs']    = $this->input_attrs;

		$value               = $this->value();
		$this->json['value'] = array();
		if ( is_array( $value ) ) {
			foreach ( $this->value() as $key => $value ) {
				if ( is_numeric( $key ) ) {
					$this->json['value'][ $value ] = true;
				} else {
					$this->json['value'][ $key ] = $value;
				}
			}
		} elseif ( ! empty( $value ) ) {
			$this->json['value'] = array( $value => true );
		}

		if ( isset( $this->settings['default'] ) ) {
			$this->json['link'] = array(
				'data-customize-setting-link' => $this->settings['default']->id,
			);
		}
	}

	/**
	 * Don't render the control content from PHP, as it's rendered via JS on load.
	 */
	public function render_content() {}

	/**
	 * Render a JS template for control display.
	 */
	protected function content_template() {
		?>
		<div {{{data.inputAttrs}}}>
			<div class="color-palette-header-interface">
				<# if ( data.label ) { #><h6 class="customize-control-title">{{{ data.label }}} </h6>
					<div class="color-palette-list-wrap">
						<div class="color-palette-list">
							<# Object.keys( data.choices ).forEach( function( key ) {
								var innerColors = data.choices[key].color;
								var labelName = data.choices[key].name.split('_')
									.map(word => word.charAt(0).toUpperCase() + word.slice(1))
									.join(' '); #>
								<# Object.keys( innerColors ).forEach( function( innerKey ) { #>
									<div class="color-palette-list-item">
										<div class="color-palette-label" title="{{{labelName}}}" for="color-palette-{{{data.id}}}-{{{key}}}">
											<input id="color-palette-{{{data.id}}}-{{{key}}}" type="checkbox" name="color-palette-{{{data.id}}}" value="{{{innerColors[innerKey]}}}" data-key="{{{key}}}" data-title="{{{labelName}}}" class="color-group-{{{key.charAt(0)}}}" {{{data.inputAttrs}}}/>
											<span class="color-palette-color" style="background-color:{{{innerColors[innerKey]}}};"></span>
										</div>
									</div>
								<# } ); #>
							<# } ); #>
						</div>
						<span class="color-palette-edit-icon" style="cursor:pointer;">&#9998;</span>
					</div>
				<# } #>
				<# if ( data.description ) { #><span class="description customize-control-description">{{{ data.description }}}</span><# } #>
				<input class="color-palette-hidden-value" type="hidden" {{{ data.link }}} />
			</div>
			<div class="color-palette-edit-interface">
				<div class="color-palette-edit-items">
					<# Object.keys( data.choices ).forEach( function( key ) {
						var choice = data.choices[key];
						if (!choice || !choice.color) return;

						var labelName = choice.name.split('_')
							.map(word => word.charAt(0).toUpperCase() + word.slice(1))
							.join(' '); #>
						<div class="color-palette-edit-item">
							<span class="color-palette-item-title" for="color-edit-{{{data.id}}}-{{{key}}}" data-key="{{{choice.name}}}">
								{{{labelName}}}
							</span>
							<div class="color-palette-list color-list">
								<# Object.keys( choice.color ).forEach( function( colorKey ) { #>
									<div class="color-palette-item">
										<input id="color-edit-{{{data.id}}}-{{{colorKey}}}"
											type="text"
											value="{{{choice.color[colorKey]}}}"
											data-color-key="{{{colorKey}}}"
											class="color-picker"
										/>
										<span class="color-palette-color" style="background-color:{{{choice.color[colorKey]}}};"></span>
									</div>
								<# } ); #>
							</div>
						</div>
						<# } ); #>
					</div>
				<button type="button" class="button button-primary color-palette-reset-button">Reset all to default</button>
			</div>
			<div class="tgwc-color-picker-popup"></div>
		</div>

		<?php
	}
}
