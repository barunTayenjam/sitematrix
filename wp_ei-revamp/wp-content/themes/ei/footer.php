<?php
/**
 * Footer: columns + site info.
 *
 * @package Ei
 */
?>
	</main>

<footer class="ei-footer">
	<div class="container">
		<div class="ei-footer-cols">
			<div class="ei-footer-about">
				<?php if ( has_custom_logo() ) : ?>
					<?php the_custom_logo( array( 'class' => 'ei-footer-logo' ) ); ?>
				<?php else : ?>
					<img src="<?php echo esc_url( wp_get_attachment_url( 3282 ) ); ?>" alt="<?php bloginfo( 'name' ); ?>" class="ei-footer-logo">
				<?php endif; ?>
				<p style="margin-top: 1rem; font-size: 0.85rem;"><?php esc_html_e( 'Manufacturer, supplier and exporter of analytical, scientific laboratory and pharmaceutical instruments.', 'ei' ); ?></p>
			</div>

			<div class="ei-footer-links">
				<h4><?php esc_html_e( 'Product Categories', 'ei' ); ?></h4>
				<ul>
					<?php foreach ( get_terms( array( 'taxonomy' => 'products', 'parent' => 0, 'hide_empty' => false, 'orderby' => 'name' ) ) as $term ) : ?>
						<li><a href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div class="ei-footer-links">
				<h4><?php esc_html_e( 'Quick Links', 'ei' ); ?></h4>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/products/' ) ); ?>"><?php esc_html_e( 'Catalogue', 'ei' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/about-us/' ) ); ?>"><?php esc_html_e( 'About Us', 'ei' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact Us', 'ei' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/sitemap/' ) ); ?>"><?php esc_html_e( 'Sitemap', 'ei' ); ?></a></li>
				</ul>
			</div>

			<div class="ei-footer-contact">
				<h4><?php esc_html_e( 'Contact', 'ei' ); ?></h4>
				<p><?php esc_html_e( 'Electronics India Instruments', 'ei' ); ?></p>
				<p><?php esc_html_e( 'Ambala - 134 003, Haryana, India', 'ei' ); ?></p>
				<p><strong>Call:</strong> <a href="tel:+911725033251">91-172-5033251</a></p>
				<p><strong>Email:</strong> <a href="mailto:info@electronicsindia.co.in">info@electronicsindia.co.in</a></p>
			</div>
		</div>

		<div class="ei-footer-bottom">
			<span>&copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php esc_html_e( 'Electronics India. All rights reserved.', 'ei' ); ?></span>
			<?php wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'echo' => false ) ); ?>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
