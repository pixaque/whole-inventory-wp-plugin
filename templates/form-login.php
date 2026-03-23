<?php
/**
 * Frontend login template.
 *
 * @package APGI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<form method="post" class="apgi-form apgi-login-form">
	<p>
		<label for="apgi-log"><?php esc_html_e( 'Username', 'wer_pk' ); ?></label>
		<input id="apgi-log" name="log" type="text" required />
	</p>
	<p>
		<label for="apgi-pwd"><?php esc_html_e( 'Password', 'wer_pk' ); ?></label>
		<input id="apgi-pwd" name="pwd" type="password" required />
	</p>
	<?php wp_nonce_field( 'apgi_login_action', 'apgi_login_nonce' ); ?>
	<p>
		<button type="submit" name="apgi_login_submit" value="1"><?php esc_html_e( 'Login', 'wer_pk' ); ?></button>
	</p>
</form>
