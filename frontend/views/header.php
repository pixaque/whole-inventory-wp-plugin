<?php 
$dashicons_path = ABSPATH . 'wp-includes/css/dashicons.css';
$dashicons_css = file_exists($dashicons_path) ? file_get_contents($dashicons_path) : '';
?>

<style>
<?php echo $dashicons_css; ?>
</style>

<link
	rel="stylesheet"
	href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
	integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
	crossorigin="anonymous"
/>

<style>
	.werpk-shell {
		max-width: 1140px;
		margin: 0 auto;
		padding: 1rem;
	}

	.werpk-card {
		border: 1px solid #dee2e6;
		border-radius: 0.75rem;
		box-shadow: 0 0.25rem 1rem rgba(0, 0, 0, 0.05);
	}
</style>

<div class="werpk-shell">
	<div class="user-navigation werpk-card bg-white p-3 mb-4">
		<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
			<div class="fw-semibold text-dark">
				<span class="dashicons-before dashicons-admin-users"></span>
				<?php
					$user_info = wp_get_current_user();
					echo esc_html( $user_info->display_name );
				?>
			</div>
			<ul class="nav nav-pills">
				<li class="nav-item"><a class="nav-link" href="../seller-account/"><?php echo __('Products', 'wer_pk') ?></a></li>
				<li class="nav-item"><a class="nav-link" href="../orders?orderStatus=1"><?php echo __('Orders', 'wer_pk') ?></a></li>
				<li class="nav-item">
					<a class="nav-link text-danger" href="<?php echo wp_logout_url( site_url ( 'login' ) );  ?>">
						<span class="btn__text"><?php echo __('Log Out', 'wer_pk') ?></span>
					</a>
				</li>
			</ul>
		</div>
	</div>
</div>
