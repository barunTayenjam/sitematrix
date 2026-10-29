<?php
/**
 * Footer: brand, categories, quick links, contact.
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
					<?php the_custom_logo(); ?>
				<?php else : ?>
					<img src="<?php echo esc_url( wp_get_attachment_url( 3282 ) ); ?>" alt="<?php bloginfo( 'name' ); ?>" class="ei-footer-logo">
				<?php endif; ?>
				<p style="margin-top: 1rem; font-size: 0.875rem;"><?php esc_html_e( 'Manufacturer, supplier and exporter of analytical, scientific laboratory and pharmaceutical instruments. ISO certified. Based in Parwanoo, Himachal Pradesh, India.', 'ei' ); ?></p>
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
					<li><a href="<?php echo esc_url( home_url( '/quality-control/' ) ); ?>"><?php esc_html_e( 'Quality Control', 'ei' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/research-and-development/' ) ); ?>"><?php esc_html_e( 'R & D', 'ei' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/clients/' ) ); ?>"><?php esc_html_e( 'Clients', 'ei' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/become-a-dealer/' ) ); ?>"><?php esc_html_e( 'Become a Dealer', 'ei' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact Us', 'ei' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/site-map/' ) ); ?>"><?php esc_html_e( 'Sitemap', 'ei' ); ?></a></li>
				</ul>
			</div>

			<div class="ei-footer-links ei-footer-contact">
				<h4><?php esc_html_e( 'Contact', 'ei' ); ?></h4>
				<ul>
					<li><strong>Phone:</strong> <a href="tel:+911725033251">91-172-5033251</a></li>
					<li><strong>Email:</strong> <a href="mailto:info@electronicsindia.co.in">info@electronicsindia.co.in</a></li>
					<li>Parwanoo, Himachal Pradesh 173220, India</li>
				</ul>
			</div>
		</div>

		<div class="ei-footer-bottom">
			<span>&copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php esc_html_e( 'Electronics India. All rights reserved.', 'ei' ); ?></span>
			<span>
				<a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'ei' ); ?></a>
				&middot;
				<a href="<?php echo esc_url( home_url( '/terms-conditions/' ) ); ?>"><?php esc_html_e( 'Terms &amp; Conditions', 'ei' ); ?></a>
			</span>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>