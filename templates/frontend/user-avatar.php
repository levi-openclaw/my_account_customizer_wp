<?php
/**
 * User avatar template.
 *
 * @since 0.1.0
 */

use ThemeGrill\WoocommerceCustomizer\Icon;

?>
<div class="tgwc-user-avatar <?php echo esc_html( tgwc_get_avatar_layout() ); ?>">
	<div class="tgwc-profile-upload-limit-issue tgwc-hide">
		<?php
		printf(
			esc_html__( 'Upload failed. Maximum allowed size is %s MB.', 'customize-my-account-page-for-woocommerce' ),
			esc_html( tgwc_get_avatar_upload_size_mb() )
		);
		?>
	</div>
	<?php do_action( 'tgwc_before_user_image' ); ?>
	<form action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>"
		class="dropzone"
		id="tgwc-file-drop-zone"
		enctype="multipart/form-data">
		<?php wp_nonce_field( 'tgwc_avatar_upload', 'tgwc_avatar_upload_nonce' ); ?>
		<input type="hidden" name="action" value="tgwc_avatar_upload" />
		<div class="tgwc-user-avatar-image-wrap <?php echo esc_html( tgwc_get_avatar_type() ); ?>">
			<?php
				$allowed_html = array(
					'img' => array(
						'src'      => array(),
						'alt'      => array(),
						'class'    => array(),
						'width'    => array(),
						'height'   => array(),
						'srcset'   => array(),
						'sizes'    => array(),
						'decoding' => array(),
					),
				);

				$avatar = get_avatar( get_current_user_id() );

				if ( strpos( $avatar, 'd=mm' ) !== false || empty( $avatar ) ) {
					$avatar = tgwc_get_default_avatar();
				}

				echo wp_kses( $avatar, $allowed_html );
				?>
			<?php if ( apply_filters( 'tgwc_hide_upload_avatar_button', true, $args ) ) { ?>
			<a class="tgwc-user-avatar-upload-icon">
				<?php Icon::get_svg_icon( 'camera', true ); ?>
			</a>

			<div class="tgwc-remove-image<?php echo esc_attr( $is_avatar_set ? '' : ' tgwc-display-none' ); ?>">
				<?php Icon::get_svg_icon( 'times-circle', true ); ?>
			</div>
			<div class="tgwc-progress tgwc-display-none">
				<?php Icon::get_svg_icon( 'spinner', true ); ?>
			</div>
			<div class="tgwc-error-message tgwc-display-none"></div>
			<?php } ?>
		</div>
	</form>
	<?php do_action( 'tgwc_after_user_image' ); ?>

	<?php do_action( 'tgwc_before_user_info' ); ?>
	<div class="tgwc-user-info">
		<h4 class="tgwc-user-id <?php echo esc_html( tgwc_get_avatar_username() ); ?>"><?php the_author_meta( 'display_name', get_current_user_id() ); ?></h4>
		<?php
			$customize       = get_option( 'tgwc_customize' );
			$show_logout_btn = isset( $customize['navigation']['show_logout_btn'] ) ? $customize['navigation']['show_logout_btn'] : false;

		if ( $show_logout_btn || is_customize_preview() ) {
			?>
				<a href="<?php echo esc_url( wc_logout_url() ); ?>" class="button <?php echo esc_html( tgwc_get_avatar_logout_button() ); ?>">
				<?php esc_html_e( 'Logout', 'customize-my-account-page-for-woocommerce' ); ?>
				</a>
			<?php
		}
		do_action( 'tgwc_user_info' );
		?>
	</div>
	<?php do_action( 'tgwc_after_user_info' ); ?>
</div>
<?php
