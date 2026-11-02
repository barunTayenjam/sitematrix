<?php
/**
 * Footer — Double Beam: brand + categories, company, support, contact.
 *
 * @package Ei
 */

?>
	</main>

<footer class="site-foot">
	<div class="foot-grid">
		<div class="foot-col">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<img src="<?php echo esc_url( wp_get_attachment_url( 3282 ) ); ?>" alt="<?php bloginfo( 'name' ); ?>" width="200" height="37">
			<?php endif; ?>
			<p class="foot-dek"><?php esc_html_e( 'Manufacturer, supplier and exporter of analytical, scientific laboratory and pharmaceutical instruments. ISO certified. Parwanoo, Himachal Pradesh, India.', 'ei' ); ?></p>
		</div>

		<div class="foot-col">
			<h4><?php esc_html_e( 'Instruments', 'ei' ); ?></h4>
			<ul>
				<?php
				$foot_terms = get_terms(
					array(
						'taxonomy'   => 'products',
						'parent'     => 0,
						'hide_empty' => false,
						'orderby'    => 'name',
						'number'     => 8,
					)
				);
				if ( ! is_wp_error( $foot_terms ) ) :
					foreach ( $foot_terms as $foot_term ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride
						?>
						<li><a href="<?php echo esc_url( get_term_link( $foot_term ) ); ?>"><?php echo esc_html( $foot_term->name ); ?></a></li>
						<?php
					endforeach;
endif;
				?>
			</ul>
		</div>

		<div class="foot-col">
			<h4><?php esc_html_e( 'Company', 'ei' ); ?></h4>
			<ul>
				<li><a href="<?php echo esc_url( home_url( '/about-us/' ) ); ?>"><?php esc_html_e( 'About Us', 'ei' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/quality-control/' ) ); ?>"><?php esc_html_e( 'Quality Control', 'ei' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/research-and-development/' ) ); ?>"><?php esc_html_e( 'R &amp; D', 'ei' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/clients/' ) ); ?>"><?php esc_html_e( 'Clients', 'ei' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/become-a-dealer/' ) ); ?>"><?php esc_html_e( 'Become a Dealer', 'ei' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/site-map/' ) ); ?>"><?php esc_html_e( 'Sitemap', 'ei' ); ?></a></li>
			</ul>
		</div>

		<div class="foot-col">
			<h4><?php esc_html_e( 'Contact', 'ei' ); ?></h4>
			<ul>
				<li><a href="tel:+911725033251">+91 172 5033251</a></li>
				<li><a href="mailto:info@electronicsindia.co.in">info@electronicsindia.co.in</a></li>
				<li>Parwanoo, Dist. Solan<br>Himachal Pradesh 173220, India</li>
			</ul>
		</div>
	</div>

	<div class="foot-bottom">
		<span>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php esc_html_e( 'Electronics India. All rights reserved.', 'ei' ); ?></span>
		<span>
			<a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'ei' ); ?></a>
			&middot;
			<a href="<?php echo esc_url( home_url( '/terms-conditions/' ) ); ?>"><?php esc_html_e( 'Terms &amp; Conditions', 'ei' ); ?></a>
		</span>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
