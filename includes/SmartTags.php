<?php
/**
 * SmartTags page.
 *
 * @package ThemeGrill\WoocommerceCustomizer
 * @since 0.1.0
 */

namespace ThemeGrill\WoocommerceCustomizer;

defined( 'ABSPATH' ) || exit;

class SmartTags {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		if ( ! did_action( 'tgwc_smart_tags_init' ) ) {
			$this->init_hooks();
			do_action( 'tgwc_smart_tags_init' );
		}
	}

	/**
	 * Init hooks function.
	 *
	 * @since 2.0.0
	 */
	public function init_hooks() {
		add_filter( 'tgwc_parse_smart_tag', array( $this, 'tgwc_parse_smart_tag' ), 10, 2 );
		add_action( 'media_buttons', array( $this, 'media_button' ), 15 );
	}

	/**
	 * Render smart tags.
	 *
	 * @since 2.0.0
	 */
	public function tgwc_select_smart_tags( $editor_id ) {
		$smart_tags_list = $this->smart_tags_list();

		$selector  = '<a id="tgwc-smart-tags-selector">';
		$selector .= Icon::get_svg_icon( 'tgwc-smart-tag' );
		$selector .= esc_html__( 'Add Smart Tags', 'customize-my-account-page-for-woocommerce' );
		$selector .= '</a>';
		$selector .= '<select class="select-smart-tags" style="display: none;">';
		$selector .= '<option></option>';

		foreach ( $smart_tags_list as $key => $value ) {
			$selector .= '<option class="ur-select-smart-tag" value = "' . esc_attr( $key ) . '"> ' . esc_html( $value ) . '</option>';
		}
		$selector .= '</select>';

		echo $selector; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Process the smart tags.
	 *
	 * @since 2.0.0
	 */
	public function tgwc_parse_smart_tag( $content, $endpoint ) {
		preg_match_all( '/\{\{(.*?)\}\}/', $content, $other_tags );

		if ( ! empty( $other_tags[1] ) ) {

			foreach ( $other_tags[1] as $key => $tag ) {
				$other_tag = explode( ' ', $tag )[0];
				switch ( $other_tag ) {
					case 'admin_email':
						$admin_email = sanitize_email( get_option( 'admin_email' ) );
						$content     = str_replace( '{{' . $other_tag . '}}', $admin_email, $content );
						break;

					case 'site_name':
						$site_name = get_option( 'blogname' );
						$content   = str_replace( '{{' . $other_tag . '}}', $site_name, $content );
						break;

					case 'site_url':
						$site_url = get_option( 'siteurl' );
						$content  = str_replace( '{{' . $other_tag . '}}', $site_url, $content );
						break;

					case 'page_title':
						$page_title = get_the_title( get_the_ID() );
						$content    = str_replace( '{{' . $other_tag . '}}', $page_title, $content );
						break;

					case 'page_url':
						$page_url = get_permalink( get_the_ID() );
						$content  = str_replace( '{{' . $other_tag . '}}', $page_url, $content );
						break;

					case 'user_ip_address':
						$user_ip_add = tgwc_get_ip_address();
						$content     = str_replace( '{{' . $other_tag . '}}', $user_ip_add, $content );
						break;

					case 'user_id':
						$user_id = is_user_logged_in() ? get_current_user_id() : '';
						$content = str_replace( '{{' . $other_tag . '}}', $user_id, $content );
						break;

					case 'user_email':
						if ( is_user_logged_in() ) {
							$user  = wp_get_current_user();
							$email = sanitize_email( $user->user_email );
						} else {
							$email = '';
						}
						$content = str_replace( '{{' . $other_tag . '}}', $email, $content );
						break;

					case 'username':
						if ( is_user_logged_in() ) {
							$user = wp_get_current_user();
							$name = sanitize_text_field( $user->user_login );
						} else {
							$name = '';
						}
						$content = str_replace( '{{' . $other_tag . '}}', $name, $content );
						break;

					case 'display_name':
						if ( is_user_logged_in() ) {
							$user = wp_get_current_user();
							$name = sanitize_text_field( $user->display_name );
						} else {
							$name = '';
						}
						$content = str_replace( '{{' . $other_tag . '}}', $name, $content );
						break;

					case 'first_name':
						if ( is_user_logged_in() ) {
							$user = wp_get_current_user();
							$name = sanitize_text_field( $user->user_firstname );
						} else {
							$name = '';
						}
						$content = str_replace( '{{' . $other_tag . '}}', $name, $content );
						break;

					case 'last_name':
						if ( is_user_logged_in() ) {
							$user = wp_get_current_user();
							$name = sanitize_text_field( $user->user_lastname );
						} else {
							$name = '';
						}
						$content = str_replace( '{{' . $other_tag . '}}', $name, $content );
						break;

					case 'current_date':
						$current_date = date_i18n( get_option( 'date_format' ) );
						$content      = str_replace( '{{' . $other_tag . '}}', sanitize_text_field( $current_date ), $content );
						break;
					case 'current_time':
						$current_time = date_i18n( get_option( 'time_format' ) );
						$content      = str_replace( '{{' . $other_tag . '}}', sanitize_text_field( $current_time ), $content );
						break;
					case 'billing_address':
					case 'shipping_address':
						if ( is_user_logged_in() ) {
							$meta_prefix = ( $other_tag === 'billing_address' ) ? 'billing_' : 'shipping_';
							$user_id     = get_current_user_id();
							$address     = array(
								'first_name' => get_user_meta( $user_id, $meta_prefix . 'first_name', true ),
								'last_name'  => get_user_meta( $user_id, $meta_prefix . 'last_name', true ),
								'company'    => get_user_meta( $user_id, $meta_prefix . 'company', true ),
								'address_1'  => get_user_meta( $user_id, $meta_prefix . 'address_1', true ),
								'address_2'  => get_user_meta( $user_id, $meta_prefix . 'address_2', true ),
								'city'       => get_user_meta( $user_id, $meta_prefix . 'city', true ),
								'state'      => get_user_meta( $user_id, $meta_prefix . 'state', true ),
								'postcode'   => get_user_meta( $user_id, $meta_prefix . 'postcode', true ),
								'country'    => get_user_meta( $user_id, $meta_prefix . 'country', true ),
							);

							$address = array_filter( $address );
							if ( ! empty( $address ) ) {
								$formatted_address = $this->get_formatted_address( $address );
								$content           = str_replace( '{{' . $other_tag . '}}', $formatted_address, $content );
							} else {
								$content = str_replace( '{{' . $other_tag . '}}', esc_html__( 'You have not set up this type of address yet.', 'customize-my-account-page-for-woocommerce' ), $content );
							}
						}
						break;
					case 'billing_company':
					case 'shipping_company':
						if ( is_user_logged_in() ) {
							$meta_prefix  = ( $other_tag === 'billing_address' ) ? 'billing_' : 'shipping_';
							$company_name = get_user_meta( $user_id, $meta_prefix . 'company', true );

							if ( empty( $company_name ) ) {
								$company_name = '';
							}

							$content = str_replace( '{{' . $other_tag . '}}', $company_name, $content );
						}
						break;
				}
			}
		}
		return $content;
	}

	/**
	 * Format the address.
	 *
	 * @since 2.0.0
	 */
	private function get_formatted_address( $address ) {
		$countries = new \WC_Countries();
		$formatted = $countries->get_formatted_address( $address );

		if ( empty( $formatted ) ) {
			$formatted = implode(
				'<br>',
				array_filter(
					array(
						trim( $address['first_name'] . ' ' . $address['last_name'] ),
						$address['company'],
						$address['address_1'],
						$address['address_2'],
						trim( $address['city'] . ' ' . $address['state'] . ' ' . $address['postcode'] ),
						$address['country'],
					)
				)
			);
		}

		return wp_kses_post( $formatted );
	}

	/**
	 * Get smart tag lists.
	 *
	 * @since 2.0.0
	 */
	public function smart_tags_list() {
		$smart_tags_list = apply_filters(
			'tgwc_smart_tags_list',
			array(
				'{{user_id}}'          => esc_html__( 'User ID', 'customize-my-account-page-for-woocommerce' ),
				'{{username}}'         => esc_html__( 'User Name', 'customize-my-account-page-for-woocommerce' ),
				'{{user_email}}'       => esc_html__( 'User Email', 'customize-my-account-page-for-woocommerce' ),
				'{{first_name}}'       => esc_html__( 'First Name', 'customize-my-account-page-for-woocommerce' ),
				'{{last_name}}'        => esc_html__( 'Last Name', 'customize-my-account-page-for-woocommerce' ),
				'{{display_name}}'     => esc_html__( 'User Display Name', 'customize-my-account-page-for-woocommerce' ),
				'{{user_ip_address}}'  => esc_html__( 'User IP Address', 'customize-my-account-page-for-woocommerce' ),
				'{{site_name}}'        => esc_html__( 'Site Name', 'customize-my-account-page-for-woocommerce' ),
				'{{site_url}}'         => esc_html__( 'Site URL ', 'customize-my-account-page-for-woocommerce' ),
				'{{page_url}}'         => esc_html__( 'Page URL', 'customize-my-account-page-for-woocommerce' ),
				'{{current_date}}'     => esc_html__( 'Current Date', 'customize-my-account-page-for-woocommerce' ),
				'{{current_time}}'     => esc_html__( 'Current Time', 'customize-my-account-page-for-woocommerce' ),
				'{{billing_address}}'  => esc_html__( 'Billing address', 'customize-my-account-page-for-woocommerce' ),
				'{{billing_company}}'  => esc_html__( 'Billing Company', 'customize-my-account-page-for-woocommerce' ),
				'{{shipping_address}}' => esc_html__( 'Shipping address', 'customize-my-account-page-for-woocommerce' ),
				'{{shipping_company}}' => esc_html__( 'Shipping Company', 'customize-my-account-page-for-woocommerce' ),
			)
		);
		return $smart_tags_list;
	}

	/**
	 * Trigger when editor loads.
	 *
	 * @since 2.0.0
	 */
	public function media_button( $editor_id ) {
		global $pagenow;
		$is_correct_page = (
		'admin.php' === $pagenow &&
		isset( $_GET['page'] ) &&
		'tgwc-customize-my-account-page' === $_GET['page']
		);

		if ( ! $is_correct_page ) {
			return;
		}

		static $smart_tags_html = null;

		if ( null === $smart_tags_html ) {
			ob_start();
			$this->tgwc_select_smart_tags( $editor_id );
			$smart_tags_html = ob_get_clean();

			echo '<script>window.tgwcSmartTagsButton = ' . json_encode( $smart_tags_html ) . ';</script>';
		}

		echo $smart_tags_html; //Phpcs:ignore
	}
}
