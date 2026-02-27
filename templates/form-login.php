<div class="wer-pk-auth container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <h2 class="h4 mb-4 text-center"><?php echo __("Login", "wer_pk"); ?></h2>
                    <form method="post" action="" class="d-grid gap-3">
                        <?php wp_nonce_field('custom_login_nonce_action', 'custom_login_nonce'); ?>
                        <div>
                            <label for="user_login" class="form-label"><?php echo __("Username or Email Address", "wer_pk"); ?></label>
                            <input type="text" name="log" id="user_login" class="form-control" value="" required>
                        </div>
                        <div>
                            <label for="user_pass" class="form-label"><?php echo __("Password", "wer_pk"); ?></label>
                            <input type="password" name="pwd" id="user_pass" class="form-control" value="" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100" name="wer_pk_login_submit">Log In</button>
                    </form>
                    <p class="mt-3 mb-0 text-center small">
                        <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php echo __("Forgot Password?", "wer_pk"); ?></a>
                        &nbsp;|&nbsp;
                        <a href="registration/"><?php echo __("Register", "wer_pk"); ?></a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
