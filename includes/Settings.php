<?php
/**
 * Settings page.
 *
 * @package ThemeGrill\WoocommerceCustomizer
 * @since 0.1.0
 */

namespace ThemeGrill\WoocommerceCustomizer;

defined( 'ABSPATH' ) || exit;

class Settings {

	/**
	 * List of tabs.
	 *
	 * @since 0.1.0
	 *
	 * @var array
	 */
	private $tabs = array();

	/**
	 * Customize URL.
	 *
	 * @var string
	 */
	private $customize_url = '';

	/**
	 * Current tab.
	 *
	 * @var string
	 */
	private $tab = '';

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->init();
		add_action( 'admin_init', array( $this, 'run_migrate_debug_to_setting' ) );
	}

	/**
	 * Initialization.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	private function init() {
		$this->tabs = apply_filters(
			'tgwc_settings_tabs',
			array(
				'endpoints'  => esc_html__( 'Endpoints', 'customize-my-account-page-for-woocommerce' ),
				'customizer' => esc_html__( 'Customizer', 'customize-my-account-page-for-woocommerce' ),
				'settings'   => esc_html__( 'Settings', 'customize-my-account-page-for-woocommerce' ),
			)
		);

		if ( function_exists( 'wc_get_page_permalink' ) ) {
			$settings_url        = add_query_arg(
				array(
					'page' => 'tgwc-customize-my-account-page',
				),
				admin_url() . 'admin.php'
			);
			$this->customize_url = add_query_arg(
				array(
					'tgwc-customizer' => true,
					'return'          => rawurlencode( $settings_url ),
					'url'             => rawurlencode( wc_get_page_permalink( 'myaccount' ) ),
				),
				admin_url() . 'customize.php'
			);
		}

		$this->tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$this->init_hooks();

		do_action( 'tgwc_settings_unhook', $this );
	}

	/**
	 * Initialization hooks.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	private function init_hooks() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_init', array( $this, 'register_setting' ) );
		add_action( 'admin_init', array( $this, 'register_translation_strings' ) );
		add_action( 'tgwc_customization_panel_tabs', array( $this, 'display_tabs' ) );
		add_action( 'tgwc_before_customization_panel_form', array( $this, 'display_messages' ) );
		add_action( 'tgwc_customization_panel_tab_content', array( $this, 'display_tab_content' ) );
		add_action( 'tgwc_after_customization_panel_form', array( $this, 'display_endpoint_dialog_form' ) );
		add_action( 'admin_footer', array( $this, 'add_js_templates' ) );
		add_action( 'tgwc_endpoints_content', array( $this, 'display_endpoints_content' ), 20, 3 );
		add_filter( 'tiny_mce_before_init', array( $this, 'tinymce_settings' ), 10, 2 );
	}

	/**
	 * TinyMCE settings.
	 *
	 * @param array  $settings TinyMCE settings.
	 * @param string $editor_id Editor id.
	 *
	 * @return array
	 */
	public function tinymce_settings( $settings, $editor_id ) {
		if ( false === strpos( $editor_id, 'tgwc_endpoints' ) ) {
			return $settings;
		}
		$settings['remove_linebreaks']       = false;
		$settings['convert_newlines_to_brs'] = true;
		$settings['remove_redundant_brs']    = false;
		$settings['forced_root_block']       = '';
		return $settings;
	}

	/**
	 * Register settings.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register_setting() {
		// Remove the customization as well.
		if ( isset( $_POST['_wpnonce'] ) ) {
			$nonce = sanitize_text_field( $_POST['_wpnonce'] );
			$valid = wp_verify_nonce( $nonce, 'tgwc-options' );

			if ( isset( $_POST['tgwc_reset_options']['customization'] ) && false !== $valid ) {
				$my_account_file = \tgwc_get_my_account_file();
				$font_dir        = tgwc_get_font_directory();
				$font_files      = list_files( $font_dir );

				( file_exists( $font_dir ) && false !== $font_files ) && array_map( 'unlink', $font_files );
				file_exists( $my_account_file ) && unlink( $my_account_file );

				// Remove the customization option as well.
				delete_option( 'tgwc_customize' );

				add_settings_error(
					'tgwc',
					'tgwc-reset-customization',
					esc_html__( 'Customization reset successfully.', 'customize-my-account-page-for-woocommerce' ),
					'success'
				);
			}
		}

		register_setting(
			'tgwc',
			'tgwc_settings',
			array(
				'sanitize_callback' => array( $this, 'process_settings' ),
			)
		);

		register_setting(
			'tgwc',
			'tgwc_endpoints',
			array(
				'sanitize_callback' => array( $this, 'process_endpoints' ),
			)
		);

		register_setting(
			'tgwc',
			'tgwc_debug_settings',
			array(
				'sanitize_callback' => array( $this, 'process_debug_settings' ),
			)
		);
	}

	/**
	 * Register strings for translation.
	 *
	 * @version 0.3.0
	 * @param array $settings Endpoint settings.
	 * @return void
	 */
	public function register_translation_strings( $settings = array() ) {
		if ( empty( $settings ) ) {
			$settings = $this->get_endpoints();
		}
		foreach ( $settings as $key => $setting ) {
			! empty( $setting['label'] ) && tgwc_register_single_string( "{$key}_label", $setting['label'] );
			! empty( $setting['content'] ) && tgwc_register_single_string( "{$key}_content", $setting['content'] );
			! empty( $setting['children'] ) && $this->register_translation_strings( $setting['children'] );
		}
	}

	/**
	 * Process the settings POST data.
	 *
	 * @since 0.1.0
	 *
	 * @param array $settings Settings.
	 *
	 * @return array|bool|void Processed settings.
	 */
	public function process_settings( $settings ) {
		// Bail early with settings if the page is not settings.
		if ( ! isset( $_POST['tgwc_page'] ) || 'settings' !== $_POST['tgwc_page'] ) {
			return TGWC()->get_settings()->get_settings();
		}

		// Bail early if the nonce verification fails.
		if ( isset( $_POST['_wpnonce'] ) ) {
			$nonce = sanitize_text_field( $_POST['_wpnonce'] );
			$valid = wp_verify_nonce( $nonce, 'tgwc-options' );
			if ( false === $valid ) {
				return;
			}
		}

		// Remove the customization as well.
		if ( isset( $_POST['tgwc_reset_options']['setting'] ) ) {
			add_settings_error(
				'tgwc',
				'tgwc-reset-settings',
				esc_html__( 'Settings reset successfully.', 'customize-my-account-page-for-woocommerce' ),
				'success'
			);
			return false;
		}

		$settings = apply_filters( 'tgwc_before_process_settings', $settings );

		if ( ! \is_array( $settings ) || empty( $settings ) ) {
			return TGWC()->get_settings()->get_settings();
		}

		$settings['custom_avatar']          = isset( $settings['custom_avatar'] ) && $settings['custom_avatar'];
		$settings['icon']                   = isset( $settings['icon'] ) && $settings['icon'];
		$settings['group_accordion_icon']   = isset( $settings['group_accordion_icon'] ) && $settings['group_accordion_icon'];
		$settings['enable_ajax_navigation'] = isset( $settings['enable_ajax_navigation'] ) && $settings['enable_ajax_navigation'];

		$settings['enable_debug'] = isset( $settings['enable_debug'] ) && tgwc_string_to_bool( $settings['enable_debug'] );

		$settings = tgwc_parse_args(
			$settings,
			array(
				'frontend' => array(
					'dropzone'         => array(),
					'jqueryscrolltabs' => array(),
				),
			)
		);

		if ( isset( $settings['frontend'] ) ) {
			foreach ( $settings['frontend'] as $library => $setting ) {
				$settings['frontend'][ $library ]['css'] = isset( $settings['frontend'][ $library ]['css'] ) && $settings['frontend'][ $library ]['css'];
				$settings['frontend'][ $library ]['js']  = isset( $settings['frontend'][ $library ]['js'] ) && $settings['frontend'][ $library ]['js'];
			}
		}

		$settings = apply_filters( 'tgwc_after_process_settings', $settings );

		add_settings_error(
			'tgwc',
			'tgwc_endpoints_saved',
			esc_html__( 'Settings saved successfully', 'customize-my-account-page-for-woocommerce' ),
			'success'
		);

		return $settings;
	}

	/**
	 * Process the endpoints POST data.
	 *
	 * @since 0.1.0
	 *
	 * @param array $endpoints Endpoints.
	 *
	 * @return array|bool|void Processed endpoints.
	 */
	public function process_endpoints( $endpoints ) {
		// Bail early with settings if the page is not settings.
		if ( ! isset( $_POST['tgwc_page'] ) || 'settings' !== $_POST['tgwc_page'] ) {
			return TGWC()->get_settings()->get_endpoints();
		}

		// Bail early if the nonce is not set.
		if ( ! isset( $_POST['_wpnonce'] ) ) {
			add_settings_error(
				'tgwc',
				'tgwc_nonce_required',
				esc_html__( 'Nonce is required.', 'customize-my-account-page-for-woocommerce' )
			);
			return false;
		}

		// Bail early if the nonce verification fails.
		$nonce = sanitize_text_field( $_POST['_wpnonce'] );
		if ( false === wp_verify_nonce( $nonce, 'tgwc-options' ) ) {
			add_settings_error(
				'tgwc',
				'tgwc_invalid-nonce',
				esc_html__( 'Invalid nonce.', 'customize-my-account-page-for-woocommerce' )
			);
			return false;
		}

		/**
		 * If the free version endpoint state is set, it means the free endpoint is deleted.
		 * It helps in backward compatibility to add the default custom endpoint when free version of plugin is installed.
		 *
		 * @since 2.0.0
		 */
		if ( isset( $_POST['tgwc_free_endpoint_state'] ) && ! empty( $_POST['tgwc_free_endpoint_state'] ) && 'deleted' === $_POST['tgwc_free_endpoint_state'] ) {
			update_option( 'tgwc_added_default_custom_endpoint', false );
		}

		// Reset settings if the options is so.
		if ( isset( $_POST['tgwc_reset_options']['setting'] ) ) {
			return false;
		}
		// Reset endpoints if the options is so.
		if ( isset( $_POST['tgwc_reset_options']['endpoints'] ) ) {
			delete_option( 'tgwc_endpoints' );
			add_settings_error(
				'tgwc',
				'tgwc-reset-endpoints',
				esc_html__( 'Endpoints reset successfully.', 'customize-my-account-page-for-woocommerce' ),
				'success'
			);
			return false;
		}

		if ( ! \is_array( $endpoints ) || empty( $endpoints ) ) {
			return TGWC()->get_settings()->get_endpoints();
		}

		if ( isset( $endpoints['endpoints_order'] ) ) {
			$endpoints_order = json_decode( $endpoints['endpoints_order'], true );
			unset( $endpoints['endpoints_order'] );
		} else {
			return $endpoints;
		}
		$error             = 0;
		$endpoints         = apply_filters( 'tgwc_before_process_endpoints', $endpoints, $endpoints_order );
		$default_endpoints = TGWC()->account_menu->get_default_endpoints();

		// Update the endpoints order id to the slugs.
		foreach ( $endpoints as $key => $endpoint ) {
			// Validation.
			if ( 'link' === $endpoint['type'] ) {
				$url = isset( $endpoint['url'] ) ? $endpoint['url'] : '';

				// Check if it's a valid URL OR starts with a hash (#)
				if ( ! empty( $url ) &&
				! filter_var( $url, FILTER_VALIDATE_URL ) &&
				'#' !== $url &&
				! preg_match( '/^#[\w-]*$/', $url ) ) {
					add_settings_error(
						'tgwc',
						'tgwc_endpoints_saved',
						esc_html__( 'Invalid URL. Must be a full URL (https://...) or a hash link (#section).', 'customize-my-account-page-for-woocommerce' ),
						'error'
					);

					$endpoints[ $key ]['url'] = '#';
					++$error;
				}
			}

			if ( isset( $default_endpoints[ $key ] ) ) {
				continue;
			}

			if ( 'endpoint' !== $endpoint['type'] ) {
				continue;
			}

			// Get slug.
			$slug = isset( $endpoint['slug'] ) ? $endpoint['slug'] : '';
			$slug = empty( $slug ) ? $key : $slug;
			$slug = sanitize_title( $slug );

			// No need to update the id if the slug and key of the endpoint is same.
			if ( $key === $slug ) {
				continue;
			}

			$endpoints[ $slug ] = $endpoint;

			// Update the endpoints order id to the slug.
			$endpoints_order = array_map(
				function ( $order ) use ( $key, $slug ) {
						// Update to id to the slug if it matches.
					if ( $order['id'] === $key ) {
						$order['id'] = $slug;
					}

						// Loop through children in case of group.
					if ( isset( $order['children'] ) ) {
						foreach ( $order['children'] as $index => $child ) {
							if ( ! isset( $order['id'] ) ) {
								continue;
							}

							if ( $child['id'] === $key ) {
								$order['children'][ $index ]['id'] = $slug;
							}
						}
					}

						return $order;
				},
				$endpoints_order
			);

			unset( $endpoints[ $key ] );
		}

		// Convert input checkbox values to boolean.
		$endpoints = array_map(
			function ( $endpoint ) {
				if ( 'link' === $endpoint['type'] ) {
					$endpoint['new_tab'] = isset( $endpoint['new_tab'] );
				} elseif ( 'group' === $endpoint['type'] ) {
					$endpoint['show_open'] = isset( $endpoint['show_open'] );
				}

					$endpoint['enable'] = isset( $endpoint['enable'] );

				if ( isset( $endpoint['class'] ) ) {
					$endpoint['class'] = explode( ' ', $endpoint['class'] );
					$endpoint['class'] = array_filter(
						$endpoint['class'],
						function ( $class ) {
							$class = trim( $class );
							return ! empty( $class );
						}
					);
				} else {
					$endpoint['class'] = array();
				}
					return $endpoint;
			},
			$endpoints
		);

		// Create the endpoints according to the order.
		$endpoints = array_reduce(
			$endpoints_order,
			function ( $result, $endpoint_order ) use ( $endpoints ) {
				$endpoint = $endpoint_order['id'];
				$type     = $endpoint_order['type'];

				if ( ! isset( $endpoints[ $endpoint ] ) ) {
					return;
				}

				unset( $endpoint_order['uiTabsAriaControls'] );

				if ( isset( $endpoint_order['children'] ) ) {
					$children = $endpoint_order['children'];

					$children = array_reduce(
						$children,
						function ( $result, $child ) use ( $endpoints ) {
							$slug = $child['id'];
							if ( isset( $endpoints[ $slug ] ) ) {
								$result[ $slug ] = $endpoints[ $slug ];
								return $result;
							}
						},
						array()
					);

					$endpoint_order['children'] = $children;
				}

				unset( $endpoint_order['id'] );
				$result[ $endpoint ] = array_merge( $endpoint_order, $endpoints[ $endpoint ] );
				return $result;
			},
			array()
		);

		// Set flag to flush the rewrite rules on next page load.
		update_option( 'tgwc_flush_rewrite', true );
		if ( isset( $_POST['tgwc_active_endpoint'] ) && ! empty( $_POST['tgwc_active_endpoint'] ) ) {
			delete_transient( 'tgwc_active_endpoint' );
			$sanitized_endpoint = sanitize_text_field( wp_unslash( $_POST['tgwc_active_endpoint'] ) );
			set_transient( 'tgwc_active_endpoint', $sanitized_endpoint, 30 );
		}

		/**
		 * If the free version endpoint state is set, it means the free endpoint is deleted.
		 * It helps in backward compatibility to add the default custom endpoint when free version of plugin is installed.
		 *
		 * @since 2.0.0
		 */
		if ( isset( $_POST['tgwc_free_endpoint_state'] ) && ! empty( $_POST['tgwc_free_endpoint_state'] ) && 'deleted' === $_POST['tgwc_free_endpoint_state'] ) {
			update_option( 'tgwc_added_default_custom_endpoint', false );
		}

		// Don't add endpoints saved message when the design customization reset is being performed.
		if ( ! isset( $_POST['tgwc_reset_options']['customization'] ) && 0 === $error ) {
			add_settings_error(
				'tgwc',
				'tgwc_endpoints_saved',
				esc_html__( 'Endpoints saved successfully', 'customize-my-account-page-for-woocommerce' ),
				'success'
			);
		}

		return apply_filters( 'tgwc_after_process_endpoints', $endpoints );
	}

	/**
	 * Register menus.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register_menu() {
		if ( ! function_exists( 'wc_get_page_permalink' ) ) {
			return;
		}

		add_menu_page(
			esc_html__( 'Customize My Account Page For WooCommerce', 'customize-my-account-page-for-woocommerce' ),
			esc_html__( 'Customize My Account', 'customize-my-account-page-for-woocommerce' ),
			'manage_options',
			'tgwc-customize-my-account-page',
			array( $this, 'display_settings_page' ),
			'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA0OCA0OCI+CiAgPGNpcmNsZSBmaWxsPSIjZmZmZmZmIiBjeD0iMjQiIGN5PSIxNSIgcj0iNyIvPgogIDxwYXRoIGZpbGw9IiNmZmZmZmYiIGQ9Ik0zMiA0MGExNCAxNCAwIDAwMTQtMTRIMThhMTQgMTQgMCAwMDE0IDE0eiIvPgogIDxwYXRoIGZpbGw9IiNmZmZmZmYiIGQ9Ik0xNiA0MGExNCAxNCAwIDAwMTQtMTRIMmExNCAxNCAwIDAwMTQgMTR6IiAvPgogIDxwYXRoIGZpbGw9IiNmZmZmZmYiIGQ9Ik0yNCAzNy40OEExNCAxNCAwIDAwMzAgMjZIMThhMTQgMTQgMCAwMDYgMTEuNDh6IiAvPgo8L3N2Zz4=',
			58
		);

		add_submenu_page(
			'tgwc-customize-my-account-page',
			esc_html__( 'Customize My Account Page For WooCommerce', 'customize-my-account-page-for-woocommerce' ),
			esc_html__( 'Endpoints', 'customize-my-account-page-for-woocommerce' ),
			'manage_options',
			'tgwc-customize-my-account-page&tab=endpoints',
			array( $this, 'display_settings_page' )
		);

		add_submenu_page(
			'tgwc-customize-my-account-page',
			esc_html__( 'Customize My Account Page For WooCommerce', 'customize-my-account-page-for-woocommerce' ),
			esc_html__( 'Customizer', 'customize-my-account-page-for-woocommerce' ),
			'manage_options',
			$this->customize_url
		);

		add_submenu_page(
			'tgwc-customize-my-account-page',
			esc_html__( 'Customize My Account Page For WooCommerce', 'customize-my-account-page-for-woocommerce' ),
			esc_html__( 'Settings', 'customize-my-account-page-for-woocommerce' ),
			'manage_options',
			'tgwc-customize-my-account-page&tab=settings',
			array( $this, 'display_settings_page' )
		);

		remove_submenu_page( 'tgwc-customize-my-account-page', 'tgwc-customize-my-account-page' );
	}

	/**
	 * Display settings page.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function display_settings_page() {
		global $pagenow, $current_screen;

		$current      = $current_screen->id;
		$selected_tab = ! empty( $this->tab ) ? $this->tab : 'endpoints';

		if ( 'admin.php' !== $pagenow || 'toplevel_page_tgwc-customize-my-account-page' !== $current ) {
			return;
		}

		require_once plugin_dir_path( TGWC_PLUGIN_FILE ) . 'templates/admin/customization.php';
	}
	/**
	 * Display settings messages
	 *
	 * @since 2.0.0
	 */
	public function display_messages() {
		?>
			<div class="wrap"><?php settings_errors( 'tgwc' ); ?></div>
	<!-- Add empty h2 tag to display fix the move of the notices. -->
	<h2></h2>
		<?php
	}
	/**
	 * Display customization page tabs list.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function display_tabs() {
		$tab_selected  = ! empty( $this->tab ) ? $this->tab : 'endpoints';
		$customize_url = $this->customize_url;
		$tabs          = $this->tabs;
		$tab_selected  = apply_filters( 'tgwc_settings_tab_selected', $tab_selected );

		require_once plugin_dir_path( TGWC_PLUGIN_FILE ) . 'templates/admin/tabs.php';
	}

	/**
	 * Display customization page tab content.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function display_tab_content() {
		$tab_selected = ! empty( $this->tab ) ? $this->tab : 'endpoints';
		$settings     = $this->get_settings();
		$endpoints    = $this->get_endpoints();
		$debug        = tgwc_get_debug_settings();

		switch ( true ) {
			case 'endpoints' === $tab_selected:
				require_once plugin_dir_path( __DIR__ ) . 'templates/admin/tab-content/endpoints.php';
				break;
			case 'settings' === $tab_selected:
				require_once plugin_dir_path( __DIR__ ) . 'templates/admin/tab-content/settings.php';
				break;
			default:
				require_once plugin_dir_path( __DIR__ ) . 'templates/admin/tab-content/endpoints.php';
		}
	}

	/**
	 * Display endpoint dialog form.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function display_endpoint_dialog_form() {
		require_once plugin_dir_path( TGWC_PLUGIN_FILE ) . 'templates/admin/endpoint-dialog.php';
	}

	/**
	 * Display endpoints header.
	 *
	 * @since 0.1.0
	 *
	 * @param string $key Endpoint key.
	 * @param array  $endpoint Endpoint.
	 * @return void
	 */
	public function display_endpoints_header( $key, $endpoint ) {
		require plugin_dir_path( TGWC_PLUGIN_FILE ) . 'templates/admin/endpoints/header.php';
	}

	/**
	 * Display endpoints content.
	 *
	 * @since 0.1.0
	 *
	 * @param string $key Endpoint key.
	 * @param array  $endpoint Endpoint.
	 * @param string $initial Initial endpoint slug.
	 * @return void
	 */
	public function display_endpoints_content( $key, $endpoint, $initial ) {
		$endpoint['class'] = ! empty( $endpoint['class'] ) ? implode( ' ', $endpoint['class'] ) : '';

		// Set key as slug for if it is not set.
		$default_endpoints = TGWC()->account_menu->get_default_endpoints();

		if ( ! isset( $default_endpoints[ $key ] ) ) {
			$endpoint['slug'] = empty( $endpoint['slug'] ) ? $key : $endpoint['slug'];
		}

		if ( 'endpoint' === $endpoint['type'] ) {
			require plugin_dir_path( TGWC_PLUGIN_FILE ) . 'templates/admin/endpoints/endpoint.php';
		} elseif ( 'link' === $endpoint['type'] ) {
			require plugin_dir_path( TGWC_PLUGIN_FILE ) . 'templates/admin/endpoints/link.php';
		} elseif ( 'group' === $endpoint['type'] ) {
			require plugin_dir_path( TGWC_PLUGIN_FILE ) . 'templates/admin/endpoints/group.php';
		}
	}

	/**
	 * Add js templates.
	 */
	public function add_js_templates() {
		global $pagenow, $current_screen;

		$current = $current_screen->id;

		if ( 'admin.php' !== $pagenow || 'toplevel_page_tgwc-customize-my-account-page' !== $current ) {
			return;
		}

		if ( 'endpoints' === $this->tab || '' === $this->tab ) {
			require_once plugin_dir_path( TGWC_PLUGIN_FILE ) . 'templates/admin/js/endpoint.php';
			require_once plugin_dir_path( TGWC_PLUGIN_FILE ) . 'templates/admin/js/link.php';
			require_once plugin_dir_path( TGWC_PLUGIN_FILE ) . 'templates/admin/js/group.php';
		}
		?>
		<script>
			( function( $, tab ) {
				var menu    = $( '#toplevel_page_tgwc-customize-my-account-page' );
				var submenu = menu.find( 'li:not(.wp-submenu-head)' );
				menu.find( 'a' ).eq( 3 ).attr( 'target', '' );
				switch ( true ) {
					case 'endpoints' === tab:
						$( submenu[0] ).addClass( 'current' );
						break;
					case 'settings' === tab:
						$( submenu[2] ).addClass( 'current' );
						break;
					case 'debug' === tab:
						$( submenu[3] ).addClass( 'current' );
						break;
				}
			} )( jQuery, "<?php echo esc_js( $this->tab ); ?>" );
		</script>
		<?php
	}

	/**
	 * Get settings.
	 *
	 * @since 0.1.0
	 *
	 * @return array Settings.
	 */
	public function get_settings() {
		$settings = get_option( 'tgwc_settings' );

		$settings = wp_parse_args(
			$settings,
			array(
				'custom_avatar'                 => true,
				'icon'                          => true,
				'group_accordion_icon'          => false,
				'group_accordion_default_state' => 'expanded',
				'icon_position'                 => 'right',
				'enable_ajax_navigation'        => false,
				'default_endpoint'              => 'dashboard',
				'enable_debug'                  => false,
				'frontend'                      => array(
					'fontawesome'      => array(
						'css' => true,
						'js'  => true,
					),
					'dropzone'         => array(
						'css' => true,
						'js'  => true,
					),
					'jqueryscrolltabs' => array(
						'css' => true,
						'js'  => true,
					),
				),
			)
		);

		return apply_filters( 'tgwc_settings', $settings );
	}

	/**
	 * Get endpoints.
	 *
	 * @since 0.1.0
	 *
	 * @return array Endpoints.
	 */
	public function get_endpoints() {
		$endpoints         = get_option( 'tgwc_endpoints' );
		$default_endpoints = TGWC()->account_menu->get_default_endpoints();

		// Get default WooCommerce endpoints,if not endpoints are in database.
		if ( empty( $endpoints ) ) {
			$endpoints = array();
			foreach ( $default_endpoints as $key => $endpoint ) {
				$endpoints[ $key ] = tgwc_get_default_endpoint_options( $endpoint );
			}
		} else {
			$flat_endpoints = tgwc_get_endpoints_flat( $endpoints );
			$diff_endpoints = array_diff_key( $default_endpoints, $flat_endpoints );
			if ( ! empty( $diff_endpoints ) ) {
				foreach ( $diff_endpoints as $key => $endpoint ) {
					$endpoints[ $key ] = tgwc_get_default_endpoint_options( $endpoint );
				}
			}
		}

		foreach ( $endpoints as $key => $endpoint ) {
			if ( isset( $default_endpoints[ $key ] ) ) {
				$endpoints[ $key ]['slug'] = '';
			} else {
				$endpoints[ $key ]['slug'] = $key;
			}
		}

		// Add default value to endpoints, link and group items.
		foreach ( $endpoints as $key => $endpoint ) {
			if ( 'endpoint' === $endpoint['type'] ) {
				$endpoints[ $key ] = wp_parse_args(
					$endpoint,
					tgwc_get_default_endpoint_options()
				);
			} elseif ( 'link' === $endpoint['type'] ) {
				$endpoints[ $key ] = wp_parse_args(
					$endpoint,
					tgwc_get_default_link_options()
				);
			} elseif ( 'group' === $endpoint['type'] ) {
				$endpoints[ $key ] = wp_parse_args(
					$endpoint,
					tgwc_get_default_group_options()
				);
			}
		}

		// Add default value to group children.
		foreach ( $endpoints as $key => $endpoint ) {
			if ( isset( $endpoint['children'] ) ) {
				foreach ( $endpoint['children'] as $child_slug => $child ) {
					if ( 'endpoint' === $child['type'] ) {
						$endpoints[ $key ]['children'][ $child_slug ] = wp_parse_args(
							$child,
							tgwc_get_default_endpoint_options()
						);
					} elseif ( 'link' === $child['type'] ) {
						$endpoints[ $key ]['children'][ $child_slug ] = wp_parse_args(
							$child,
							tgwc_get_default_link_options()
						);
					} elseif ( 'group' === $child['type'] ) {
						$endpoints[ $key ]['children'][ $child_slug ] = wp_parse_args(
							$child,
							tgwc_get_default_group_options()
						);
					}
				}
			}
		}

		return apply_filters( 'tgwc_get_endpoints', $endpoints );
	}

	/**
	 * Process debug settings.
	 *
	 * @since 0.1.0
	 *
	 * @return array|bool
	 */
	public function process_debug_settings( $settings ) {
		if ( ! isset( $_POST['tgwc_page'] ) || 'debug' !== $_POST['tgwc_page'] ) {
			return tgwc_get_debug_settings();
		}

		// Bail early if the nonce is not set.
		if ( ! isset( $_POST['_wpnonce'] ) ) {
			add_settings_error(
				'tgwc',
				'tgwc_nonce_required',
				esc_html__( 'Nonce is required.', 'customize-my-account-page-for-woocommerce' )
			);
			return false;
		}

		// Bail early if the nonce verification fails.
		$nonce = sanitize_text_field( $_POST['_wpnonce'] );
		if ( false === wp_verify_nonce( $nonce, 'tgwc-options' ) ) {
			add_settings_error(
				'tgwc',
				'tgwc_invalid-nonce',
				esc_html__( 'Invalid nonce.', 'customize-my-account-page-for-woocommerce' )
			);
			return false;
		}

		// Reset settings if the options is so.
		if ( isset( $_POST['tgwc_reset_debug'] ) ) {
			add_settings_error(
				'tgwc',
				'tgwc-reset-debug-settings',
				esc_html__( 'Debug settings reset successfully.', 'customize-my-account-page-for-woocommerce' ),
				'success'
			);
			return false;
		}

		$settings['enable_debug'] = isset( $settings['enable_debug'] ) && tgwc_string_to_bool( $settings['enable_debug'] );

		$settings = tgwc_parse_args(
			$settings,
			array(
				'frontend' => array(
					'dropzone'         => array(),
					'jqueryscrolltabs' => array(),
				),
			)
		);

		if ( isset( $settings['frontend'] ) ) {
			foreach ( $settings['frontend'] as $library => $setting ) {
				$settings['frontend'][ $library ]['css'] = isset( $settings['frontend'][ $library ]['css'] ) && $settings['frontend'][ $library ]['css'];
				$settings['frontend'][ $library ]['js']  = isset( $settings['frontend'][ $library ]['js'] ) && $settings['frontend'][ $library ]['js'];
			}
		}

		add_settings_error(
			'tgwc',
			'tgwc_debug_saved',
			esc_html__( 'Debug settings saved successfully', 'customize-my-account-page-for-woocommerce' ),
			'success'
		);

		return $settings;
	}

	/**
	 * Migrating the debug setting to settings.
	 *
	 * @return void
	 */
	public function run_migrate_debug_to_setting() {

		if ( get_option( 'tgwc_flat_settings_migrated', false ) ) {
			return;
		}

		$main_settings  = $this->get_settings();
		$debug_settings = tgwc_get_debug_settings();

		if ( $debug_settings !== false ) {

			$main_settings['enable_debug'] = isset( $debug_settings['enable_debug'] ) ? $debug_settings['enable_debug'] : false;

			if ( isset( $debug_settings['frontend'] ) ) {

				$main_settings['frontend'] = array(
					'fontawesome'      => array(
						'css' => isset( $debug_settings['frontend']['fontawesome']['css'] ) ? $debug_settings['frontend']['fontawesome']['css'] : true,
						'js'  => isset( $debug_settings['frontend']['fontawesome']['js'] ) ? $debug_settings['frontend']['fontawesome']['js'] : true,
					),
					'dropzone'         => array(
						'css' => isset( $debug_settings['frontend']['dropzone']['css'] ) ? $debug_settings['frontend']['dropzone']['css'] : true,
						'js'  => isset( $debug_settings['frontend']['dropzone']['js'] ) ? $debug_settings['frontend']['dropzone']['js'] : true,
					),
					'jqueryscrolltabs' => array(
						'css' => isset( $debug_settings['frontend']['jqueryscrolltabs']['css'] ) ? $debug_settings['frontend']['jqueryscrolltabs']['css'] : true,
						'js'  => isset( $debug_settings['frontend']['jqueryscrolltabs']['js'] ) ? $debug_settings['frontend']['jqueryscrolltabs']['js'] : true,
					),
				);
			}

			update_option( 'tgwc_settings', $main_settings );
			delete_option( 'tgwc_debug_settings' );
		}

		update_option( 'tgwc_flat_settings_migrated', time() );
	}
}
