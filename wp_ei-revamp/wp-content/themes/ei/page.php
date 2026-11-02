<?php
/**
 * Generic page — 10K Agency Technical Luxury.
 * About, Quality, R&D, Clients, Contact.
 *
 * @package Ei
 */

get_header(); ?>

<?php
while ( have_posts() ) :
	the_post();
	?>

<section class="sheet page-sheet reveal" id="page-<?php echo esc_attr( get_the_ID() ); ?>">
	<div class="container">
		<div class="page-body">
			<?php the_content(); ?>

			<?php if ( 'contact' === get_post_field( 'post_name', get_the_ID() ) ) : ?>
				<hr>
				<h2><?php esc_html_e( 'Leave us a message', 'ei' ); ?></h2>
				<form class="contact-form" id="ei-contact-form">
					<div class="field">
						<label for="name">Name *</label>
						<input type="text" name="name" id="name" required>
					</div>
					<div class="field">
						<label for="email">Email *</label>
						<input type="email" name="email" id="email" required>
					</div>
					<div class="field">
						<label for="phone">Phone</label>
						<input type="tel" name="phone" id="phone">
					</div>
					<div class="field">
						<label for="subject">Subject</label>
						<input type="text" name="subject" id="subject">
					</div>
					<div class="field">
						<label for="message">Message *</label>
						<textarea name="message" id="message" rows="5" required></textarea>
					</div>
					<button type="submit" class="btn btn-primary">Send Message</button>
					<div class="form-status success"></div>
					<div class="form-status error"></div>
				</form>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php endwhile; ?>
