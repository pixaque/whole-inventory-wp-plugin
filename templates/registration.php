<?php
$current_page_id = get_queried_object_id();

$registration_heading = __( 'Register', 'wer_pk' );
$username_label = __( 'Username', 'wer_pk' );
$email_label = __( 'Email', 'wer_pk' );
$password_label = __( 'Password', 'wer_pk' );
$register_button_text = __( 'Register', 'wer_pk' );
$show_login_link = true;
$login_link_label = __( 'Already have an account? Login', 'wer_pk' );
$login_url = home_url( '/login/' );

if ( function_exists( 'rwmb_get_value' ) && $current_page_id ) {
	$registration_heading_meta = rwmb_get_value( 'wer_pk_registration_heading', array(), $current_page_id );
	$username_label_meta = rwmb_get_value( 'wer_pk_registration_username_label', array(), $current_page_id );
	$email_label_meta = rwmb_get_value( 'wer_pk_registration_email_label', array(), $current_page_id );
	$password_label_meta = rwmb_get_value( 'wer_pk_registration_password_label', array(), $current_page_id );
	$register_button_text_meta = rwmb_get_value( 'wer_pk_registration_button_text', array(), $current_page_id );
	$show_login_link_meta = rwmb_get_value( 'wer_pk_registration_show_login_link', array(), $current_page_id );
	$login_link_label_meta = rwmb_get_value( 'wer_pk_registration_login_label', array(), $current_page_id );
	$login_url_meta = rwmb_get_value( 'wer_pk_registration_login_url', array(), $current_page_id );

	if ( ! empty( $registration_heading_meta ) ) {
		$registration_heading = $registration_heading_meta;
	}

	if ( ! empty( $username_label_meta ) ) {
		$username_label = $username_label_meta;
	}

	if ( ! empty( $email_label_meta ) ) {
		$email_label = $email_label_meta;
	}

	if ( ! empty( $password_label_meta ) ) {
		$password_label = $password_label_meta;
	}

	if ( ! empty( $register_button_text_meta ) ) {
		$register_button_text = $register_button_text_meta;
	}

	if ( '' !== $show_login_link_meta && null !== $show_login_link_meta ) {
		$show_login_link = (bool) $show_login_link_meta;
	}

	if ( ! empty( $login_link_label_meta ) ) {
		$login_link_label = $login_link_label_meta;
	}

	if ( ! empty( $login_url_meta ) ) {
		$login_url = $login_url_meta;
	}
}
?>
<div class="frontend-registration-form">
	<h2><?php echo esc_html( $registration_heading ); ?></h2>
	<form method="post" action="">
		<?php wp_nonce_field( 'custom_registration_nonce_action', 'custom_registration_nonce' ); ?>
		<p>
			<label for="username"><?php echo esc_html( $username_label ); ?></label>
			<input type="text" id="username" name="username" required>
		</p>
		<p>
			<label for="email"><?php echo esc_html( $email_label ); ?></label>
			<input type="email" id="email" name="email" required>
		</p>
		<p>
			<label for="password"><?php echo esc_html( $password_label ); ?></label>
			<input type="password" id="password" name="password" required>
		</p>
		<p>
			<input type="submit" name="wer_pk_registration_submit" value="<?php echo esc_attr( $register_button_text ); ?>">
		</p>
	</form>
	<?php if ( $show_login_link ) : ?>
		<p>
			<a href="<?php echo esc_url( $login_url ); ?>"><?php echo esc_html( $login_link_label ); ?></a>
		</p>
	<?php endif; ?>
</div>
