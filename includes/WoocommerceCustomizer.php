<?php
/**
 * WooCommerce Customizer setup
 *
 * @package ThemeGrill\WoocommerceCustomizer
 * @since 0.1.0
 */

namespace ThemeGrill\WoocommerceCustomizer;

use ThemeGrill\WoocommerceCustomizer\Compatibility\FlatsomeCompatibility;
use ThemeGrill\WoocommerceCustomizer\Compatibility\JetpackCRMCompatibility;
use ThemeGrill\WoocommerceCustomizer\Customizer\Customizer;
use ThemeGrill\WoocommerceCustomizer\Compatibility\WCMembershipCompatibility;
use ThemeGrill\WoocommerceCustomizer\Compatibility\WCMembershipForTeamsCompatibility;

defined( 'ABSPATH' ) || exit;

/**
 * Main WooCommerce Customizer Class
 *
 * @class WoocommerceCustomizer
 */
final class WoocommerceCustomizer {
	/**
	 * Scripts and Styles suffix.
	 *
	 * @since 0.1.0
	 *
	 * @var String
	 */
	private $suffix = '';


	/**
	 * Notice.
	 *
	 * @since 0.1.0
	 *
	 * @var Notice
	 */
	public $notice = null;

	/**
	 * Ajax.
	 *
	 * @since 0.1.0
	 *
	 * @var Customizer
	 */
	public $customizer = null;

	/**
	 * Ajax.
	 *
	 * @since 0.1.0
	 *
	 * @var Ajax
	 */
	public $ajax = null;

	/**
	 * Settings.
	 *
	 * @since 0.1.0
	 *
	 * @var Settings
	 */
	public $settings = null;

	/**
	 * Account menu.
	 *
	 * @since 0.1.0
	 *
	 * @var AccountMenu
	 */
	public $account_menu = null;

	/**
	 * The single instance of the class.
	 *
	 * @since 0.1.0
	 *
	 * @var WoocommerceCustomizer
	 */
	protected static $instance = null;

	/**
	 * Get WooCommerce Customizer instance.
	 *
	 * Ensures only instance of ThemeGrill Woocommerce Customizer is loaded or
	 * can be loaded.
	 *
	 * @since 0.1.0
	 * @static
	 *
	 * @return WoocommerceCustomizer - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 */
	private function __construct() {
		$this->init_hooks();

		// @see https://developer.wordpress.org/reference/hooks/customize_loaded_components/
		$this->customizer = new Customizer();
	}

	/**
	 * Initialize hooks.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	private function init_hooks() {
		add_action( 'init', array( $this, 'init' ), 0 );
		add_action( 'init', array( $this, 'init_compatibilities' ) );
		add_action( 'admin_init', array( $this, 'deactivate_plugin' ) );

		add_filter( 'plugin_action_links_' . plugin_basename( TGWC_PLUGIN_FILE ), array( $this, 'add_actions_links' ), 10, 4 );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_styles' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_common_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_common_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'remove_styles_scripts' ), 11 );

		add_filter( 'body_class', array( $this, 'add_body_class' ), 11 );
		add_filter( 'do_shortcode_tag', array( $this, 'add_attributes' ), 10, 2 );

		add_action( 'admin_footer', array( $this, 'add_templates' ) );

		// WC HPOS compatibility.
		add_action(
			'before_woocommerce_init',
			function () {
				if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
					\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', TGWC_PLUGIN_FILE, true );
				}
			}
		);
	}

	/**
	 * Initialize WooCommerce Customizer when WordPress initializes.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function init() {
		$this->suffix = ( ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) || tgwc_is_debug_enabled() ) ? '' : '.min';

		// Before init action.
		do_action( 'before_tgwc_init' );

		// Set up localization.
		$this->load_plugin_textdomain();

		// Update the plugin version.
		$this->update_plugin_version();

		// Load class instances.
		$this->notice       = new Notice();
		$this->settings     = new Settings();
		$this->ajax         = new Ajax();
		$this->account_menu = new AccountMenu();
		new SmartTags();

		// After init action.
		do_action( 'tgwc_init' );
	}

	/**
	 * Initialize compatibilities classes.
	 *
	 * @since 0.4.2
	 * @return void
	 */
	public function init_compatibilities() {
		WCMembershipCompatibility::instance();
		WCMembershipForTeamsCompatibility::instance();
		FlatsomeCompatibility::instance();
		JetpackCRMCompatibility::instance();
	}

	/**
	 * Load localization files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
	 *
	 * Locales found in:
	 *      - WP_LANG_DIR/customize-my-account-page-for-woocommerce/customize-my-account-page-for-woocommerce-LOCALE.mo
	 *      - WP_LANG_DIR/plugins/customize-my-account-page-for-woocommerce-LOCALE.mo
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function load_plugin_textdomain() {
		if ( function_exists( 'determine_locale' ) ) {
			$locale = determine_locale();
		} else {
			// TODO Remove when start supporting WP 5.0 or later.
			$locale = is_admin() ? get_user_locale() : get_locale();
		}

		$locale = apply_filters(
			'plugin_locale',
			$locale,
			'customize-my-account-page-for-woocommerce'
		);

		unload_textdomain( 'customize-my-account-page-for-woocommerce' );
		load_textdomain(
			'customize-my-account-page-for-woocommerce',
			WP_LANG_DIR . '/customize-my-account-page-for-woocommerce/customize-my-account-page-for-woocommerce-' . $locale . '.mo'
		);
		load_plugin_textdomain(
			'customize-my-account-page-for-woocommerce',
			false,
			plugin_basename( dirname( TGWC_PLUGIN_FILE ) ) . '/languages'
		);
	}

	/**
	 * Add settings action link in the plugins list table.
	 *
	 * @param string[] $actions     An array of plugin action links. By default
	 *                              this can include 'activate', 'deactivate', and 'delete'.
	 *                              With Multisite active this can also include
	 *                              'network_active' and 'network_only' items.
	 * @param string   $plugin_file   Path to the plugin file relative to the plugins directory.
	 * @param array    $plugin_data    An array of plugin data. See get_plugin_data().
	 * @param string   $context       The plugin context. By default this can include
	 *                                'all', 'active', 'inactive', 'recently_activated',
	 *                                'upgrade', 'mustuse', 'dropins', and 'search'.
	 * @return string[] Modified actions links
	 */
	public function add_actions_links( $actions, $plugin_file, $plugin_data, $context ) {
		if ( ! is_plugin_active( plugin_basename( TGWC_PLUGIN_FILE ) ) ) {
			return $actions;
		}

		$settings_url = add_query_arg(
			array(
				'page' => 'tgwc-customize-my-account-page',
			),
			admin_url() . 'admin.php'
		);

		$custom_actions['settings'] = sprintf(
			'<a href="%s">%s</a>',
			esc_url( $settings_url ),
			esc_html__( 'Settings', 'customize-my-account-page-for-woocommerce' )
		);

		return $custom_actions + $actions;
	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function enqueue_admin_scripts() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! ( isset( $_GET['page'] ) && 'tgwc-customize-my-account-page' === $_GET['page'] ) ) {
			return;
		}

		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_script( 'jquery-ui-tabs' );
		wp_enqueue_script( 'jquery-effects-fade' );
		wp_enqueue_script( 'jquery-ui-tooltip' );

		wp_enqueue_media();
		wp_enqueue_editor();

		wp_enqueue_script(
			'select2',
			plugin_dir_url( TGWC_PLUGIN_FILE ) . "assets/js/select2/select2{$this->suffix}.js",
			array( 'jquery' ),
			TGWC_VERSION,
			true
		);

		wp_enqueue_script(
			'jquery-nestable',
			plugin_dir_url( TGWC_PLUGIN_FILE ) . "assets/js/jquery-nestable/jquery-nestable{$this->suffix}.js",
			array( 'jquery', 'jquery-ui-core' ),
			TGWC_VERSION,
			true
		);
		wp_enqueue_script(
			'jquery-sortablejs',
			plugin_dir_url( TGWC_PLUGIN_FILE ) . 'assets/js/jquery-sortablejs/jquery-sortablejs.js',
			array( 'jquery', 'jquery-ui-core' ),
			TGWC_VERSION,
			true
		);

		wp_enqueue_script(
			'fontawesome',
			plugin_dir_url( TGWC_PLUGIN_FILE ) . "assets/js/fontawesome/all{$this->suffix}.js",
			array( 'jquery' ),
			TGWC_VERSION,
			true
		);

		wp_enqueue_script(
			'tgwc-admin',
			plugin_dir_url( TGWC_PLUGIN_FILE ) . "assets/js/admin/admin{$this->suffix}.js",
			array( 'jquery-ui-core', 'jquery-ui-dialog', 'jquery-ui-tabs', 'jquery-nestable', 'select2', 'fontawesome', 'tgwc-util', 'jquery-sortablejs' ),
			TGWC_VERSION,
			true
		);

		// Getting active endpoint if not set then get it from the default active endpoint.
		$active_endpoint = get_transient( 'tgwc_active_endpoint' ) ? get_transient( 'tgwc_active_endpoint' ) : ( isset( TGWC()->get_settings()->get_settings()['default_endpoint'] ) ? TGWC()->get_settings()->get_settings()['default_endpoint'] : 'dashboard' );

		wp_localize_script(
			'tgwc-admin',
			'tgwc_admin',
			array(
				'active_endpoint'                        => $active_endpoint,
				'tgwc_link_url_err'                      => __( 'Invalid URL. Must be a full URL (https://...) or a hash link (#section).', 'customize-my-account-page-for-woocommerce' ),
				'smart_tags_dropdown_title'              => esc_html__( 'Smart Tags', 'customize-my-account-page-for-woocommerce' ),
				'smart_tags_dropdown_search_placeholder' => esc_html__( 'Search Tags...', 'customize-my-account-page-for-woocommerce' ),
				'tgwc_upload_nonce'                      => wp_create_nonce( 'tgwc_upload_nonce' ),
				'ajaxurl'                                => admin_url( 'admin-ajax.php' ),
				'customizer_logout_btn_enabled'          => get_option( 'tgwc_override_endpoint_logout_button', '' ),
			)
		);
	}

	/**
	 * Enqueue admin styles.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function enqueue_admin_styles() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! ( isset( $_GET['page'] ) && 'tgwc-customize-my-account-page' === $_GET['page'] ) ) {
			return;
		}

		wp_enqueue_style(
			'jquery-ui',
			plugin_dir_url( TGWC_PLUGIN_FILE ) . "assets/css/jquery-ui/jquery-ui{$this->suffix}.css",
			null,
			TGWC_VERSION
		);

		wp_enqueue_style(
			'select2',
			plugin_dir_url( TGWC_PLUGIN_FILE ) . "assets/css/select2/select2{$this->suffix}.css",
			null,
			TGWC_VERSION
		);

		wp_enqueue_style(
			'jquery-nestable',
			plugin_dir_url( TGWC_PLUGIN_FILE ) . "assets/css/jquery-nestable/jquery-nestable{$this->suffix}.css",
			null,
			TGWC_VERSION
		);

		wp_enqueue_style(
			'fontawesome',
			plugin_dir_url( TGWC_PLUGIN_FILE ) . "assets/js/fontawesome/all{$this->suffix}.js",
			null,
			TGWC_VERSION,
			true
		);

		wp_enqueue_style(
			'tgwc-admin',
			plugin_dir_url( TGWC_PLUGIN_FILE ) . "assets/css/admin{$this->suffix}.css",
			array( 'select2', 'jquery-nestable', 'fontawesome', 'jquery-ui' ),
			TGWC_VERSION
		);
	}

	/**
	 * Enqueue public styles.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function enqueue_public_styles() {
		if ( ! ( is_account_page() && is_user_logged_in() ) ) {
			return;
		}

		if ( tgwc_is_frontend_library_enabled( 'dropzone', 'css' ) ) {
			wp_enqueue_style(
				'dropzone',
				plugin_dir_url( TGWC_PLUGIN_FILE ) . "assets/css/dropzone/dropzone{$this->suffix}.css",
				null,
				TGWC_VERSION
			);
		}

		if ( tgwc_is_frontend_library_enabled( 'jqueryscrolltabs', 'css' ) && ( 'tab' === tgwc_get_menu_style() || is_customize_preview() ) ) {
			wp_enqueue_style(
				'jquery-scrolltabs',
				plugin_dir_url( TGWC_PLUGIN_FILE ) . "assets/css/jquery-scrolltabs/scrolltabs{$this->suffix}.css",
				null,
				TGWC_VERSION
			);
		}
		wp_enqueue_style(
			'tgwc-public',
			plugin_dir_url( TGWC_PLUGIN_FILE ) . "assets/css/public{$this->suffix}.css",
			array(),
			TGWC_VERSION
		);
		wp_enqueue_style(
			'tgwc-new-public',
			plugin_dir_url( TGWC_PLUGIN_FILE ) . "assets/css/public_new{$this->suffix}.css",
			array(),
			TGWC_VERSION
		);

		$my_account_file = tgwc_get_my_account_file();
		$my_account_url  = tgwc_get_my_account_file_url();

		if ( \file_exists( $my_account_file ) ) {
			wp_enqueue_style(
				'tgwc-myaccount',
				$my_account_url,
				array( 'tgwc-public', 'tgwc-new-public' ),
				TGWC_VERSION
			);
		}
	}

	/**
	 * Enqueue public scripts.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function enqueue_public_scripts() {
		if ( ! ( is_account_page() && is_user_logged_in() ) ) {
			return;
		}

		if ( tgwc_is_frontend_library_enabled( 'dropzone', 'js' ) ) {
			wp_enqueue_script(
				'dropzone',
				plugin_dir_url( TGWC_PLUGIN_FILE ) . "assets/js/dropzone/dropzone{$this->suffix}.js",
				null,
				TGWC_VERSION,
				true
			);
		}

		if ( tgwc_is_frontend_library_enabled( 'jqueryscrolltabs', 'js' ) && ( 'tab' === tgwc_get_menu_style() || is_customize_preview() ) ) {
			wp_enqueue_script(
				'jquery-scrolltabs',
				plugin_dir_url( TGWC_PLUGIN_FILE ) . "assets/js/jquery-scrolltabs/jquery-scrolltabs{$this->suffix}.js",
				array( 'jquery' ),
				TGWC_VERSION,
				true
			);
		}

		wp_enqueue_script(
			'tgwc-public',
			plugin_dir_url( TGWC_PLUGIN_FILE ) . "assets/js/public/public{$this->suffix}.js",
			array( 'tgwc-util', 'dropzone' ),
			TGWC_VERSION,
			true
		);

		$settings = get_option( 'tgwc_settings' );

		if ( isset( $settings['enable_ajax_navigation'] ) && $settings['enable_ajax_navigation'] ) {

			wp_enqueue_script(
				'tgwc-myaccount-menu-nav',
				plugin_dir_url( TGWC_PLUGIN_FILE ) . "assets/js/public/myaccount-menu-nav{$this->suffix}.js",
				array( 'tgwc-util', 'dropzone' ),
				TGWC_VERSION,
				true
			);

			wp_localize_script(
				'tgwc-myaccount-menu-nav',
				'myaccountMenuNav',
				array(
					'ajax_url'    => admin_url( 'admin-ajax.php' ),
					'nonce'       => wp_create_nonce( 'myaccount-menu-nav-nonce' ),
					'account_url' => wc_get_page_permalink( 'myaccount' ),
				)
			);
		}
		$customize    = get_option( 'tgwc_customize' );
		$menu_postion = isset( $customize['layout']['menu_postion'] ) ? $customize['layout']['menu_postion'] : 'vertical-left';
		$menu_style   = isset( $customize['layout']['menu_style'] ) ? $customize['layout']['menu_style'] : 'minimal';

		wp_localize_script(
			'tgwc-public',
			'tgwc_public',
			array(
				'acceptFilesForProfile' => apply_filters(
					'tgwc_image_upload_mime_types',
					array(
						'jpg|jpeg|jpe' => 'image/jpeg',
						'png'          => 'image/png',
					),
				),
				'chevronRightIcon'      => plugins_url( '/assets/images/chevron-right.svg', TGWC_PLUGIN_FILE ),
				'chevronLeftIcon'       => plugins_url( '/assets/images/chevron-down.svg', TGWC_PLUGIN_FILE ),
				'menuStyle'             => $menu_postion,
				'menuPosition'          => $menu_style,
				'isCustomizer'          => is_customize_preview(),
			)
		);
	}

	/**
	 * Enqueue common scripts both to public and admin.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function enqueue_common_scripts() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ( isset( $_GET['page'] ) && 'tgwc-customize-my-account-page' === $_GET['page'] ) || ( function_exists( 'is_account_page' ) && is_account_page() ) ) {
			$icons = tgwc_get_icon_list();
			$icons = array_map(
				function ( $icon ) {
						$text = str_replace( 'fas fa-', '', $icon );
						$text = str_replace( '-', ' ', $text );
						$text = ucfirst( $text );

						return array(
							'id'   => $icon,
							'text' => "<i class='{$icon}'></i> {$text}",
						);
				},
				$icons
			);

			$roles = array();
			if ( is_admin() ) {
				$roles = \get_editable_roles();
				$roles = array_reduce(
					array_keys( $roles ),
					function ( $result, $key ) use ( $roles ) {
						$result[] = array(
							'id'   => $key,
							'text' => $roles[ $key ]['name'],
						);

						return $result;
					},
					array()
				);
			}

			wp_register_script(
				'tgwc-util',
				plugin_dir_url( TGWC_PLUGIN_FILE ) . "assets/js/common/util{$this->suffix}.js",
				array( 'jquery' ),
				TGWC_VERSION,
				true
			);

			wp_localize_script(
				'tgwc-util',
				'tgwc',
				array(
					'ajaxURL'           => admin_url( 'admin-ajax.php' ),
					'i18n'              => array(
						'enable'                   => esc_html__( 'Enable', 'customize-my-account-page-for-woocommerce' ),
						'remove'                   => esc_html__( 'Remove', 'customize-my-account-page-for-woocommerce' ),
						'selectAnIcon'             => esc_html__( 'Select an icon', 'customize-my-account-page-for-woocommerce' ),
						'selectUserRoles'          => esc_html__( 'Select user roles', 'customize-my-account-page-for-woocommerce' ),
						'cancel'                   => esc_html__( 'Cancel', 'customize-my-account-page-for-woocommerce' ),
						'delete'                   => esc_html__( 'Delete', 'customize-my-account-page-for-woocommerce' ),
						'reset'                    => esc_html__( 'Reset', 'customize-my-account-page-for-woocommerce' ),
						'add'                      => esc_html__( 'Add', 'customize-my-account-page-for-woocommerce' ),
						'notAvailable'             => esc_html__( 'Not available ', 'customize-my-account-page-for-woocommerce' ),
						'available'                => esc_html__( 'Available ', 'customize-my-account-page-for-woocommerce' ),
						'slugCannotBeEmpty'        => esc_html__( 'Slug cannot be empty ', 'customize-my-account-page-for-woocommerce' ),
						'settings'                 => esc_html__( 'Settings ', 'customize-my-account-page-for-woocommerce' ),
						'designCustomization'      => esc_html__( 'Design Customization ', 'customize-my-account-page-for-woocommerce' ),
						'restoreSettingsInfo'      => esc_html__( 'Restore the general settings and remove the added endpoints, group and links.', 'customize-my-account-page-for-woocommerce' ),
						'restoreCustomizationInfo' => esc_html__( 'Remove all the design customization.', 'customize-my-account-page-for-woocommerce' ),
						'invalidSlug'              => esc_html__( 'Invalid slug. ', 'customize-my-account-page-for-woocommerce' ),
						'labelCannotBeEmpty'       => esc_html__( 'Label cannot be empty.', 'customize-my-account-page-for-woocommerce' ),
						'couldNotSaveChanges'      => esc_html__( 'Could Not Save Changes.', 'customize-my-account-page-for-woocommerce' ),
						'resolveFormErrors'        => esc_html__( 'Please resolve the following errors and try again.', 'customize-my-account-page-for-woocommerce' ),
						'ok'                       => esc_html__( 'ok', 'customize-my-account-page-for-woocommerce' ),
						'slugCanOnlyContains'      => esc_html__( 'Slug can only contains alphabets,numbers, underscore(_) and dash(-) characters.', 'customize-my-account-page-for-woocommerce' ),
						'slugMustBeginWith'        => esc_html__( 'Slug must begin and end with alphabets and numbers.', 'customize-my-account-page-for-woocommerce' ),
						'slugMustBeOfLength'       => esc_html__( 'Slug must be 3 characters in length.', 'customize-my-account-page-for-woocommerce' ),
						'save'                     => esc_html__( 'Save Changes', 'customize-my-account-page-for-woocommerce' ),
						'discard'                  => esc_html__( 'Discard', 'customize-my-account-page-for-woocommerce' ),

					),
					'previousAttachId'  => strval( get_user_meta( get_current_user_id(), 'tgwc_avatar_image', true ) ),
					'gravatarImage'     => tgwc_get_default_avatar( $type = 'src' ),
					'avatarImageSize'   => tgwc_get_avatar_image_size(),
					'menuStyle'         => \tgwc_get_menu_style(),
					'icons'             => $icons,
					'roles'             => $roles,
					'avatarUploadSize'  => tgwc_get_avatar_upload_size() / ( 1024 * 1024 ),
					'endpointIcon'      => Icon::get_svg_icon( 'tgwc-endpoint' ),
					'groupIcon'         => Icon::get_svg_icon( 'tgwc-group' ),
					'linkIcon'          => Icon::get_svg_icon( 'tgwc-link' ),
					'deleteIcon'        => Icon::get_svg_icon( 'tgwc-delete' ),
					'deletePopupIcon'   => Icon::get_svg_icon( 'tgwc-delete-popup' ),
					'alertTriangleIcon' => Icon::get_svg_icon( 'tgwc-alert-triangle' ),

				)
			);

			wp_enqueue_script( 'tgwc-util' );
		}
	}

	/**
	 * Remove the scripts and styles.
	 *
	 * @since 0.2.0
	 */
	public function remove_styles_scripts() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! ( isset( $_GET['page'] ) && 'tgwc-customize-my-account-page' === $_GET['page'] ) ) {
			return;
		}

		if ( class_exists( 'WC_Bookings_Init' ) ) {
			wp_dequeue_style( 'jquery-ui-style' );
		}
	}

	/**
	 * Get the plugin url.
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', TGWC_PLUGIN_FILE ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( TGWC_PLUGIN_FILE ) );
	}

	/**
	 * Get settings.
	 *
	 * @since 0.1.0
	 *
	 * @return Settings Settings instance.
	 */
	public function get_settings() {
		return $this->settings;
	}

	/**
	 * Add custom classes to body tag.
	 *
	 * @param array $classes List of classes in body tag.
	 * @return array
	 */
	public function add_body_class( $classes ) {
		$classes[] = 'tgwc-woocommerce-customize-my-account';
		return $classes;
	}

	/**
	 * Update the plugin version.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	private function update_plugin_version() {
		$version = get_option( 'tgwc_version' );

		if ( false !== get_option( 'tgwc_version' ) ) {
			$this->run_migration();
		} else {
			// Update migration flags.
			update_option( 'tgwc_color_palete_migration', true );
			update_option( 'tgwc_initial_started_version', TGWC_VERSION );
		}

		if ( false === $version || version_compare( $version, TGWC_VERSION, '<' ) ) {
			update_option( 'tgwc_version', TGWC_VERSION );
		}
	}
	/**
	 * Run migration.
	 *
	 * @param mixed $version Stored version.
	 * @return void
	 */
	private function run_migration() {

		$color_palete_migration = get_option( 'tgwc_color_palete_migration' );

		if ( ! $color_palete_migration ) {
			tgwc_color_palete_migration();
		}
	}

	/**
	 * Deactivate the plugin if the WooCommerce is not active.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function deactivate_plugin() {
		// Enable plugin only when WooCommerce is activated.
		if ( ! tgwc_is_woocommerce_activated() ) {
			$plugin_data = get_plugin_data( TGWC_PLUGIN_FILE, false );
			$this->notice->add_error_notice(
				'tgwc_woocommerce_deactivate',
				$plugin_data['Name'] . ': ',
				esc_html__( 'WooCommerce is required for this plugin to work. Please, activate WooCommerce first.', 'customize-my-account-page-for-woocommerce' )
			);
			deactivate_plugins( plugin_basename( TGWC_PLUGIN_FILE ) );

			if ( isset( $_GET['activate'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				unset( $_GET['activate'] );
			}
		}
	}

	/**
	 * Add templates.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function add_templates() {
		if ( isset( $_GET['page'] ) && 'tgwc-customize-my-account-page' === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			require_once plugin_dir_path( TGWC_PLUGIN_FILE ) . 'templates/admin/dialogs/restore-defaults.php';
		}
	}

	/**
	 * Add attributes to the wrapper of my account shortcode.
	 *
	 * @param string $output Shortcode output.
	 * @param string $tag Shortcode tag.
	 * @return array|string|string[]|null Output.
	 */
	public function add_attributes( $output, $tag ) {
		if ( ! in_array( $tag, array( 'woocommerce_my_account', 'thrive_account_template' ), true ) ) {
			return $output;
		}

		$customize     = tgwc_get_customizer_values();
		$menu_position = isset( $customize['layout']['menu_position'] ) ? $customize['layout']['menu_position'] : 'vertical-left';
		$menu_layout   = isset( $customize['layout']['menu_style'] ) ? $customize['layout']['menu_style'] : 'minimal';
		$menu_style    = isset( $customize['wrapper']['menu_style'] ) ? $customize['wrapper']['menu_style'] : '';

		return preg_replace(
			'/class="woocommerce/',
			"id='tgwc-woocommerce' data-menu-position='$menu_position' data-menu-layout='$menu_layout' data-menu-style='$menu_style' $0",
			$output,
			1
		);
	}
}
