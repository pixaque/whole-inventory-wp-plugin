<?php /*
          <?php if(is_user_logged_in()) { ?>
            <a href="<?php echo wp_logout_url( site_url ( 'login' ) );  ?>" class="btn btn--small  btn--dark-orange">
            <span class="site-header__avatar"><?php echo get_avatar(get_current_user_id(), 60); ?></span>
            <span class="btn__text">Log Out</span>
            </a>
            <?php } else { ?>
              <a href="<?php echo wp_login_url( site_url ( 'login' ) ); ?>" class="btn btn--small btn--orange">Login</a>
             <?php } ?>
			 
			 */
			
			 include_once("header.php");

			 ?>
			 
<?php 

global $WER_PK_Products;
$WER_PK_Products = new Products();

?>

<div class="wrap" style="max-width: 100%;">
	</br>
	<h4><?php echo __('Add Product(s)', 'wer_pk'); ?></h4>
	<form method="post" id="product_Add_Edit" action="javascript:void(0)" enctype="multipart/form-data">
		<input type="hidden" name="product_store" value="<?php echo !empty($data['editProduct']) ? $data['editProduct']->id : $currentUser; ?>" />
		<input type="hidden" name="product_id" value="<?php echo !empty($data['editProduct']) ? $data['editProduct']->product_id : ""; ?>" />
		<input type="hidden" name="variant_id" value="<?php echo !empty($data['editProduct']) ? $data['editProduct']->variant_id : ""; ?>" />
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td colspan="4">
					<fieldset id="variantFieldset">
						<legend><?php echo __('Manage Product Variants', 'wer_pk'); ?></legend>
						<div id="variantProducts">
							<?php $WER_PK_Products::getProductVariant(); ?>
						</div>
						<div id="inputContainer"></div> <!-- Container for dynamic inputs -->
					</fieldset>
				</td>
			</tr>
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="4">
					<label class="wp-wer_pk-label"><?php echo __('Product Name', 'wer_pk'); ?></label><br/>
					<input type="text" value="<?php echo !empty($data['editProduct']) ? $data['editProduct']->product_name : ""; ?>" name="product_name"  />
				</td>
				
			</tr>
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>
			<tr>
				<td>
					<label class="wp-wer_pk-label"><?php echo __('Quantity', 'wer_pk'); ?></label><br/>
					<input type="number" min="1" max="5000" value="<?php echo !empty($data['editProduct']) ? $data['editProduct']->product_quantity : "";?>" name="product_quantity" />
				</td>
				<td>
					<label class="wp-wer_pk-label"><?php echo __('GST', 'wer_pk'); ?></label><br/>
					<input type="text" value="<?php echo !empty($data['editProduct']) ? $data['editProduct']->product_GST : "0.00";?>" name="product_GST" />
				</td>
				<td>
					<label class="wp-wer_pk-label"><?php echo __('Product Price', 'wer_pk'); ?></label><br/>
					<input type="text" value="<?php echo !empty($data['editProduct']) ? $data['editProduct']->product_price : "";?>" name="product_price" />
				</td>
				<td>
					<label class="wp-wer_pk-label"><?php echo __('Discount %', 'wer_pk'); ?></label><br/>
					<input type="text" value="<?php echo !empty($data['editProduct']) ? $data['editProduct']->product_discount : "0";?>" name="product_discount" />
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
						onClick="wer_pkUpdateForm();"
						>
						<?php echo __('Update Changes', 'wer_pk'); ?> <span class="spinner" id="mainSpinner" style="visibility: visible;"></span>
						</button>
					<?php
						} else {
					?>
						<button 
						type="submit" 
						name="Save" 
						id="SaveMe" 
						class="button button-secondary button-large"
						onClick="wer_pkSaveProduct();"
						>
						<?php echo __('Save product', 'wer_pk'); ?> <span class="spinner" id="mainSpinner" style="visibility: visible;"></span>
						</button>
						
					<?php
						}
					?>
				</td>
			</tr>

		</table>


		
	</form>


	<dialog id="my-dialog">
		<form method="dialog" id="variant_Add_Edit" action="javascript:void(0)">
			<label class="wp-wer_pk-label"><?php echo __('Product Variants', 'wer_pk'); ?></label><br/>
					<input 
						type="text" 
						value="" 
						name="product_variant"
						/>
			<button id="variantSubmit" type="submit" onClick="savePVariant()" ><?php echo __('Submit', 'wer_pk'); ?></button>
		</form>

		<?php $WER_PK_Products::getPVariant(); ?>

	</dialog>

</div><!--end #wrap -->