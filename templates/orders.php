<?php
/**
 * Orders workflow template.
 *
 * @package APGI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$is_edit = ( $editing_order && isset( $editing_order->id ) );
?>
<div class="apgi-orders-wrap">
	<h2><?php esc_html_e( 'Place Orders', 'wer_pk' ); ?></h2>

	<?php if ( 'order_added' === $status ) : ?>
		<p class="apgi-notice apgi-success"><?php esc_html_e( 'Order placed successfully.', 'wer_pk' ); ?></p>
	<?php elseif ( 'order_updated' === $status ) : ?>
		<p class="apgi-notice apgi-success"><?php esc_html_e( 'Order updated successfully.', 'wer_pk' ); ?></p>
	<?php elseif ( 'order_deleted' === $status ) : ?>
		<p class="apgi-notice apgi-success"><?php esc_html_e( 'Order deleted successfully.', 'wer_pk' ); ?></p>
	<?php elseif ( 'failed' === $status || 'invalid' === $status ) : ?>
		<p class="apgi-notice apgi-error"><?php esc_html_e( 'Unable to place order. Check quantity and product stock.', 'wer_pk' ); ?></p>
	<?php endif; ?>

	<form method="post" class="apgi-form">
		<h3><?php echo esc_html( $is_edit ? __( 'Edit Order', 'wer_pk' ) : __( 'Create New Order', 'wer_pk' ) ); ?></h3>
		<input type="hidden" name="apgi_order_action" value="<?php echo esc_attr( $is_edit ? 'update' : 'create' ); ?>" />
		<input type="hidden" name="order_id" value="<?php echo esc_attr( $is_edit ? (string) $editing_order->id : '0' ); ?>" />
		<p>
			<label for="apgi-product-id"><?php esc_html_e( 'Product', 'wer_pk' ); ?></label>
			<select id="apgi-product-id" name="product_id" required>
				<option value=""><?php esc_html_e( 'Select a product', 'wer_pk' ); ?></option>
				<?php foreach ( $product_options as $option ) : ?>
					<option value="<?php echo esc_attr( (string) $option->id ); ?>" <?php selected( $is_edit ? (int) $editing_order->product_id : 0, (int) $option->id ); ?>>
						<?php echo esc_html( sprintf( '%s (Stock: %d)', $option->materialsName, (int) $option->stockQty ) ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="apgi-order-qty"><?php esc_html_e( 'Order Quantity', 'wer_pk' ); ?></label>
			<input id="apgi-order-qty" name="quantity" type="number" min="1" step="1" required value="<?php echo esc_attr( $is_edit ? (string) $editing_order->quantity : '1' ); ?>" />
		</p>
		<p>
			<label for="apgi-order-note"><?php esc_html_e( 'Order Note', 'wer_pk' ); ?></label>
			<textarea id="apgi-order-note" name="order_note" rows="3"><?php echo esc_textarea( $is_edit ? (string) $editing_order->note : '' ); ?></textarea>
		</p>
		<?php wp_nonce_field( 'apgi_place_order_action', 'apgi_place_order_nonce' ); ?>
		<p>
			<button type="submit" name="apgi_place_order_submit" value="1"><?php echo esc_html( $is_edit ? __( 'Update Order', 'wer_pk' ) : __( 'Place Order', 'wer_pk' ) ); ?></button>
			<?php if ( $is_edit ) : ?>
				<a class="button" href="<?php echo esc_url( home_url( '/orders/' ) ); ?>"><?php esc_html_e( 'Cancel', 'wer_pk' ); ?></a>
			<?php endif; ?>
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
					<th><?php esc_html_e( 'Actions', 'wer_pk' ); ?></th>
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
					<td>
						<a class="button" href="<?php echo esc_url( add_query_arg( 'apgi_edit_order', (int) $order->id, home_url( '/orders/' ) ) ); ?>"><?php esc_html_e( 'Edit', 'wer_pk' ); ?></a>
						<form method="post" class="apgi-inline-form" onsubmit="return confirm('<?php echo esc_js( __( 'Delete this order?', 'wer_pk' ) ); ?>');">
							<input type="hidden" name="order_id" value="<?php echo esc_attr( (string) $order->id ); ?>" />
							<?php wp_nonce_field( 'apgi_delete_order_action', 'apgi_delete_order_nonce' ); ?>
							<button type="submit" name="apgi_delete_order_submit" value="1" class="button button-link-delete"><?php esc_html_e( 'Delete', 'wer_pk' ); ?></button>
						</form>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
</div>
