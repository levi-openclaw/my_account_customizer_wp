<?php
/**
 * Template name: WooCommerce - My Account for Flatsome.
 */

get_header(); ?>

<?php do_action( 'flatsome_before_page' ); ?>

<?php wc_get_template( 'myaccount/header.php' ); ?>

<div class="page-wrapper my-account mb">
	<div class="container" role="main">
		<?php
		while ( have_posts() ) {
			the_post();
			the_content();
		}
		?>
	</div>
</div>

<?php do_action( 'flatsome_after_page' ); ?>

<?php get_footer(); ?>
