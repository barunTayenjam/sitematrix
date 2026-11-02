<?php
/**
 * Footer — 10K Agency.
 * Dark colophon, ember accent, contact block, legal.
 *
 * @package Ei
 */
?>

</main>

<footer class="site-footer">
	<div class="footer-inner">
		<div class="footer-brand">
			<a class="nav-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">10<span class="accent">K</span> Agency</a>
			<p class="footer-dek" style="font-family:'IBM Plex Mono',ui-monospace,monospace;font-size:0.65rem;letter-spacing:0.15em;color:rgba(255,255,255,0.5);margin-top:10px;">
				10K Agency — precision instruments. Designed, manufactured and exported from Parwanoo since 1983.
			</p>
		</div>
		<nav class="footer-nav" aria-label="Footer">
		<?php
		$footer_nav_items = array(
			array( 'label' => __( 'Home', 'ei' ), 'url' => home_url( '/' ) ),
			array( 'label' => __( 'Products', 'ei' ), 'url' => home_url( '/products/' ) ),
			array( 'label' => __( 'R&D', 'ei' ), 'url' => home_url( '/research-and-development/' ) ),
			array( 'label' => __( 'Quality Control', 'ei' ), 'url' => home_url( '/quality-control/' ) ),
			array( 'label' => __( 'Clients', 'ei' ), 'url' => home_url( '/clients/' ) ),
			array( 'label' => __( 'About Us', 'ei' ), 'url' => home_url( '/about-us/' ) ),
			array( 'label' => __( 'Contact us', 'ei' ), 'url' => home_url( '/contact/' ) ),
		);
		foreach ( $footer_nav_items as $item ) :
			$current = is_front_page() && $item['url'] === home_url( '/' )
				|| ( ! is_front_page() && $item['url'] === home_url( $_SERVER['REQUEST_URI'] ?? '' ) );
			?>
			<a href="<?php echo esc_url( $item['url'] ); ?>" <?php echo $current ? 'aria-current="page"' : ''; ?>>
				<?php echo esc_html( $item['label'] ); ?>
			</a>
		<?php endforeach; ?>
		</nav>
		<div class="footer-contact">
			<div class="footer-contact-item">
				<span class="footer-contact-label mono">Phone</span>
				<a href="tel:+911725033251" style="color:#fff;text-decoration:none;">+91 172 5033251</a>
			</div>
			<div class="footer-contact-item">
				<span class="footer-contact-label mono">Email</span>
				<a href="mailto:info@electronicsindia.co.in" style="color:#fff;text-decoration:none;">info@electronicsindia.co.in</a>
			</div>
			<div class="footer-contact-item">
				<span class="footer-contact-label mono">WhatsApp</span>
				<a href="https://wa.me/919056777299?text=Hi%20I%20want%20to%20know%20more%20about%20the%20Products.%20Ref:%20http://www.10k.agency/" style="color:#fff;text-decoration:none;">+91 90567 77299</a>
			</div>
			<div class="footer-contact-item">
				<span class="footer-contact-label mono">Address</span>
				<span style="color:rgba(255,255,255,0.7);font-family:'IBM Plex Mono',ui-monospace,monospace;font-size:0.65rem;letter-spacing:0.1em;">111, Industrial Area, Phase -2<br>Panchkula-134113<br>Haryana, India</span>
			</div>
		</div>
	</div>
	<div class="footer-bottom">
		<div class="footer-bottom-inner">
			<span style="font-family:'IBM Plex Mono',ui-monospace,monospace;font-size:0.65rem;color:rgba(255,255,255,0.4);">
				© <?php echo esc_html( gmdate( 'Y' ) ); ?> 10K Agency. All rights reserved.
			</span>
			<span style="font-family:'IBM Plex Mono',ui-monospace,monospace;font-size:0.65rem;color:rgba(255,255,255,0.4);">
				SET IN ANTON · ARCHIVO · INTER · IBM PLEX
			</span>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
