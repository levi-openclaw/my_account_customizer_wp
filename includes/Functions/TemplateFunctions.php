<?php
/**
 * Template functions
 */

if ( ! function_exists( 'tgwc_account_navigation' ) ) {
	/**
	 * Custom WooCommerce My Account Navigation.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function tgwc_account_navigation() {
		$settings = TGWC()->get_settings()->get_settings();

		$settings['nav_class'] = array(
			'tgwc-woocommerce-MyAccount-navigation',
			'tgwc-nav--collapsed',
		);

		$settings['nav_class'] = apply_filters( 'tgwc_nav_class', $settings['nav_class'] );
		$settings['nav_class'] = implode( ' ', $settings['nav_class'] );

		wc_get_template(
			'frontend/navigation.php',
			$settings,
			TGWC_TEMPLATE_PATH,
			TGWC_TEMPLATE_PATH
		);
	}
}

if ( ! function_exists( 'tgwc_display_myaccount_menu_item' ) ) {
	/**
	 * Display account menu item.
	 *
	 * @since 0.1.0
	 *
	 * @param string $slug  Endpoint slug.
	 *
	 * @return void
	 */
	function tgwc_display_myaccount_menu_item( $slug ) {
		$endpoint = \tgwc_get_endpoint( $slug );

		if ( false === $endpoint ) {
			return;
		}

		$account_menu_types = array(
			'endpoint',
			'link',
			'group',
		);

		$type             = $endpoint['type'];
		$choose_icon_type = ! empty( $endpoint['choose_icon_type'] ) ? $endpoint['choose_icon_type'] : 'choose_icon';

		if ( 'choose_icon' !== $choose_icon_type ) {
			$endpoint['choose_icon_type'] = isset( $endpoint['custom_icon'] ) ? $endpoint['custom_icon'] : '';
		} else {
			$endpoint['choose_icon_type'] = $choose_icon_type;
		}

		if ( in_array( $type, $account_menu_types, true ) ) {
			$tgwc_override_endpoint_logout_button = get_option( 'tgwc_override_endpoint_logout_button', '' );
			$endpoint['slug']                     = $slug;

			if ( ! is_customize_preview() && 'customer-logout' === $slug && '' !== $tgwc_override_endpoint_logout_button ) {
				return;
			}

			$endpoint['label'] = tgwc_translate_dynamic_string( $endpoint['label'], "{$slug}_label" );
			wc_get_template(
				"frontend/{$type}-item.php",
				$endpoint,
				TGWC_TEMPLATE_PATH,
				TGWC_TEMPLATE_PATH
			);
		}
	}
}

if ( ! function_exists( 'tgwc_account_content' ) ) {
	/**
	 * Custom WooCommerce My Account Content.
	 *
	 * Override the woocommerce_account_content().
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function tgwc_account_content() {
		global $wp;

		if ( ! empty( $wp->query_vars ) ) {
			foreach ( $wp->query_vars as $slug => $value ) {
				// TODO: Inform the user that the following text cannot be used as slugs.
				if ( in_array( $slug, array( 'pagename', 'page', 'page_id', 'preview', 'dashboard' ), true ) ) {
					continue;
				}

				if ( has_action( 'woocommerce_account_' . $slug . '_endpoint' ) ) {
					$position = tgwc_get_endpoint_content_position( $slug );
					if ( 'main' === $position ) {
						tgwc_display_account_content( $slug );
					} else {
						( 'before' === $position ) && tgwc_display_account_content( $slug );
						do_action( 'woocommerce_account_' . $slug . '_endpoint', $value );
						( 'after' === $position ) && tgwc_display_account_content( $slug );
					}
					return;
				}

				if ( tgwc_display_account_content( $slug ) ) {
					return;
				}
			}
		}

		$default_endpoint = tgwc_get_default_endpoint();
		$position         = tgwc_get_endpoint_content_position( $default_endpoint );
		if ( ! isset( $wp->query_vars['dashboard'] )
			&& has_action( 'woocommerce_account_' . $default_endpoint . '_endpoint' ) ) {
			if ( 'main' === $position ) {
				tgwc_display_account_content( $default_endpoint );
			} else {
				( 'before' === $position ) && tgwc_display_account_content( $default_endpoint );
				do_action( 'woocommerce_account_' . $default_endpoint . '_endpoint', $value );
				( 'after' === $position ) && tgwc_display_account_content( $default_endpoint );
			}
		} elseif ( isset( $wp->query_vars['dashboard'] ) || 'dashboard' === $default_endpoint ) {
			tgwc_display_dashboard();
		} else {
			tgwc_display_account_content( $default_endpoint );
		}
	}
}

if ( ! function_exists( 'tgwc_display_dashboard' ) ) {
	/**
	 * Display dashboard when it is not set to default endpoint.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function tgwc_display_dashboard() {
		$endpoints = tgwc_get_endpoints_flat();

		if ( ! isset( $endpoints['edit-account'] ) ) {
			wc_get_template(
				'myaccount/dashboard.php',
				array(
					'current_user' => get_user_by( 'id', get_current_user_id() ),
				)
			);
		}

		$endpoint = $endpoints['dashboard'];
		$content  = trim( $endpoint['content'] );

		if ( ! $endpoint['enable'] ) {
			return;
		}

		$position = tgwc_get_endpoint_content_position( 'dashboard' );

		if ( empty( $content ) ) {
			wc_get_template(
				'myaccount/dashboard.php',
				array(
					'current_user' => get_user_by( 'id', get_current_user_id() ),
				)
			);
			return;
		}

		if ( 'main' === $position ) {
			tgwc_display_account_content( 'dashboard' );
			return;
		}

		'before' === $position && tgwc_display_account_content( 'dashboard' );

		wc_get_template(
			'myaccount/dashboard.php',
			array(
				'current_user' => get_user_by( 'id', get_current_user_id() ),
			)
		);

		'after' === $position && tgwc_display_account_content( 'dashboard' );
	}
}

if ( ! function_exists( 'tgwc_display_account_content' ) ) {
	/**
	 * Display the account content template.
	 *
	 * @since 0.1.0
	 *
	 * @param string $slug Endpoint slug.
	 * @return boolean True if the template is found, False otherwise.
	 */
	function tgwc_display_account_content( $slug ) {
		$endpoint = tgwc_get_endpoint( $slug );

		// Bail early if endpoint doesn't exist.
		if ( ! $endpoint ) {
			return false;
		}

		$content       = apply_filters( 'tgwc_parse_smart_tag', trim( $endpoint['content'] ), $endpoint );
		$default_class = array_merge(
			(array) $endpoint['class'],
			array(
				'tgwc-account_content',
				"tgwc-account_content_{$slug}",
				'tgwc-sidecontent-over-shadow',
			)
		);

		if ( $endpoint['enable'] && ! empty( $content ) ) {
			$class               = apply_filters( 'tgwc_account_content_class', $default_class );
			$endpoint['slug']    = $slug;
			$endpoint['class']   = implode( ' ', $class );
			$endpoint['content'] = tgwc_translate_dynamic_string( $content, "{$slug}_content" );

			wc_get_template(
				'frontend/account-content.php',
				$endpoint,
				TGWC_TEMPLATE_PATH,
				TGWC_TEMPLATE_PATH
			);
		}

		return true;
	}
}

if ( ! function_exists( 'tgwc_user_avatar' ) ) {
	/**
	 * Display user avatar.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function tgwc_user_avatar() {
		$settings = TGWC()->get_settings()->get_settings();

		// Bail if custom avatar is not enabled.
		if ( ! $settings['custom_avatar'] && apply_filters( 'tgwc_hide_custom_avatar_section_with_username', true, $settings ) ) {
			return;
		}

		$avatar_image              = get_user_meta( get_current_user_id(), 'tgwc_avatar_image', true );
		$settings['is_avatar_set'] = $avatar_image ? true : false;

		wc_get_template(
			'frontend/user-avatar.php',
			$settings,
			TGWC_TEMPLATE_PATH,
			TGWC_TEMPLATE_PATH
		);
	}
}

if ( ! function_exists( 'tgwc_replace_gravatar_image' ) ) {
	/**
	 * Replace Gravatar image with custom avatar.
	 *
	 * @param string $avatar      HTML for the user's avatar.
	 * @param mixed  $id_or_email The avatar to retrieve. Accepts a user_id, Gravatar MD5 hash,
	 *                            user email, WP_User object, WP_Post object, or WP_Comment object.
	 * @param int    $size        Square avatar width and height in pixels to retrieve.
	 * @param string $default     URL for the default image or a default type. Accepts '404', 'retro', 'monsterid',
	 *                            'wavatar', 'indenticon', 'mystery', 'mm', 'mysteryman', 'blank', or 'gravatar_default'.
	 * @param string $alt         Alternative text to use in the avatar image tag.
	 * @param array  $args        Arguments passed to get_avatar_data(), after processing.
	 * @return string
	 */
	function tgwc_replace_gravatar_image( $avatar, $id_or_email, $size, $default, $alt, $args ) {

		// Bail early if TGWC is not active.
		if ( ! function_exists( 'TGWC' ) || is_null( TGWC() ) || is_null( TGWC()->get_settings() ) ) {
			return $avatar;
		}

		remove_all_filters( 'get_avatar' );
		add_filter( 'get_avatar', 'tgwc_replace_gravatar_image', PHP_INT_MAX, 6 );

		$settings = TGWC()->get_settings()->get_settings();

		// Bail if custom avatar is not enabled.
		if ( ! $settings['custom_avatar'] ) {
			return $avatar;
		}

		// Bail early if the user is not logged in.
		if ( 0 === $id_or_email ) {
			return $avatar;
		}

		$attach_id  = get_user_meta( $id_or_email, 'tgwc_avatar_image', true );
		$image_size = apply_filters( 'tgwc_myaccount_image_size', 'thumbnail' );
		$image      = wp_get_attachment_image_src( $attach_id, $image_size );

		// Bail early if the image is not set.
		if ( false === $image ) {
			return $avatar;
		}

		return sprintf(
			'<img alt="%s" src="%s" srcset="%s" class="avatar avatar-%s photo" height="%s" width="%s" %s/>',
			esc_attr( $alt ),
			esc_url( $image[0] ),
			esc_url( $image[0] ) . ' 2x',
			$size,
			(int) $args['height'],
			(int) $args['width'],
			$args['extra_attr']
		);
	}
}
