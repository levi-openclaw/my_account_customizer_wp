<?php
/**
 * Button configs.
 *
 * @package ThemeGrill\WoocommerceCustomizer\Customizer\Config
 * @since   0.1.0
 */

namespace ThemeGrill\WoocommerceCustomizer\Customizer\Config;

defined( 'ABSPATH' ) || exit;

class Template {
	/**
	 * Instance variable.
	 *
	 * @since 0.1.0
	 * @static
	 *
	 * @var ThemeGrill\WoocommerceCustomizer\Customizer\Config\Template
	 */
	private static $instance = null;

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 */
	private function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialize and get instance of the class.
	 *
	 * @since 0.1.0
	 * @static
	 *
	 * @return ThemeGrill\WoocommerceCustomizer\Customizer\Config\Button
	 */
	public static function init() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize hooks.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	private function init_hooks() {
		add_filter( 'tgwc_customizer_sections', array( $this, 'add_sections' ) );
	}

	/**
	 * Add sections.
	 *
	 * @since 0.1.0
	 *
	 * @param array $sections List of sections.
	 * @return array Modified list of sections.
	 */
	public function add_sections( $sections ) {
		$sections[] = array(
			'id'          => 'tgwc_customize[template]',
			'title'       => esc_html__( 'Default Template', 'customize-my-account-page-for-woocommerce' ),
			'description' => '<p>' . esc_html__( 'Looking for a template? You can browse our templates, import and preview templates, then activate them right here.', 'customize-my-account-page-for-woocommerce' ) . '</p>' .
						'<p>' . esc_html__( 'While previewing a new template, you can continue to tailor things like form styles and custom css, and explore template-specific options.', 'customize-my-account-page-for-woocommerce' ) . '</p>',
			'class'       => 'ThemeGrill\WoocommerceCustomizer\Customizer\Sections\Template',
			'priority'    => 0,
		);

		return $sections;
	}
}
