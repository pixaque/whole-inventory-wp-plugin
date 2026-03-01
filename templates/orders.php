<?php
/**
 * Orders workflow template.
 *
 * @package APGI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="apgi-orders-wrap">
	<h2><?php esc_html_e( 'Place Orders', 'wer_pk' ); ?></h2>

	<?php if ( 'order_added' === $status ) : ?>
		<p class="apgi-notice apgi-success"><?php esc_html_e( 'Order placed successfully.', 'wer_pk' ); ?></p>
	<?php elseif ( 'failed' === $status || 'invalid' === $status ) : ?>
		<p class="apgi-notice apgi-error"><?php esc_html_e( 'Unable to place order. Check quantity and product stock.', 'wer_pk' ); ?></p>
	<?php endif; ?>

	<form method="post" class="apgi-form">
		<h3><?php esc_html_e( 'Create New Order', 'wer_pk' ); ?></h3>
		<p>
			<label for="apgi-product-id"><?php esc_html_e( 'Product', 'wer_pk' ); ?></label>
			<select id="apgi-product-id" name="product_id" required>
				<option value=""><?php esc_html_e( 'Select a product', 'wer_pk' ); ?></option>
				<?php foreach ( $product_options as $option ) : ?>
					<option value="<?php echo esc_attr( (string) $option->id ); ?>">
						<?php echo esc_html( sprintf( '%s (Stock: %d)', $option->materialsName, (int) $option->stockQty ) ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="apgi-order-qty"><?php esc_html_e( 'Order Quantity', 'wer_pk' ); ?></label>
			<input id="apgi-order-qty" name="quantity" type="number" min="1" step="1" required />
		</p>
		<p>
			<label for="apgi-order-note"><?php esc_html_e( 'Order Note', 'wer_pk' ); ?></label>
			<textarea id="apgi-order-note" name="order_note" rows="3"></textarea>
		</p>
		<?php wp_nonce_field( 'apgi_place_order_action', 'apgi_place_order_nonce' ); ?>
		<p>
			<button type="submit" name="apgi_place_order_submit" value="1"><?php esc_html_e( 'Place Order', 'wer_pk' ); ?></button>
		</p>
	</form>

	<h3><?php esc_html_e( 'Recent Orders', 'wer_pk' ); ?></h3>
	<?php if ( empty( $orders ) ) : ?>
		<p><?php esc_html_e( 'No orders found.', 'wer_pk' ); ?></p>
	<?php else : ?>
		<table class="apgi-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Order #', 'wer_pk' ); ?></th>
					<th><?php esc_html_e( 'Product', 'wer_pk' ); ?></th>
					<th><?php esc_html_e( 'Quantity', 'wer_pk' ); ?></th>
					<th><?php esc_html_e( 'Note', 'wer_pk' ); ?></th>
					<th><?php esc_html_e( 'Date', 'wer_pk' ); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ( $orders as $order ) : ?>
				<tr>
					<td><?php echo esc_html( (string) $order->id ); ?></td>
					<td><?php echo esc_html( (string) $order->materialsName ); ?></td>
					<td><?php echo esc_html( (string) $order->quantity ); ?></td>
					<td><?php echo esc_html( (string) $order->note ); ?></td>
					<td><?php echo esc_html( (string) $order->created_at ); ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
</div>
