<?php 
$dashicons_path = ABSPATH . 'wp-includes/css/dashicons.css';
$dashicons_css = file_exists($dashicons_path) ? file_get_contents($dashicons_path) : '';
?>

<style>
<?php echo $dashicons_css; ?>
</style>

<div class="user-navigation">

	<ul>
		<li class='firstList'>
			<span class="dashicons-before dashicons-admin-users">
			<?php 
				$user_info = wp_get_current_user();
				echo $user_info->display_name;
			?>
			</span>
		</li>
		<li><a href="../seller-account/"><?php echo __('Products', 'wer_pk') ?></a></li>
		<li><a href="../orders?orderStatus=1"><?php echo __('Orders', 'wer_pk') ?></a></li>
		<li>
			<a href="<?php echo wp_logout_url( site_url ( 'login' ) );  ?>">
				<span class="btn__text"><?php echo __('Log Out', 'wer_pk') ?></span>
			</a>
		</li>
	</ul>
</div>