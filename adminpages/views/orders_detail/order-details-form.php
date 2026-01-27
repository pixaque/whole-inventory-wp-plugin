<div class="wrap" id="<?php echo esc_attr( self::MENU_SLUG ); ?>">

	<?php 
		do_action('dummyNotice');
	?>

	<?php 
	if($result[0]->confirmed){
	?>	
		<div class="notice notice-success" style="position: relative;">
			<p><?php echo __( "This order is already confirmed. Please add new from the orders page.", 'wer_pk' ); ?></p>
		</div>
		</br>
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td>
					<h1><?php echo __( "Bill No: ", 'wer_pk' ); echo $result[0]->billNo; ?></h1>
					<p><?php echo $result[0]->description; ?></p>
				</td>
				<td>
					<h1><?php echo __( "Dated: ", 'wer_pk' ); echo $result[0]->billdate; ?></h1>
					<p><strong><?php echo __( "Expense Type: " . $result[0]->expenseType, 'wer_pk' ); ?></strong></p>
				</td>
			</tr>
		</table>
	<?php

	} else {
	?>

	<div class="notice notice-error" style="position: relative;" id="notice">
		<span class="dashicons-before dashicons-no" id="closeMe"></span>
	</div>
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td>
					<h1><?php echo __( "Bill No: ", 'wer_pk' ); ?><?php echo $result[0]->billNo; ?></h1>
					<p><?php echo $result[0]->description; ?></p>
				</td>
				<td>
					<h1><?php echo __( "Dated: ", 'wer_pk' ); echo $result[0]->billdate; ?></h1>
					<p><strong><?php esc_html_e( "Expense Type: ", 'wer_pk' ); echo $result[0]->expenseType; ?></strong></p>
				</td>
			</tr>
		</table>
	<h1><?php echo __( "Add Item", 'wer_pk' ); ?></h1>

	

	<form method="post" id="item_Add_Edit" action="javascript:void(0)" enctype="multipart/form-data">
		<?php
		/**
			<input type="hidden" name="item_id" value="<?php echo !empty($data['editItem']) ? $data['editItem']->id : ""; ?>" />
			<input type="hidden" name="GST" value="<?php echo !empty($result[0]->GST) ? $result[0]->GST : ""; ?>" />
			<input type="hidden" name="discount" value="<?php echo !empty($result[0]->discount) ? $result[0]->discount : ""; ?>" />
		**/
		?>

		<input type="hidden" name="billid" value="<?php echo !empty($result[0]->id) ? $result[0]->id : ""; ?>" />
		<input type="hidden" name="productid" value="<?php echo !empty($data['editItem']) ? $data['editItem']->id : ""; ?>" />
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td>
					<label class="wp-wer_pk-label"><?php _e('Select a Supplier', 'wer_pk' );?></label></br>
					<select name="supplierName" required="required" onChange="changeSupplier();">
						<option><?php echo __( "Please Select A Supplier.", 'wer_pk' ); ?></option>
						<?php 
							foreach($suppliers as $sup){
								$selectedOption = $sup->ID  ===  (!empty($data['editItem']) ? $data['editItem']->supplierName : "")  ? "selected" : "";
								echo "<option value='$sup->ID' $selectedOption>$sup->display_name</option>";
							}; 
						?>
					</select>
				</td>
				<td>
					<label class="wp-wer_pk-label"><?php _e('Select Item', 'wer_pk' );?></label></br>
					<strong class="selectedItem"><?php echo !empty($data['editItem']) ? $data['editItem']->materialsName : ""; ?></strong>
					<select name="materialsName" required="required" onChange="changeItemPrice();">
						<option><?php _e( "Please Select An Item.", 'wer_pk' ); ?></option>
					</select>
					<br>
					<label class="wp-wer_pk-label" style="font-weight: bold;"><small class="itemsinHand"></small> </label>
				</td>
				<td>
					<label class="wp-wer_pk-label"><?php _e('Quantity', 'wer_pk' );?></label></br>
					<input type="number" min="1" max="50" value="<?php echo !empty($data['editItem']) ? $data['editItem']->quantity : 1;?>" name="quantity"  required="required"/> * (<span class="itemPrice">0.00</span> + <span class="itemGST">0.00</span>)
				</td>
				<td>
					<label class="wp-wer_pk-label"><?php _e('Discount %', 'wer_pk' );?></label></br>
					<input type="text" value="<?php echo !empty($data['editItem']) ? $data['editItem']->discount : 0;?>" name="discount"  class="small-text" />
				</td>
				<td>
					<label class="wp-wer_pk-label"><?php _e('GST', 'wer_pk' );?></label></br>
					<input type="text" value="<?php echo !empty($data['editItem']) ? $data['editItem']->GST : 0.00;?>" name="GST" class="small-text" disabled="disabled" /> + 
				</td>
				<td>
					<label class="wp-wer_pk-label"><?php _e('Price', 'wer_pk' );?></label></br>
					<input type="text" value="<?php echo !empty($data['editItem']) ? $data['editItem']->totalPrice : ""; ?>" name="totalPrice"  required="required"  disabled="disabled"/>
				</td>
			</tr>
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="4">
					<?php
						if ( isset($data['editing']) ) {
					?>
						<button 
						type="submit" 
						name="Update" 
						id="UpdateMe" 
						class="button button-secondary button-large" 
						onClick="wer_pkUpdateBillItem();"
						>
						<?php echo __('Update Changes', 'wer_pk' );?> <span class="spinner" id="mainSpinner" style="visibility: visible;"></span>
						</button>
					<?php
						} else {
					?>
						<button 
						type="submit" 
						name="Save" 
						id="SaveMe" 
						class="button button-secondary button-large"
						onClick="wer_pkSaveBillItem();"
						>
						<?php echo __('Save item', 'wer_pk' );?> <span class="spinner" id="mainSpinner" style="visibility: visible;"></span>
						</button>
						
					<?php
						}
					?>
				</td>
			</tr>

		</table>


		
	</form>

	<button type="button" 
		id="confirmOrder" 
		class="button button-primary button-large" 
		style="position: absolute; top: 10px; right: 10px; z-index: 99999;" 
		onClick="wer_pkConfirmOrder(<?php echo $result[0]->id ?>)"
	><?php echo __('Confirm Order', 'wer_pk' );?> <span class="spinnerEmail" style="visibility: visible; display: none;"></span></button>

	<button type="button" 
		id="confirmOrder" 
		class="button button-primary button-large" 
		style="position: absolute; bottom: 10px; right: 10px; z-index: 99999;" 
		onClick="wer_pkConfirmOrder(<?php echo $result[0]->id ?>)"
	><?php echo __('Confirm Order', 'wer_pk' );?> <span class="spinnerEmail" style="visibility: visible; display: none;"></span></button>
	
	<?php
		}
	?>
	

</div><!--end #wrap -->