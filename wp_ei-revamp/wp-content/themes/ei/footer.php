<?php
/**
 * Footer — 10K Agency.
 * Technical Luxury: obsidian colophon, minimal stacked navigation.
 *
 * @package Ei
 */

?>

</main>

<footer class="site-footer">
	<div class="footer-inner">
		<div class="footer-brand">
			<a class="nav-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">10<span class="accent">K</span> Agency</a>
			<p class="footer-dek"><?php esc_html_e( 'Precision instruments, engineered and documented in-house.', 'ei' ); ?></p>
		</div>
		<nav class="footer-nav" aria-label="Footer">
			<?php
			$footer_nav_items = array(
				array(
					'label' => __( 'Manifesto', 'ei' ),
					'url'   => home_url( '/#s1' ),
				),
				array(
					'label' => __( 'Specimens', 'ei' ),
					'url'   => home_url( '/#s2' ),
				),
				array(
					'label' => __( 'Catalogue', 'ei' ),
					'url'   => home_url( '/products/' ),
				),
				array(
					'label' => __( 'Quality', 'ei' ),
					'url'   => home_url( '/quality-control/' ),
				),
				array(
					'label' => __( 'R&D', 'ei' ),
					'url'   => home_url( '/research-and-development/' ),
				),
				array(
					'label' => __( 'About', 'ei' ),
					'url'   => home_url( '/about-us/' ),
				),
				array(
					'label' => __( 'Contact', 'ei' ),
					'url'   => home_url( '/contact/' ),
				),
			);
			foreach ( $footer_nav_items as $item ) :
				?>
				<a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a>
			<?php endforeach; ?>
		</nav>
		<div class="footer-contact">
			<div class="footer-contact-item">
				<span class="footer-contact-label"><?php esc_html_e( 'Phone', 'ei' ); ?></span>
				<a href="tel:+911725033251"><?php esc_html_e( '+91 172 5033251', 'ei' ); ?></a>
			</div>
			<div class="footer-contact-item">
				<span class="footer-contact-label"><?php esc_html_e( 'Email', 'ei' ); ?></span>
				<a href="mailto:info@electronicsindia.co.in"><?php esc_html_e( 'info@electronicsindia.co.in', 'ei' ); ?></a>
			</div>
			<div class="footer-contact-item">
				<span class="footer-contact-label"><?php esc_html_e( 'WhatsApp', 'ei' ); ?></span>
				<a href="https://wa.me/919056777299"><?php esc_html_e( '+91 90567 77299', 'ei' ); ?></a>
			</div>
			<div class="footer-contact-item">
				<span class="footer-contact-label"><?php esc_html_e( 'Address', 'ei' ); ?></span>
				<span>111, Industrial Area, Phase - 2<br>Panchkula-134113<br>Haryana, India</span>
			</div>
		</div>
	</div>
	<div class="footer-bottom">
		<div class="footer-bottom-inner">
			<span>© <?php echo esc_html( gmdate( 'Y' ) ); ?> 10K Agency.</span>
			<span>Designed and built for precision.</span>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
