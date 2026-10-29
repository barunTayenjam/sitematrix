<?php
/**
 * Contact form partial — no CF7 dependency. Native HTML form POSTing to
 * /wp-json/ei/v1/contact via fetch, live region status message.
 *
 * @package Ei
 */
?>
<form id="ei-contact-form" action="<?php echo esc_url( rest_url( 'ei/v1/contact' ) ); ?>" class="ei-form" novalidate>
	<div class="ei-form-row ei-form-row--2">
		<div class="ei-field">
			<label for="ei-contact-name"><?php esc_html_e( 'Full name', 'ei' ); ?> <span class="required">*</span></label>
			<input type="text" id="ei-contact-name" name="name" required autocomplete="name">
		</div>
		<div class="ei-field">
			<label for="ei-contact-email"><?php esc_html_e( 'Work email', 'ei' ); ?> <span class="required">*</span></label>
			<input type="email" id="ei-contact-email" name="email" required autocomplete="email">
		</div>
	</div>
	<div class="ei-form-row">
		<div class="ei-field">
			<label for="ei-contact-phone"><?php esc_html_e( 'Phone', 'ei' ); ?></label>
			<input type="tel" id="ei-contact-phone" name="phone" autocomplete="tel">
		</div>
	</div>
	<div class="ei-form-row">
		<div class="ei-field">
			<label for="ei-contact-subject"><?php esc_html_e( 'Subject', 'ei' ); ?></label>
			<input type="text" id="ei-contact-subject" name="subject" autocomplete="off">
		</div>
	</div>
	<div class="ei-form-row">
		<div class="ei-field">
			<label for="ei-contact-message"><?php esc_html_e( 'Message', 'ei' ); ?> <span class="required">*</span></label>
			<textarea id="ei-contact-message" name="message" rows="5" required autocomplete="off"></textarea>
		</div>
	</div>
	<button type="submit" class="btn btn--primary"><?php esc_html_e( 'Send Message', 'ei' ); ?></button>
	<div class="ei-form-status"></div>
</form>
