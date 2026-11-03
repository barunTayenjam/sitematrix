<?php
/**
 * Footer — 10K Agency · Warm Editorial Light.
 * Paper ground, 2px rule, three-zone grid, mono colophon bar.
 *
 * @package Ei
 */

?>

</main>

<footer class="site-footer">
	<div class="container">
		<div class="footer-grid">
			<div class="footer-brand-col">
				<a class="nav-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<span class="brand-bold">10K</span><span class="brand-serif">Agency</span>
				</a>
				<p class="footer-dek"><?php esc_html_e( 'Precision instruments, engineered and documented in-house. Analytical, scientific, laboratory and pharmaceutical equipment since 1983.', 'ei' ); ?></p>
			</div>
			<nav class="footer-nav-col" aria-label="Footer">
				<span class="mono-micro"><?php esc_html_e( 'Navigate', 'ei' ); ?></span>
				<a href="<?php echo esc_url( home_url( '/#s1' ) ); ?>"><?php esc_html_e( 'Manifesto', 'ei' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/#s2' ) ); ?>"><?php esc_html_e( 'Specimens', 'ei' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/products/' ) ); ?>"><?php esc_html_e( 'Products', 'ei' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/quality-control/' ) ); ?>"><?php esc_html_e( 'Quality', 'ei' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/research-and-development/' ) ); ?>"><?php esc_html_e( 'R&D', 'ei' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/about-us/' ) ); ?>"><?php esc_html_e( 'About', 'ei' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'ei' ); ?></a>
			</nav>
			<div class="footer-contact-col">
				<div class="footer-contact-item">
					<span class="mono-micro"><?php esc_html_e( 'Phone', 'ei' ); ?></span>
					<a href="tel:+911725033251">+91 172 5033251</a>
				</div>
				<div class="footer-contact-item">
					<span class="mono-micro"><?php esc_html_e( 'Email', 'ei' ); ?></span>
					<a href="mailto:info@electronicsindia.co.in">info@electronicsindia.co.in</a>
				</div>
				<div class="footer-contact-item">
					<span class="mono-micro"><?php esc_html_e( 'WhatsApp', 'ei' ); ?></span>
					<a href="https://wa.me/919056777299" rel="noopener">+91 90567 77299</a>
				</div>
				<div class="footer-contact-item">
					<span class="mono-micro"><?php esc_html_e( 'Works', 'ei' ); ?></span>
					<span>111, Industrial Area, Phase 2<br>Panchkula 134113, Haryana, India</span>
				</div>
			</div>
		</div>
		<div class="footer-bottom">
			<span class="mono-micro">&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> 10K Agency</span>
			<span class="mono-micro"><?php esc_html_e( 'Precision, measured', 'ei' ); ?></span>
			<a class="btn-text" href="#content">&uarr; Top</a>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
