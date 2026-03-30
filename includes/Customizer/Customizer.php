<?php
/**
 * My Account Customizer.
 *
 * @package ThemeGrill\WoocommerceCustomizer\Customizer
 * @since 0.1.0
 */

namespace ThemeGrill\WoocommerceCustomizer\Customizer;

use Exception;
use ScssPhp\ScssPhp\Compiler;
use WP_Customize_Manager;
use WP_Customize_Panel;
use WP_Customize_Section;
use WP_Error;

defined( 'ABSPATH' ) || exit;

class Customizer {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialization.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	private function init() {
		$this->configs();

		$this->init_hooks();

		do_action( 'tgwc_customizer_unhooks', $this );
	}

	/**
	 * Initialize configs.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	private function configs() {
		Config\Navigation::init();
		Config\Avatar::init();
		Config\Layout::init();
		Config\Spacing::init();
		if ( false === get_option( 'tgwc_initial_started_version' ) ) {
			Config\Legacy::init();
		}
	}
	/**
	 * Initialize hooks.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	private function init_hooks() {
		$raw_referer = wp_parse_args( wp_parse_url( wp_get_raw_referer(), PHP_URL_QUERY ) );
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $raw_referer['tgwc-customizer'] ) || isset( $_GET['tgwc-customizer'] ) ) {
			// Register customize panel, sections and controls.
			add_action( 'customize_register', array( $this, 'register_controls' ), 11 );

			// Remove unrelated panel, sections, components, etc.
			add_filter( 'customize_section_active', array( $this, 'section_filter' ), 10, 2 );
			add_filter( 'customize_panel_active', array( $this, 'panel_filter' ), 10, 2 );
			add_filter( 'customize_loaded_components', array( $this, 'remove_core_components' ), 10 );

			// Enqueue customizer scripts.
			add_action( 'customize_preview_init', array( $this, 'enqueue_customizer_preview_scripts' ) );
			add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_customize_control_scripts' ) );

			// Change publish button text to save.
			add_filter( 'gettext', array( $this, 'change_publish_button' ), 10, 2 );

			// Compile SASS to load on frontend.
			add_action( 'customize_save_after', array( $this, 'save_after' ) );

		}

		// Add google font.
		add_action( 'wp_enqueue_scripts', array( $this, 'add_google_font' ) );
	}

	/**
	 * Enqueue customize control scripts.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function enqueue_customize_control_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Register control scripts.
		wp_register_style(
			'selectWoo',
			plugins_url( "assets/css/select2/select2{$suffix}.css", TGWC_PLUGIN_FILE ),
			array(),
			TGWC_VERSION
		);
		wp_register_script(
			'selectWoo',
			plugins_url( "assets/js/select2/selectWoo-full{$suffix}.js", TGWC_PLUGIN_FILE ),
			array(),
			TGWC_VERSION,
			true
		);

		// Enqueue control scripts.
		wp_enqueue_style(
			'tgwc-customize-controls',
			plugins_url( "/assets/css/customize-controls{$suffix}.css", TGWC_PLUGIN_FILE ),
			array(),
			TGWC_VERSION
		);

		wp_enqueue_script(
			'tgwc-customize-controls',
			plugins_url( "/assets/js/admin/customize-controls{$suffix}.js", TGWC_PLUGIN_FILE ),
			array( 'jquery' ),
			TGWC_VERSION,
			true
		);

		$asset = require TGWC_ABSPATH . '/dist/admin-customizer-color-palette.asset.php';
		wp_enqueue_script(
			'tgwc-customize-color-picker',
			plugins_url( 'dist/admin-customizer-color-palette.js', TGWC_PLUGIN_FILE ),
			$asset['dependencies'],
			$asset['version'],
			true
		);
		wp_enqueue_style(
			'tgwc-customize-controls-style',
			plugins_url( 'dist/style-admin-customizer-color-palette.css', TGWC_PLUGIN_FILE ),
			array(),
			TGWC_VERSION
		);

		$avatar = wp_parse_args(
			get_option( 'tgwc_settings' ),
			array(
				'custom_avatar' => true,
			)
		);

		wp_localize_script(
			'tgwc-customize-controls',
			'_tgwcControlsData',
			array(
				'panelTitle'                    => esc_html__( 'My Account Page', 'customize-my-account-page-for-woocommerce' ),
				'panelDescription'              => esc_html__( 'WooCommerce Customizer &ndash; Allows you to preview changes and customize MyAccount page.', 'customize-my-account-page-for-woocommerce' ),
				'whitelistPanels'               => apply_filters( 'tgwc_customizer_panels_whitelist', array() ),
				'whitelistSections'             => apply_filters( 'tgwc_customizer_sections_whitelist', array() ),
				'resetText'                     => esc_html__( 'Reset', 'customize-my-account-page-for-woocommerce' ),
				'nonce'                         => wp_create_nonce( '_tgwc_customizer_reset' ),
				'resetConfirm'                  => esc_html__( 'Warning! this will remove all my-account page customizations.', 'customize-my-account-page-for-woocommerce' ),
				'group_count'                   => count( tgwc_get_endpoints_by_type( 'group' ) ),
				'isUploadProfilePictureEnabled' => tgwc_string_to_bool( $avatar['custom_avatar'] ),
				'currentTheme'                  => array(
					'name'        => wp_get_theme()->get( 'Name' ),
					'version'     => wp_get_theme()->get( 'Version' ),
					'is_child'    => is_child_theme(),
					'parent_name' => is_child_theme() ? wp_get_theme()->parent()->get( 'Name' ) : '',
					'is_astra'    => ( 'Astra' === wp_get_theme()->get( 'Name' ) || ( is_child_theme() && 'Astra' === wp_get_theme()->parent()->get( 'Name' ) ) ),
				),
			)
		);
	}

	/**
	 * Remove core customizer components.
	 *
	 * @since 0.1.0
	 *
	 * @param string[] $components Core components list.
	 *
	 * @return string[] Core customizer components.
	 */
	public function remove_core_components( $components ) {
		return array_filter(
			$components,
			function ( $component ) {
				return ! in_array( $component, array( 'widgets', 'nav_menus' ), true );
			}
		);
	}

	/**
	 * Enqueue customizer preview js.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function enqueue_customizer_preview_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style(
			'tgwc-customize-preview',
			plugins_url( "/assets/css/customize-preview{$suffix}.css", TGWC_PLUGIN_FILE ),
			array(),
			TGWC_VERSION
		);
		wp_enqueue_script(
			'tgwc-customize-preview',
			plugins_url( "/assets/js/admin/customize-preview{$suffix}.js", TGWC_PLUGIN_FILE ),
			array( 'jquery', 'customize-preview' ),
			TGWC_VERSION,
			true
		);
		wp_localize_script(
			'tgwc-customize-preview',
			'_tgwcCustomizePreviewL10n',
			array(
				'form_id'   => 1,
				'templates' => array(
					'default'    => esc_html__( 'Default Template', 'customize-my-account-page-for-woocommerce' ),
					'layout-two' => esc_html__( 'Classic Layout', 'customize-my-account-page-for-woocommerce' ),
				),
			)
		);
	}

	/**
	 * Register customizer settings.
	 *
	 * @since 0.1.0
	 *
	 * @param WP_Customize_Manager $wp_customize WP_Customize_Manager instance.
	 *
	 * @return void
	 */
	public function register_controls( $wp_customize ) {
		// Remove core partials.
		$wp_customize->selective_refresh->remove_partial( 'blogname' );
		$wp_customize->selective_refresh->remove_partial( 'blogdescription' );
		$wp_customize->selective_refresh->remove_partial( 'custom_header' );

		$this->add_panels( $wp_customize );
		$this->add_sections( $wp_customize );
		$this->add_controls( $wp_customize );
	}

	/**
	 * Add panels.
	 *
	 * @since 0.1.0
	 *
	 * @param WP_Customize_Manager $wp_customize WordPress customize manager.
	 * @return void
	 */
	private function add_panels( $wp_customize ) {
		$panels = apply_filters( 'tgwc_customizer_panels', array() );

		foreach ( $panels as $panel ) {
			$panel = wp_parse_args( $panel, $this->default_panel_options() );

			$id = trim( $panel['id'] );
			if ( empty( $id ) ) {
				continue;
			}

			$wp_customize->add_panel( $id, $panel );
		}
	}

	/**
	 * Add sections.
	 *
	 * @since 0.1.0
	 *
	 * @param WP_Customize_Manager $wp_customize WordPress customize manager.
	 * @return void
	 */
	private function add_sections( $wp_customize ) {
		$sections = apply_filters( 'tgwc_customizer_sections', array(), $this );

		foreach ( $sections as $section ) {
			$section = wp_parse_args( $section, $this->default_section_options() );

			// Bail early if id is not set.
			$id = trim( $section['id'] );
			if ( empty( $id ) ) {
				continue;
			}

			$class = trim( $section['class'] );
			// Add a core or custom customize sections.
			if ( ! empty( $class ) && class_exists( $class ) ) {
				$wp_customize->register_section_type( $class );
				$wp_customize->add_section( new $class( $wp_customize, $id, $section ) );
			} else {
				$wp_customize->add_section( $id, $section );
			}
		}
	}

	/**
	 * Add controls.
	 *
	 * @since 0.1.0
	 *
	 * @param WP_Customize_Manager $wp_customize WordPress customize manager.
	 * @return void
	 */
	private function add_controls( $wp_customize ) {
		$controls = apply_filters( 'tgwc_customizer_controls', array(), $this );

		foreach ( $controls as $control ) {
			$control = tgwc_parse_args(
				$control,
				array(
					'id'      => '',
					'setting' => $this->default_setting_options(),
					'control' => $this->default_control_options(),
				)
			);

			// Bail early if section is not set.
			$section = trim( $control['control']['section'] );
			if ( empty( $section ) ) {
				continue;
			}

			// Bail early if the id is not set.
			$id = trim( $control['id'] );
			if ( empty( $id ) ) {
				continue;
			}

			// Extract the custom_args from inside the control array.
			if ( isset( $control['control']['custom_args'] ) ) {
				$custom_args = $control['control']['custom_args'];
				foreach ( $custom_args as $key => $arg ) {
					$control['control'][ $key ] = $arg;
				}
			}

			// Register and set the custom control if present.
			$this->add_setting( $wp_customize, $id, $control['setting'] );
			$class = trim( $control['control']['class'] );
			// Add a core or custom customize controls.
			if ( ! empty( $class ) && class_exists( $class ) ) {
				$wp_customize->register_control_type( $class );
				$wp_customize->add_control(
					new $class( $wp_customize, $id, $control['control'] )
				);
			} else {
				$wp_customize->add_control( $id, $control['control'] );
			}
		}
	}

	/**
	 * Add setting.
	 *
	 * @since 0.1.0
	 *
	 * @param WP_Customize_Manager        $wp_customize WordPress customize manager
	 * @param WP_Customize_Setting|string $setting_id   Customize Setting object, or ID.
	 * @param array                       $args         Array of properties for the new Setting object.
	 *
	 * @return WP_Customize_Setting                     The instance of the setting that was added.
	 */
	private function add_setting( $wp_customize, $setting_id, $args = array() ) {
		$args = wp_parse_args( $args, $this->default_setting_options() );

		$wp_customize->add_setting( $setting_id, $args );
	}

	/**
	 * Compile and save the style data.
	 *
	 * @since 0.1.0
	 *
	 * @param WP_Customize_Manager WP_Customize_Manager instance.
	 *
	 * @return void
	 */
	public function save_after( $manager ) {
		global $wp_filesystem;

		$action = 'save-customize_' . $manager->get_stylesheet();
		if ( ! check_ajax_referer( $action, 'nonce', false ) ) {
			wp_send_json_error( 'invalid_nonce' );
		}

		if ( ! isset( $_POST['customized'] ) ) {
			return;
		}

		$customized = json_decode( sanitize_textarea_field( wp_unslash( $_POST['customized'] ) ), true );
		if ( null === $customized ) {
			return;
		}

		if ( isset( $customized['tgwc_customize[navigation][show_logout_btn]'] ) && ! tgwc_is_old_user() ) {
			$show_logout = (bool) $customized['tgwc_customize[navigation][show_logout_btn]'];
			$endpoints   = tgwc_get_endpoints_flat();

			if ( isset( $endpoints['customer-logout'] ) ) {
				update_option( 'tgwc_override_endpoint_logout_button', $show_logout );
			}
		}

		$css             = $this->compile_scss();
		$directory       = tgwc_get_my_account_directory();
		$my_account_file = tgwc_get_my_account_file();

		$credentials = request_filesystem_credentials( '', 'direct' );

		if ( ! $credentials ) {
			throw new Exception( esc_html__( 'Invalid filesystem credentials', 'customize-my-account-page-for-woocommerce' ) );
		}

		WP_Filesystem( $credentials );

		// Save the compiled css.
		if ( ! is_wp_error( $css ) && wp_mkdir_p( $directory ) ) {
			// Remove my-account.css for backward compatibility.
			if ( $wp_filesystem->exists( $my_account_file ) ) {
				$wp_filesystem->delete( $my_account_file );
			}

			// Generate new my-account.css file with attached version to bust cache.
			$my_account_file = \tgwc_get_new_my_account_file();

			$wp_filesystem->put_contents( $my_account_file, $css, FS_CHMOD_FILE );
		}

		$font_directory = tgwc_get_font_directory();

		// Save fonts files.
		if ( isset( $customized['tgwc_customize[wrapper][font_family]'] ) ) {
			$this->local_google_fonts();
		}
	}

	/**
	 * Compile scss to css.
	 *
	 * @return string|WP_Error Compiled CSS.
	 * @since 0.1.0
	 */
	protected function compile_scss() {
		ob_start();
		require_once plugin_dir_path( TGWC_PLUGIN_FILE ) . 'includes/Customizer/Views/scss.php';
		$scss = ob_get_clean();

		try {
			$bourbon_path = plugin_dir_path( TGWC_PLUGIN_FILE ) . 'node_modules/bourbon/core/';
			$compiler     = new Compiler();
			$compiler->addImportPath( $bourbon_path );
			$compiler->setFormatter( 'ScssPhp\ScssPhp\Formatter\Compressed' );
			$css = $compiler->compile( trim( $scss ) );
			return $css;
		} catch ( Exception $e ) {
			// TODO: Add logger with opt in.
			echo esc_html( $e->getMessage() );
		}

		return new WP_Error(
			'could-not-compile-scss',
			esc_html__( 'ScssPhp: Unable to compile content', 'customize-my-account-page-for-woocommerce' )
		);
	}

	/**
	 * Change publish button text to save.
	 *
	 * @since 0.1.0
	 * @param string $translation  Translated text.
	 * @param string $text         Text to translate.
	 *
	 * @return string
	 */
	public function change_publish_button( $translation, $text ) {
		switch ( $text ) {
			case 'Publish':
				$translation = esc_html__( 'Save', 'customize-my-account-page-for-woocommerce' );
				break;
			case 'Published':
				$translation = esc_html__( 'Saved', 'customize-my-account-page-for-woocommerce' );
				break;
		}

		return $translation;
	}

	/**
	 * Default setting options.
	 *
	 * @since 0.1.0
	 *
	 * @return array
	 */
	private function default_setting_options() {
		return apply_filters(
			'tgwc_customizer_default_setting_options',
			array(
				'default'           => '',
				'transport'         => 'postMessage',
				'type'              => 'option',
				'sanitize_callback' => '',
			)
		);
	}

	/**
	 * Default control options.
	 *
	 * @since 0.1.0
	 *
	 * @return array
	 */
	private function default_control_options() {
		return apply_filters(
			'tgwc_customizer_default_control_options',
			array(
				'label'       => '',
				'description' => '',
				'section'     => '',
				'priority'    => 160,
				'type'        => 'text',
				'capability'  => 'edit_theme_options',
				'choices'     => array(),
				'input_attrs' => array(),
				'class'       => '',
			)
		);
	}

	/**
	 * Default section options.
	 *
	 * @since 0.1.0
	 *
	 * @return array
	 */
	private function default_section_options() {
		return apply_filters(
			'tgwc_customize_default_section_options',
			array(
				'id'                 => '',
				'title'              => '',
				'description'        => '',
				'panel'              => '',
				'priority'           => 160,
				'capability'         => 'edit_theme_options',
				'theme_supports'     => '',
				'active_callback'    => 'is_account_page',
				'description_hidden' => false,
				'class'              => '',
			)
		);
	}

	/**
	 * Default panel options.
	 *
	 * @since 0.1.0
	 *
	 * @return array
	 */
	private function default_panel_options() {
		return apply_filters(
			'tgwc_customize_default_panel_options',
			array(
				'id'              => '',
				'title'           => '',
				'description'     => '',
				'priority'        => 160,
				'capability'      => 'edit_theme_options',
				'theme_supports'  => '',
				'active_callback' => 'is_account_page',
			)
		);
	}

	/**
	 * Add google font.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function add_google_font() {
		$this->local_google_fonts();
		$font_css = tgwc_get_font_directory() . '/font.css';
		if ( file_exists( $font_css ) ) {
			wp_enqueue_style( 'tgwc-google-font', tgwc_get_font_directory_url() . '/font.css', array(), filemtime( $font_css ) );
		}
	}

		/**
		 * Show only our style settings in the preview.
		 *
		 * @param bool                 $active  Whether the Customizer section is active.
		 * @param WP_Customize_Section $section WP_Customize_Section instance.
		 */
	public function section_filter( $active, $section ) {
		$whitelist_sections = apply_filters(
			'tgwc_customizer_sections',
			array(
				array( 'id' => 'custom_css' ),
			)
		);

		$ids = wp_list_pluck( $whitelist_sections, 'id' );

		if ( in_array( $section->id, $ids, true ) ) {
			return $active;
		}

		return false;
	}

	/**
	 * Show only our style settings in the preview.
	 *
	 * @param bool               $active  Whether the Customizer panel is active.
	 * @param WP_Customize_Panel $panel WP_Customize_Section instance.
	 */
	public function panel_filter( $active, $panel ) {
		$whitelist_panels = apply_filters(
			'tgwc_customizer_panels',
			array(
				array( 'id' => 'custom_css' ),
			)
		);

		$ids = wp_list_pluck( $whitelist_panels, 'id' );

		if ( in_array( $panel->id, $ids, true ) ) {
			return $active;
		}

		return false;
	}

	/**
	 * Locally download google fonts.
	 *
	 * @since 0.4.2
	 * @return void
	 */
	private function local_google_fonts() {
		require_once ABSPATH . 'wp-admin/includes/file.php';

		global $wp_filesystem;
		$font_directory = tgwc_get_font_directory();
		$cached_font    = get_option( 'tgwc_local_google_font', '' );
		$values         = tgwc_get_customizer_values();
		$font_family    = ! empty( $values['wrapper']['font_family'] ) ? $values['wrapper']['font_family'] : '';

		if ( empty( $font_family ) || ( $cached_font === $font_family ) ) {
			return;
		}

		update_option( 'tgwc_local_google_font', $font_family );
		$credentials = request_filesystem_credentials( '', 'direct' );

		if ( ! $credentials ) {
			return;
		}

		WP_Filesystem( $credentials );

		if ( wp_mkdir_p( $font_directory ) ) {
			$font_files = list_files( $font_directory );

			if ( false !== $font_files ) {
				foreach ( $font_files as $font_file ) {
					$wp_filesystem->delete( $font_file ); // Delete old font files.
				}
			}

			$remote_args = array(
				'user-agent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:73.0) Gecko/20100101 Firefox/73.0',
			);
			$response    = wp_remote_get( "https://fonts.googleapis.com/css2?family=$font_family", $remote_args );

			if ( ! is_wp_error( $response ) ) {
				$font_css = wp_remote_retrieve_body( $response );

				if ( ! empty( $font_css ) ) {
					if ( preg_match_all( '/(?:https?|ftps?):\/\/[a-zA-Z\d\-.]+\.[a-zA-Z\d]{3,4}(\/\S[^)]*)?/', $font_css, $matches ) ) {
						list( $font_urls, $font_paths ) = $matches;
						$fonts                          = array_combine( array_values( $font_paths ), array_values( $font_urls ) );

						if ( false === $fonts ) {
							return;
						}

						foreach ( $fonts as $font_path => $font_url ) {
							$filename      = basename( $font_path );
							$font_response = wp_remote_get( $font_url, $remote_args );
							$font_css      = str_replace( $font_url, tgwc_get_font_directory_url() . "/$filename", $font_css );

							if ( ! is_wp_error( $font_response ) ) {
								$font_content = wp_remote_retrieve_body( $font_response );
								if ( ! empty( $font_content ) ) {
									$wp_filesystem->put_contents( $font_directory . "/$filename", $font_content, FS_CHMOD_FILE );
								}
							}
						}

						$wp_filesystem->put_contents( $font_directory . '/font.css', $font_css, FS_CHMOD_FILE );
					}
				}
			}
		}
	}
}
