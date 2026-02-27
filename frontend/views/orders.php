<?php include_once("header.php"); ?>

<div class="werpk-shell">
    <div class="user-sub-navigation card werpk-card mb-4">
        <div class="card-body py-2">
            <ul class="nav nav-pills justify-content-center gap-2">
                <li class="nav-item"><a class="nav-link <?php echo $_REQUEST["orderStatus"] == 1 ? "active" : "" ?>" href="../orders?orderStatus=1"><?php echo __('Confirmed', 'wer_pk') ?> <span class="badge bg-light text-dark ms-1" id="assignment-count"><?php echo count($ordersConfirmed); ?></span></a></li>
                <li class="nav-item"><a class="nav-link <?php echo $_REQUEST["orderStatus"] == 2 ? "active" : "" ?>" href="../orders?orderStatus=2"><?php echo __('Pending', 'wer_pk') ?></a></li>
                <li class="nav-item"><a class="nav-link <?php echo $_REQUEST["orderStatus"] == 3 ? "active" : "" ?>" href="../orders?orderStatus=3"><?php echo __('Processed', 'wer_pk') ?></a></li>
                <li class="nav-item"><a class="nav-link <?php echo $_REQUEST["orderStatus"] == 4 ? "active" : "" ?>" href="../orders?orderStatus=4"><?php echo __('All', 'wer_pk') ?></a></li>
            </ul>
        </div>
    </div>

<?php if (count($results) > 0): ?>
    <div id="wp_wer_pk_products_table">
        <?php
        $groupedResults = [];
        foreach ($results as $result) {
            $groupedResults[$result->billNo][] = $result;
        }

        foreach ($groupedResults as $billNo => $billOrders):
            $totalOrderPrice = 0;
        ?>
            <div class="card werpk-card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between flex-wrap gap-2">
                        <p class="mb-0"><small><?php echo __('Reference Bill #:', 'wer_pk') ?> <strong><?php echo $billNo ?></strong></small></p>
                        <p class="mb-0 text-end">
                            <small><?php echo __('Bill Date:', 'wer_pk') ?> <?php echo $billOrders[0]->billdate ?></small><br>
                            <?php if ($billOrders[0]->confirmed): ?>
                                <small class="badge bg-success"><?php echo $billOrders[0]->processed == 1 ? __('Processed Successfully', 'wer_pk') : __('Confirmed', 'wer_pk') ?></small>
                            <?php endif; ?>
                        </p>
                    </div>

                    <p class="mt-2 mb-3"><small><?php echo __('Description:', 'wer_pk') ?> <?php echo $billOrders[0]->description ?></small></p>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle sellerTable mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th><strong><?php echo __('Id', 'wer_pk') ?></strong></th>
                                    <th><strong><?php echo __('Bill Id', 'wer_pk') ?></strong></th>
                                    <th><strong><?php echo __('Product Id', 'wer_pk') ?></strong></th>
                                    <th><strong><?php echo __('Material Name', 'wer_pk') ?></strong></th>
                                    <th><strong><?php echo __('Quantity', 'wer_pk') ?></strong></th>
                                    <th><strong><?php echo __('GST', 'wer_pk') ?></strong></th>
                                    <th><strong><?php echo __('Order Price', 'wer_pk') ?></strong></th>
                                    <th><strong><?php echo __('Discount %', 'wer_pk') ?></strong></th>
                                    <?php echo $billOrders[0]->processed != 1 && $_REQUEST["orderStatus"] == 1 ? "<th></th>" : "" ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($billOrders as $order):
                                    $totalOrderPrice += $order->totalPrice;
                                ?>
                                <tr>
                                    <td><?php echo $order->order_id ?></td>
                                    <td><?php echo $order->billid ?></td>
                                    <td><?php echo $order->productid ?></td>
                                    <td><?php echo $order->materialsName ?></td>
                                    <td><?php echo $order->quantity ?></td>
                                    <td><span class="dashicons-before <?php echo esc_attr(Settings::set_currency_symbol()); ?>"></span>: <?php echo esc_html($order->GST); ?></td>
                                    <td><span class="dashicons-before <?php echo esc_attr(Settings::set_currency_symbol()); ?>"></span>: <?php echo $order->totalPrice ?></td>
                                    <td><?php echo $order->discount ?></td>
                                    <?php if ($order->processed != 1 && $_REQUEST["orderStatus"] == 1): ?>
                                        <td>
                                            <a class="btn btn-sm btn-outline-success" href="javascript:void(0);" onClick="wer_pkProcessOrder(<?php echo $order->order_id ?>, true);"><?php echo __('Processed', 'wer_pk'); ?></a>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td colspan="6" class="text-end"><strong><?php echo __('Total Order Price:', 'wer_pk') ?></strong></td>
                                    <td colspan="2"><strong><span class="dashicons-before <?php echo esc_attr(Settings::set_currency_symbol()); ?>"></span>: <?php echo number_format($totalOrderPrice, 2); ?></strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="alert alert-info"><?php echo __('No orders Found. Please come back later.', 'wer_pk'); ?></div>
<?php endif; ?>
</div>
