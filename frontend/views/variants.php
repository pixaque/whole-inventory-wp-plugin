<?php
	if(0 < count( $results )) {
?>
<div class="wrap" style="max-width: 100%;">

	<table width="100%" Class="table">
		<thead>
			<tr>
				<th  data-column="variant_id">
					<?php echo __('Id', 'wer_pk') ?>
				</th>
				<!-- <th  data-column="product_id" style="cursor: move;">
					product_id
				</th> -->
				<th  data-column="materialsName" style="cursor: move;">
					<?php echo __('Product(s) Variant Name', 'wer_pk') ?>  
				</th>
			</tr>
		</thead>

	<?php

	foreach ( $results as $result) {
	
	?>
		<tr>
			<td><?php echo $result->attribute_id ?></td>
			<td>
				<?php echo $result->attributeName ?><br/>
					<div Class="actionDiv">				
						<a href="javascript:void(0);" onClick="getPVariantById(<?php echo $result->attribute_id ?>);">
							<span Class="dashicons-before dashicons-edit"></span><?php echo __('Edit', 'wer_pk') ?>
						</a> |
						<a href="javascript:void(0);" onClick="deletePVariant(<?php echo $result->attribute_id ?>);">
							<span Class="dashicons-before dashicons-trash"></span><?php echo __('Delete', 'wer_pk') ?>
						</a>
					</div>

			</td>
		</tr>
	<?php } ?>

	</table>

<?php } else { ?>
	
	<strong>
		<?php echo __('No product\'s variants Found. Please start adding product\'s variants.', 'wer_pk'); ?>
	</strong>

<?php } ?>

</div>