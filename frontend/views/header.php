<?php
$dashicons_path = ABSPATH . 'wp-includes/css/dashicons.css';
$dashicons_css  = file_exists( $dashicons_path ) ? file_get_contents( $dashicons_path ) : '';

$current_page_id = get_queried_object_id();
$nav_products_label = __( 'Products', 'wer_pk' );
$nav_orders_label   = __( 'Orders', 'wer_pk' );
$nav_logout_label   = __( 'Log Out', 'wer_pk' );

if ( function_exists( 'rwmb_get_value' ) && $current_page_id ) {
	$products_meta = rwmb_get_value( 'wer_pk_nav_products_label', array(), $current_page_id );
	$orders_meta   = rwmb_get_value( 'wer_pk_nav_orders_label', array(), $current_page_id );
	$logout_meta   = rwmb_get_value( 'wer_pk_nav_logout_label', array(), $current_page_id );

	if ( ! empty( $products_meta ) ) {
		$nav_products_label = $products_meta;
	}
	if ( ! empty( $orders_meta ) ) {
		$nav_orders_label = $orders_meta;
	}
	if ( ! empty( $logout_meta ) ) {
		$nav_logout_label = $logout_meta;
	}
}
?>

<style>
<?php echo $dashicons_css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</style>

<div class="user-navigation">
	<ul>
		<li class='firstList'>
			<span class="dashicons-before dashicons-admin-users">
			<?php
				$user_info = wp_get_current_user();
				echo esc_html( $user_info->display_name );
			?>
			</span>
		</li>
		<li><a href="../seller-account/"><?php echo esc_html( $nav_products_label ); ?></a></li>
		<li><a href="../orders?orderStatus=1"><?php echo esc_html( $nav_orders_label ); ?></a></li>
		<li>
			<a href="<?php echo esc_url( wp_logout_url( site_url( 'login' ) ) ); ?>">
				<span class="btn__text"><?php echo esc_html( $nav_logout_label ); ?></span>
			</a>
		</li>
	</ul>
</div>
