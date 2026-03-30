<?php
/**
 * Template hooks.
 *
 * @since 0.1.0
 */

// My Account.
if ( ! function_exists( 'add_action' ) ) {
	return;
}
add_action( 'woocommerce_account_navigation', 'tgwc_account_navigation', 10, 3 );
add_action( 'woocommerce_account_content', 'tgwc_account_content' );
add_action( 'tgwc_my_account_menu_item', 'tgwc_display_myaccount_menu_item', 10 );
add_action( 'tgwc_before_account_navigation_wrap', 'tgwc_user_avatar' );
if ( tgwc_is_old_user() ) {
	add_action( 'woocommerce_account_content', 'tgwc_render_customizer_edit_button', 9 );
}
add_action( 'woocommerce_account_navigation', 'tgwc_render_customizer_edit_button', 9 );
add_action( 'tgwc_before_account_items_list', 'tgwc_render_customizer_edit_button' );
add_action( 'tgwc_before_user_image', 'tgwc_render_customizer_edit_button' );
add_filter( 'get_avatar', 'tgwc_replace_gravatar_image', PHP_INT_MAX - 1, 6 );
