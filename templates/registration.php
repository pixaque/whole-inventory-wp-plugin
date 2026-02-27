<div class="wer-pk-auth container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <h2 class="h4 mb-4 text-center"><?php echo __("Register", "wer_pk"); ?></h2>
                    <form method="post" action="" class="d-grid gap-3">
                        <?php wp_nonce_field('custom_registration_nonce_action', 'custom_registration_nonce'); ?>
                        <div>
                            <label for="username" class="form-label"><?php echo __("Username", "wer_pk"); ?></label>
                            <input id="username" type="text" name="username" class="form-control" required>
                        </div>
                        <div>
                            <label for="email" class="form-label"><?php echo __("Email", "wer_pk"); ?></label>
                            <input id="email" type="email" name="email" class="form-control" required>
                        </div>
                        <div>
                            <label for="password" class="form-label"><?php echo __("Password", "wer_pk"); ?></label>
                            <input id="password" type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" name="wer_pk_registration_submit" class="btn btn-primary w-100"><?php echo __("Register", "wer_pk"); ?></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
