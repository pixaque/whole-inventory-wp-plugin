<?php
/**
 * Frontend registration template.
 *
 * @package APGI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<form method="post" class="apgi-form apgi-registration-form">
	<p>
		<label for="apgi-username"><?php esc_html_e( 'Username', 'wer_pk' ); ?></label>
		<input id="apgi-username" name="username" type="text" required />
	</p>
	<p>
		<label for="apgi-email"><?php esc_html_e( 'Email', 'wer_pk' ); ?></label>
		<input id="apgi-email" name="email" type="email" required />
	</p>
	<p>
		<label for="apgi-password"><?php esc_html_e( 'Password', 'wer_pk' ); ?></label>
		<input id="apgi-password" name="password" type="password" required />
	</p>
	<?php wp_nonce_field( 'apgi_registration_action', 'apgi_registration_nonce' ); ?>
	<p>
		<button type="submit" name="apgi_registration_submit" value="1"><?php esc_html_e( 'Register', 'wer_pk' ); ?></button>
	</p>
</form>
