<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$container = get_theme_mod( 'understrap_container_type' );
?>

<?php get_template_part( 'sidebar-templates/sidebar', 'footerfull' ); ?>

<div class="wrapper" id="wrapper-footer">

	<div class="<?php echo esc_attr( $container ); ?>">

		<div class="row">

			<div class="col-md-12">

				<footer class="site-footer" id="colophon">

					<div class="site-info">
						<ul>
							<li id="copyright">© Copyright 2023 The Informed Renter. All rights reserved.</li>
							<li><a href="/legal/privacy-policy/">Privacy Policy</a></li>
							<li><a href="/legal/terms-of-use/">Terms of Use</a></li>
							<li><a href="/legal/compliance/">Compliance</a></li>
							<li><a href="/legal/accessibility/">Accessibility</a></li>
						</ul>
					<?php
					/*
						wp_nav_menu(
							array(
								'theme_location'  => 'footer-legal',
								'container_class' => '',
								'container_id'    => '',
								'menu_class'      => 'navbar-nav ms-auto',
								'fallback_cb'     => '',
								'menu_id'         => 'footer-legal-menu',
								'depth'           => 2,
								'walker'          => new Understrap_WP_Bootstrap_Navwalker(),
							)
						);
						*/
					?>


					</div><!-- .site-info -->

				</footer><!-- #colophon -->

			</div><!-- col -->

		</div><!-- .row -->

	</div><!-- .container(-fluid) -->

</div><!-- #wrapper-footer -->

<?php // Closing div#page from header.php. ?>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>

</html>

