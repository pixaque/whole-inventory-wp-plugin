<?php
/**
 * Seller account inventory template.
 *
 * @package APGI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="apgi-products-wrap">
	<h2><?php echo esc_html( APGI\Settings::get( 'brand_name', __( 'Inventory', 'wer_pk' ) ) ); ?></h2>

	<?php if ( 'inventory_added' === $status ) : ?>
		<p class="apgi-notice apgi-success"><?php esc_html_e( 'Inventory item added successfully.', 'wer_pk' ); ?></p>
	<?php elseif ( 'failed' === $status || 'invalid' === $status ) : ?>
		<p class="apgi-notice apgi-error"><?php esc_html_e( 'Unable to save inventory item. Please verify your input.', 'wer_pk' ); ?></p>
	<?php endif; ?>

	<form method="post" class="apgi-form">
		<h3><?php esc_html_e( 'Add Inventory Item', 'wer_pk' ); ?></h3>
		<p>
			<label for="apgi-product-name"><?php esc_html_e( 'Product Name', 'wer_pk' ); ?></label>
			<input id="apgi-product-name" name="product_name" type="text" maxlength="200" required />
		</p>
		<p>
			<label for="apgi-store-id"><?php esc_html_e( 'Store ID', 'wer_pk' ); ?></label>
			<input id="apgi-store-id" name="store_id" type="number" min="0" step="1" required />
		</p>
		<p>
			<label for="apgi-stock-qty"><?php esc_html_e( 'Stock Quantity', 'wer_pk' ); ?></label>
			<input id="apgi-stock-qty" name="stock_qty" type="number" min="0" step="1" required />
		</p>
		<p>
			<label for="apgi-unit-price"><?php esc_html_e( 'Unit Price', 'wer_pk' ); ?></label>
			<input id="apgi-unit-price" name="unit_price" type="number" min="0" step="0.01" required />
		</p>
		<?php wp_nonce_field( 'apgi_add_inventory_action', 'apgi_add_inventory_nonce' ); ?>
		<p>
			<button type="submit" name="apgi_add_inventory_submit" value="1"><?php esc_html_e( 'Add Inventory', 'wer_pk' ); ?></button>
		</p>
	</form>

	<h3><?php esc_html_e( 'Current Inventory', 'wer_pk' ); ?></h3>
	<?php if ( empty( $products ) ) : ?>
		<p><?php esc_html_e( 'No inventory found.', 'wer_pk' ); ?></p>
	<?php else : ?>
		<table class="apgi-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'ID', 'wer_pk' ); ?></th>
					<th><?php esc_html_e( 'Product Name', 'wer_pk' ); ?></th>
					<th><?php esc_html_e( 'Store ID', 'wer_pk' ); ?></th>
					<th><?php esc_html_e( 'Stock', 'wer_pk' ); ?></th>
					<th><?php esc_html_e( 'Unit Price', 'wer_pk' ); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ( $products as $product ) : ?>
				<tr>
					<td><?php echo esc_html( (string) $product->id ); ?></td>
					<td><?php echo esc_html( (string) $product->materialsName ); ?></td>
					<td><?php echo esc_html( (string) $product->storeId ); ?></td>
					<td><?php echo esc_html( (string) $product->stockQty ); ?></td>
					<td><?php echo esc_html( $symbol . number_format_i18n( (float) $product->unitPrice, 2 ) ); ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
</div>
