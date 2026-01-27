<div class="frontend-login-form">
        <h2><?php echo __("Login", "wer_pk"); ?></h2>
        <form method="post" action="">
            <?php wp_nonce_field('custom_login_nonce_action', 'custom_login_nonce'); ?>
            <p>
                <label for="user_login"><?php echo __("Username or Email Address", "wer_pk"); ?></label>
                <input type="text" name="log" id="user_login" class="input" value="" size="20" required>
            </p>
            <p>
                <label for="user_pass"><?php echo __("Password", "wer_pk"); ?></label>
                <input type="password" name="pwd" id="user_pass" class="input" value="" size="20" required>
            </p>
            <p>
                <input type="submit" value="Log In" class="button button-primary" name="submit">
            </p>
        </form>
        <p>
            <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php echo __("Forgot Password?", "wer_pk"); ?></a> | <a href="registration/"><?php echo __("Register", "wer_pk"); ?></a>
        </p>
    </div>