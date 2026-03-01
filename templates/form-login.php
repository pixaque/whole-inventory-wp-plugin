<?php
$current_page_id = get_queried_object_id();

$login_heading = __( 'Login', 'wer_pk' );
$login_username_label = __( 'Username or Email Address', 'wer_pk' );
$login_password_label = __( 'Password', 'wer_pk' );
$login_button_text = __( 'Log In', 'wer_pk' );
$forgot_password_label = __( 'Forgot Password?', 'wer_pk' );
$register_link_label = __( 'Register', 'wer_pk' );
$show_register_link = true;
$register_url = home_url( '/registration/' );

if ( function_exists( 'rwmb_get_value' ) && $current_page_id ) {
	$login_heading_meta = rwmb_get_value( 'wer_pk_login_heading', array(), $current_page_id );
	$login_username_label_meta = rwmb_get_value( 'wer_pk_login_username_label', array(), $current_page_id );
	$login_password_label_meta = rwmb_get_value( 'wer_pk_login_password_label', array(), $current_page_id );
	$login_button_text_meta = rwmb_get_value( 'wer_pk_login_button_text', array(), $current_page_id );
	$forgot_password_label_meta = rwmb_get_value( 'wer_pk_login_forgot_password_label', array(), $current_page_id );
	$register_link_label_meta = rwmb_get_value( 'wer_pk_login_register_label', array(), $current_page_id );
	$show_register_link_meta = rwmb_get_value( 'wer_pk_login_show_register_link', array(), $current_page_id );
	$register_url_meta = rwmb_get_value( 'wer_pk_login_register_url', array(), $current_page_id );

	if ( ! empty( $login_heading_meta ) ) {
		$login_heading = $login_heading_meta;
	}

	if ( ! empty( $login_username_label_meta ) ) {
		$login_username_label = $login_username_label_meta;
	}

	if ( ! empty( $login_password_label_meta ) ) {
		$login_password_label = $login_password_label_meta;
	}

	if ( ! empty( $login_button_text_meta ) ) {
		$login_button_text = $login_button_text_meta;
	}

	if ( ! empty( $forgot_password_label_meta ) ) {
		$forgot_password_label = $forgot_password_label_meta;
	}

	if ( ! empty( $register_link_label_meta ) ) {
		$register_link_label = $register_link_label_meta;
	}

	if ( '' !== $show_register_link_meta && null !== $show_register_link_meta ) {
		$show_register_link = (bool) $show_register_link_meta;
	}

	if ( ! empty( $register_url_meta ) ) {
		$register_url = $register_url_meta;
	}
}
?>
<div class="frontend-login-form">
	<h2><?php echo esc_html( $login_heading ); ?></h2>
	<form method="post" action="">
		<?php wp_nonce_field( 'custom_login_nonce_action', 'custom_login_nonce' ); ?>
		<p>
			<label for="user_login"><?php echo esc_html( $login_username_label ); ?></label>
			<input type="text" name="log" id="user_login" class="input" value="" size="20" required>
		</p>
		<p>
			<label for="user_pass"><?php echo esc_html( $login_password_label ); ?></label>
			<input type="password" name="pwd" id="user_pass" class="input" value="" size="20" required>
		</p>
		<p>
			<input type="submit" value="<?php echo esc_attr( $login_button_text ); ?>" class="button button-primary" name="wer_pk_login_submit">
		</p>
	</form>
	<p>
		<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php echo esc_html( $forgot_password_label ); ?></a>
		<?php if ( $show_register_link ) : ?>
			| <a href="<?php echo esc_url( $register_url ); ?>"><?php echo esc_html( $register_link_label ); ?></a>
		<?php endif; ?>
	</p>
</div>
