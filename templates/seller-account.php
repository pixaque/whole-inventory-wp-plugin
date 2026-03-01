<?php
/**
 * Seller account products list template.
 *
 * @package APGI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="apgi-products-wrap">
	<h2><?php echo esc_html( APGI\Settings::get( 'brand_name', __( 'Inventory', 'wer_pk' ) ) ); ?></h2>
	<?php if ( empty( $products ) ) : ?>
		<p><?php esc_html_e( 'No products found.', 'wer_pk' ); ?></p>
	<?php else : ?>
		<table class="apgi-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'ID', 'wer_pk' ); ?></th>
					<th><?php esc_html_e( 'Product Name', 'wer_pk' ); ?></th>
					<th><?php esc_html_e( 'Store ID', 'wer_pk' ); ?></th>
					<th><?php esc_html_e( 'Currency', 'wer_pk' ); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ( $products as $product ) : ?>
				<tr>
					<td><?php echo esc_html( (string) $product->id ); ?></td>
					<td><?php echo esc_html( (string) $product->materialsName ); ?></td>
					<td><?php echo esc_html( (string) $product->storeId ); ?></td>
					<td><?php echo esc_html( $symbol ); ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
</div>
