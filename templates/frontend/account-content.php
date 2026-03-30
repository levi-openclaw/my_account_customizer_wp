<?php
/**
 * Account content template.
 *
 * @since 0.1.0
 */

defined( 'ABSPATH' ) || exit;
?>

<?php do_action( 'tgwc_before_account_content_container', $slug ); ?>
<div class="<?php echo esc_attr( $class ); ?>"
	id="tgwc-account-content<?php echo esc_attr( $slug ); ?>">

	<?php
	do_action( 'tgwc_before_account_content_title', $slug );

	$content_title = apply_filters( 'tgwc_account_content_title', $label, $slug );

	/**
	 * Added the endpoint specifict filter for title.
	 *
	 * @since 0.2.0
	 */
	$content_title = apply_filters( "tgwc_account_content_title-{$slug}", $label, $slug );

	/**
	 * Added filter to hide/show the title.
	 *
	 * @since 0.2.0
	 */
	if ( apply_filters( 'tgwc_display_account_content_title', false, $label, $slug ) ) {
		printf( '<h2>%s</h2>', esc_html( $content_title ) );
	}
	do_action( 'tgwc_after_account_content_title', $slug );
	?>

	<?php do_action( 'tgwc_before_account_content', $slug ); ?>
	<?php echo do_shortcode( $content ); ?>
	<?php do_action( 'tgwc_after_account_content', $slug ); ?>
</div>
<?php
do_action( 'tgwc_after_account_content_container', $slug );
