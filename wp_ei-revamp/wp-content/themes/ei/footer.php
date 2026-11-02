<?php
/**
 * Footer: mono colophon, light paper, hairline top rule.
 *
 * @package Ei
 */
?>

</main>

<footer class="site-foot mono">
	<div class="foot-inner" style="max-width:960px;margin:0 auto;padding:0 40px;">
		<div class="foot-grid">
			<div class="foot-col">
				<?php if ( has_custom_logo() ) : ?>
					<?php the_custom_logo(); ?>
				<?php else : ?>
					<img src="<?php echo esc_url( wp_get_attachment_url( 3282 ) ); ?>" alt="<?php bloginfo( 'name' ); ?>" width="200" height="37">
				<?php endif; ?>
				<p class="foot-dek" style="font-size:0.7rem;letter-spacing:0.12em;color:var(--stone);margin-top:10px;">
					<?php esc_html_e( 'Manufacturer, supplier and exporter of analytical, scientific laboratory and pharmaceutical instruments. ISO certified. Parwanoo, Himachal Pradesh, India.', 'ei' ); ?>
				</p>
			</div>
			<div class="foot-col">
				<h5 style="font-size:0.7rem;letter-spacing:0.2em;text-transform:uppercase;color:var(--stone);margin-bottom:10px;">Instruments</h5>
				<ul style="list-style:none;">
					<?php
					$foot_terms = get_terms( array( 'taxonomy' => 'products', 'parent' => 0, 'hide_empty' => false, 'orderby' => 'name', 'number' => 8 ) );
					if ( ! is_wp_error( $foot_terms ) ) :
						foreach ( $foot_terms as $foot_term ) :
							?>
							<li style="margin-bottom:6px;"><a href="<?php echo esc_url( get_term_link( $foot_term ) ); ?>" style="color:var(--ink);font-size:0.85rem;"><?php echo esc_html( $foot_term->name ); ?></a></li>
						<?php endforeach;
					endif;
					?>
				</ul>
			</div>
			<div class="foot-col">
				<h5 style="font-size:0.7rem;letter-spacing:0.2em;text-transform:uppercase;color:var(--stone);margin-bottom:10px;">Company</h5>
				<ul style="list-style:none;">
					<li style="margin-bottom:6px;"><a href="<?php echo esc_url( home_url( '/about-us/' ) ); ?>" style="color:var(--ink);font-size:0.85rem;"><?php esc_html_e( 'About Us', 'ei' ); ?></a></li>
					<li style="margin-bottom:6px;"><a href="<?php echo esc_url( home_url( '/quality-control/' ) ); ?>" style="color:var(--ink);font-size:0.85rem;"><?php esc_html_e( 'Quality Control', 'ei' ); ?></a></li>
					<li style="margin-bottom:6px;"><a href="<?php echo esc_url( home_url( '/research-and-development/' ) ); ?>" style="color:var(--ink);font-size:0.85rem;"><?php esc_html_e( 'R &amp; D', 'ei' ); ?></a></li>
					<li style="margin-bottom:6px;"><a href="<?php echo esc_url( home_url( '/clients/' ) ); ?>" style="color:var(--ink);font-size:0.85rem;"><?php esc_html_e( 'Clients', 'ei' ); ?></a></li>
					<li style="margin-bottom:6px;"><a href="<?php echo esc_url( home_url( '/become-a-dealer/' ) ); ?>" style="color:var(--ink);font-size:0.85rem;"><?php esc_html_e( 'Become a Dealer', 'ei' ); ?></a></li>
					<li style="margin-bottom:6px;"><a href="<?php echo esc_url( home_url( '/site-map/' ) ); ?>" style="color:var(--ink);font-size:0.85rem;"><?php esc_html_e( 'Sitemap', 'ei' ); ?></a></li>
				</ul>
			</div>
			<div class="foot-col">
				<h5 style="font-size:0.7rem;letter-spacing:0.2em;text-transform:uppercase;color:var(--stone);margin-bottom:10px;">Contact</h5>
				<ul style="list-style:none;">
					<li style="margin-bottom:6px;"><a href="tel:+911725033251" style="color:var(--ink);font-size:0.85rem;">+91 172 5033251</a></li>
					<li style="margin-bottom:6px;"><a href="mailto:info@electronicsindia.co.in" style="color:var(--ink);font-size:0.85rem;">info@electronicsindia.co.in</a></li>
					<li style="margin-bottom:6px;font-size:0.85rem;color:var(--stone);">Parwanoo, Dist. Solan<br>Himachal Pradesh 173220, India</li>
				</ul>
			</div>
		</div>
		<div class="foot-bottom" style="border-top:1px solid var(--line);padding-top:20px;display:flex;justify-content:space-between;gap:16px;flex-wrap:wrap;font-size:0.72rem;color:var(--stone);">
			<span>© <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php esc_html_e( 'Electronics India. All rights reserved.', 'ei' ); ?></span>
			<span>
				<a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>" style="color:var(--ink);"><?php esc_html_e( 'Privacy Policy', 'ei' ); ?></a>
				&nbsp;·&nbsp;
				<a href="<?php echo esc_url( home_url( '/terms-conditions/' ) ); ?>" style="color:var(--ink);"><?php esc_html_e( 'Terms &amp; Conditions', 'ei' ); ?></a>
			</span>
			<span>SET IN IBM PLEX · SHEETS 01–05</span>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>