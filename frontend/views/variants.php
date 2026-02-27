<?php
	if(0 < count( $results )) {
?>
<div class="werpk-shell">
	<div class="card werpk-card">
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-striped table-hover align-middle" width="100%">
					<thead class="table-dark">
						<tr>
							<th data-column="variant_id"><?php echo __('Id', 'wer_pk') ?></th>
							<th data-column="materialsName"><?php echo __('Product(s) Variant Name', 'wer_pk') ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ( $results as $result) { ?>
						<tr>
							<td><?php echo $result->attribute_id ?></td>
							<td>
								<?php echo $result->attributeName ?><br/>
								<div Class="actionDiv mt-1">
									<a class="btn btn-sm btn-outline-primary" href="javascript:void(0);" onClick="getPVariantById(<?php echo $result->attribute_id ?>);">
										<span Class="dashicons-before dashicons-edit"></span><?php echo __('Edit', 'wer_pk') ?>
									</a>
									<a class="btn btn-sm btn-outline-danger" href="javascript:void(0);" onClick="deletePVariant(<?php echo $result->attribute_id ?>);">
										<span Class="dashicons-before dashicons-trash"></span><?php echo __('Delete', 'wer_pk') ?>
									</a>
								</div>
							</td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
<?php } else { ?>
	<div class="alert alert-info">
		<?php echo __('No product\'s variants Found. Please start adding product\'s variants.', 'wer_pk'); ?>
	</div>
<?php } ?>
</div>
