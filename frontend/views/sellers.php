<?php
	if(0 < count( $results )){
?>
<div class="werpk-shell">
	<div class="card werpk-card">
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-striped table-hover align-middle" width="100%">
					<thead class="table-dark">
						<tr>
							<th data-column="variant_id"><?php echo __('Id', 'wer_pk') ?></th>
							<th data-column="materialsName"><?php echo __('Product(s)', 'wer_pk') ?></th>
							<th data-column="variantSKU"><?php echo __('SKU', 'wer_pk') ?></th>
							<th data-column="variantStock"><?php echo __('Stock', 'wer_pk') ?></th>
							<th data-column="variantGST"><?php echo __('GST', 'wer_pk') ?></th>
							<th data-column="variantPrice"><?php echo __('Price', 'wer_pk') ?></th>
							<th data-column="variantDiscount"><?php echo __('Discount', 'wer_pk') ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ( $results as $result) { ?>
						<tr>
							<td><?php echo $result->variant_id ?></td>
							<td>
								<?php echo $result->materialsName ?><br/>
								<?php echo $result->attributes ?>
								<?php if( ! $data['Admin'] ) { ?>
									<div Class="actionDiv mt-1">
										<a class="btn btn-sm btn-outline-primary" href="javascript:void(0);" onClick="wer_pkEditSeller(<?php echo $result->product_id ?>, <?php echo $result->variant_id ?>);">
											<span Class="dashicons-before dashicons-edit"></span><?php echo __('Edit', 'wer_pk') ?>
										</a>
										<a class="btn btn-sm btn-outline-danger" href="javascript:void(0);" onClick="wer_pkDeleteSeller(<?php echo $result->product_id ?>, 'Are you sure?');">
											<span Class="dashicons-before dashicons-trash"></span><?php echo __('Delete', 'wer_pk') ?>
										</a>
									</div>
								<?php } ?>
							</td>
							<td><?php echo $result->variantSKU ?></td>
							<td><?php echo $result->variantStock ?></td>
							<td><span class="dashicons-before <?php echo esc_attr(Settings::set_currency_symbol()); ?>"></span>: <?php echo $result->variantGST ?></td>
							<td><span class="dashicons-before <?php echo esc_attr(Settings::set_currency_symbol()); ?>"></span>: <?php echo $result->variantPrice ?></td>
							<td><?php echo $result->variantDiscount ?></td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
<?php } else { ?>
	<div class="alert alert-info"><?php echo __('No products Found. Please start adding products.', 'wer_pk'); ?></div>
<?php } ?>
</div>
