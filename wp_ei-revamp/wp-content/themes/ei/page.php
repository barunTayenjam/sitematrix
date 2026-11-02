<?php
/**
 * Generic page template — 10K Agency.
 * About, Quality, R&D, Clients, Contact.
 *
 * @package Ei
 */

get_header(); ?>

<?php
while ( have_posts() ) :
	the_post();
	$slug = get_post_field( 'post_name', get_the_ID() );
	?>
	<section class="sheet page-sheet">
		<div class="container page-inner">
			<span class="mono-micro" style="display:block;margin-bottom:16px;"><?php echo esc_html( strtoupper( $slug ) ); ?></span>
			<h1 class="display-l"><?php echo esc_html( get_the_title() ); ?></h1>
			<div class="page-content">
				<?php the_content(); ?>
			</div>

			<?php if ( 'contact' === $slug ) : ?>
				<hr>
				<h2 class="display-m" style="margin-bottom:20px;"><?php esc_html_e( 'Request quotation / technical enquiry', 'ei' ); ?></h2>
				<div class="contact-panel reveal">
					<?php
					$cf7_form = get_posts(
						array(
							'post_type'      => 'wpcf7_contact_form',
							'posts_per_page' => 1,
							'post_status'    => 'publish',
						)
					);
					if ( ! empty( $cf7_form ) ) {
						echo do_shortcode( sprintf( '[contact-form-7 id="%d"]', (int) $cf7_form[0]->ID ) );
					} else {
						?>
						<form method="post" action="" class="contact-fallback-form">
							<div class="form-field">
								<label for="contact_name">Name *</label>
								<input type="text" id="contact_name" name="name" required>
							</div>
							<div class="form-field">
								<label for="contact_email">Email *</label>
								<input type="email" id="contact_email" name="email" required>
							</div>
							<div class="form-field">
								<label for="contact_phone">Phone</label>
								<input type="tel" id="contact_phone" name="phone">
							</div>
							<div class="form-field">
								<label for="contact_message">Message *</label>
								<textarea id="contact_message" name="message" rows="5" required></textarea>
							</div>
							<button type="submit" class="btn btn-solid">Send message</button>
						</form>
					<?php } ?>
				</div>
			<?php endif; ?>
		</div>
	</section>
<?php endwhile; ?>

<?php get_footer(); ?>
