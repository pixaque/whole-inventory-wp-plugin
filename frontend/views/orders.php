<?php include_once( 'header.php' ); ?>
<?php
$current_page_id = get_queried_object_id();

$labels = array(
	'confirmed_tab'      => __( 'Confirmed', 'wer_pk' ),
	'pending_tab'        => __( 'Pending', 'wer_pk' ),
	'processed_tab'      => __( 'Processed', 'wer_pk' ),
	'all_tab'            => __( 'All', 'wer_pk' ),
	'reference_bill'     => __( 'Reference Bill #:', 'wer_pk' ),
	'bill_date'          => __( 'Bill Date:', 'wer_pk' ),
	'processed_success'  => __( 'Processed Successfully', 'wer_pk' ),
	'confirmed_status'   => __( 'Confirmed', 'wer_pk' ),
	'description'        => __( 'Description:', 'wer_pk' ),
	'col_id'             => __( 'Id', 'wer_pk' ),
	'col_bill_id'        => __( 'Bill Id', 'wer_pk' ),
	'col_product_id'     => __( 'Product Id', 'wer_pk' ),
	'col_material_name'  => __( 'Material Name', 'wer_pk' ),
	'col_quantity'       => __( 'Quantity', 'wer_pk' ),
	'col_gst'            => __( 'GST', 'wer_pk' ),
	'col_order_price'    => __( 'Order Price', 'wer_pk' ),
	'col_discount'       => __( 'Discount %', 'wer_pk' ),
	'process_action'     => __( 'Processed', 'wer_pk' ),
	'total_order_price'  => __( 'Total Order Price:', 'wer_pk' ),
	'empty_state'        => __( 'No orders Found. Please come back later.', 'wer_pk' ),
);

if ( function_exists( 'rwmb_get_value' ) && $current_page_id ) {
	$map = array(
		'wer_pk_orders_confirmed_tab_label' => 'confirmed_tab',
		'wer_pk_orders_pending_tab_label' => 'pending_tab',
		'wer_pk_orders_processed_tab_label' => 'processed_tab',
		'wer_pk_orders_all_tab_label' => 'all_tab',
		'wer_pk_orders_reference_bill_label' => 'reference_bill',
		'wer_pk_orders_bill_date_label' => 'bill_date',
		'wer_pk_orders_processed_success_label' => 'processed_success',
		'wer_pk_orders_confirmed_status_label' => 'confirmed_status',
		'wer_pk_orders_description_label' => 'description',
		'wer_pk_orders_col_id_label' => 'col_id',
		'wer_pk_orders_col_bill_id_label' => 'col_bill_id',
		'wer_pk_orders_col_product_id_label' => 'col_product_id',
		'wer_pk_orders_col_material_name_label' => 'col_material_name',
		'wer_pk_orders_col_quantity_label' => 'col_quantity',
		'wer_pk_orders_col_gst_label' => 'col_gst',
		'wer_pk_orders_col_order_price_label' => 'col_order_price',
		'wer_pk_orders_col_discount_label' => 'col_discount',
		'wer_pk_orders_process_action_label' => 'process_action',
		'wer_pk_orders_total_order_price_label' => 'total_order_price',
		'wer_pk_orders_empty_state_label' => 'empty_state',
	);

	foreach ( $map as $meta_key => $label_key ) {
		$meta_value = rwmb_get_value( $meta_key, array(), $current_page_id );
		if ( ! empty( $meta_value ) ) {
			$labels[ $label_key ] = $meta_value;
		}
	}
}

$order_status = isset( $_REQUEST['orderStatus'] ) ? absint( $_REQUEST['orderStatus'] ) : 1;
?>

<div class="user-sub-navigation">
	<ul>
		<li><a href="../orders?orderStatus=1" class=<?php echo 1 === $order_status ? '"selected"' : '""'; ?>><?php echo esc_html( $labels['confirmed_tab'] ); ?> <span class="confirmed-orders" id="assignment-count"><?php echo esc_html( count( $ordersConfirmed ) ); ?></span></a></li>
		<li><a href="../orders?orderStatus=2" class=<?php echo 2 === $order_status ? '"selected"' : '""'; ?>><?php echo esc_html( $labels['pending_tab'] ); ?></a></li>
		<li><a href="../orders?orderStatus=3" class=<?php echo 3 === $order_status ? '"selected"' : '""'; ?>><?php echo esc_html( $labels['processed_tab'] ); ?></a></li>
		<li><a href="../orders?orderStatus=4" class=<?php echo 4 === $order_status ? '"selected"' : '""'; ?>><?php echo esc_html( $labels['all_tab'] ); ?></a></li>
	</ul>
</div>

<?php if ( count( $results ) > 0 ) : ?>
<div class="wrap" style="max-width: 100%;">
	<div id="wp_wer_pk_products_table">
		<?php
		$groupedResults = array();
		foreach ( $results as $result ) {
			$groupedResults[ $result->billNo ][] = $result;
		}

		foreach ( $groupedResults as $billNo => $billOrders ) :
			$totalOrderPrice = 0;
			?>
			<table width="100%" cellpadding="5" cellspacing="3" border='0'>
				<tr>
					<td width="45%">
						<p><small><?php echo esc_html( $labels['reference_bill'] ); ?> <strong><?php echo esc_html( $billNo ); ?></strong></small></p>
					</td>
					<td width="10%"></td>
					<td width="45%">
						<p>
							<small><?php echo esc_html( $labels['bill_date'] ); ?> <?php echo esc_html( $billOrders[0]->billdate ); ?></small><br>
							<?php if ( $billOrders[0]->confirmed ) : ?>
								<small style="color: white; padding: 5px 10px; background: green; border-radius: 5px;"><?php echo esc_html( 1 === (int) $billOrders[0]->processed ? $labels['processed_success'] : $labels['confirmed_status'] ); ?></small>
							<?php endif; ?>
						</p>
					</td>
				</tr>
				<tr>
					<td colspan="3">
						<p><small><?php echo esc_html( $labels['description'] ); ?> <?php echo esc_html( $billOrders[0]->description ); ?></small></p>
					</td>
				</tr>
			</table>

			<table width="100%" cellpadding="5" cellspacing="3" border='0' class="sellerTable">
				<tr>
					<th><strong><?php echo esc_html( $labels['col_id'] ); ?></strong></th>
					<th><strong><?php echo esc_html( $labels['col_bill_id'] ); ?></strong></th>
					<th><strong><?php echo esc_html( $labels['col_product_id'] ); ?></strong></th>
					<th><strong><?php echo esc_html( $labels['col_material_name'] ); ?></strong></th>
					<th><strong><?php echo esc_html( $labels['col_quantity'] ); ?></strong></th>
					<th><strong><?php echo esc_html( $labels['col_gst'] ); ?></strong></th>
					<th><strong><?php echo esc_html( $labels['col_order_price'] ); ?></strong></th>
					<th><strong><?php echo esc_html( $labels['col_discount'] ); ?></strong></th>
					<?php echo 1 !== (int) $billOrders[0]->processed && 1 === $order_status ? '<th></th>' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</tr>

				<?php foreach ( $billOrders as $order ) : ?>
					<?php $totalOrderPrice += $order->totalPrice; ?>
					<tr>
						<td><?php echo esc_html( $order->order_id ); ?></td>
						<td><?php echo esc_html( $order->billid ); ?></td>
						<td><?php echo esc_html( $order->productid ); ?></td>
						<td><?php echo esc_html( $order->materialsName ); ?></td>
						<td><?php echo esc_html( $order->quantity ); ?></td>
						<td><span class="dashicons-before <?php echo esc_attr( Settings::set_currency_symbol() ); ?>"></span>: <?php echo esc_html( $order->GST ); ?></td>
						<td><span class="dashicons-before <?php echo esc_attr( Settings::set_currency_symbol() ); ?>"></span>: <?php echo esc_html( $order->totalPrice ); ?></td>
						<td><?php echo esc_html( $order->discount ); ?></td>
						<?php if ( 1 !== (int) $order->processed && 1 === $order_status ) : ?>
							<td>
								<a href="javascript:void(0);" onClick="wer_pkProcessOrder(<?php echo esc_attr( $order->order_id ); ?>, true);"><?php echo esc_html( $labels['process_action'] ); ?></a>
							</td>
						<?php endif; ?>
					</tr>
				<?php endforeach; ?>

				<tr>
					<td colspan="6" style="text-align: right;"><strong><?php echo esc_html( $labels['total_order_price'] ); ?></strong></td>
					<td colspan="2"><strong><span class="dashicons-before <?php echo esc_attr( Settings::set_currency_symbol() ); ?>"></span>: <?php echo esc_html( number_format( $totalOrderPrice, 2 ) ); ?></strong></td>
				</tr>

			</table>

		<?php endforeach; ?>
	</div>
</div>
<?php else : ?>
	<strong><?php echo esc_html( $labels['empty_state'] ); ?></strong>
<?php endif; ?>
