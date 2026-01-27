<form method="post" action="">
    <?php wp_nonce_field('custom_registration_nonce_action', 'custom_registration_nonce'); ?>
    <label for="username"><?php echo __("Username", "wer_pk"); ?></label>
    <input type="text" name="username" required>

    <label for="email"><?php echo __("Email", "wer_pk"); ?></label>
    <input type="email" name="email" required>

    <label for="password"><?php echo __("Password", "wer_pk"); ?></label>
    <input type="password" name="password" required>

    <input type="submit" name="submit" value="<?php echo __("Register", "wer_pk"); ?>">
</form>