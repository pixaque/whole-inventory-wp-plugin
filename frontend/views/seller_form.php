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

<div class="werpk-shell">
	<div class="card werpk-card mb-4">
		<div class="card-body">
			<h4 class="card-title mb-4"><?php echo __('Add Product(s)', 'wer_pk'); ?></h4>
			<form method="post" id="product_Add_Edit" action="javascript:void(0)" enctype="multipart/form-data">
				<input type="hidden" name="product_store" value="<?php echo !empty($data['editProduct']) ? $data['editProduct']->id : $currentUser; ?>" />
				<input type="hidden" name="product_id" value="<?php echo !empty($data['editProduct']) ? $data['editProduct']->product_id : ""; ?>" />
				<input type="hidden" name="variant_id" value="<?php echo !empty($data['editProduct']) ? $data['editProduct']->variant_id : ""; ?>" />

				<fieldset id="variantFieldset" class="border rounded-3 p-3 mb-4">
					<legend class="float-none w-auto fs-6 px-2"><?php echo __('Manage Product Variants', 'wer_pk'); ?></legend>
					<div id="variantProducts" class="mb-3">
						<?php $WER_PK_Products::getProductVariant(); ?>
					</div>
					<div id="inputContainer"></div>
				</fieldset>

				<div class="mb-3">
					<label class="form-label wp-wer_pk-label"><?php echo __('Product Name', 'wer_pk'); ?></label>
					<input class="form-control" type="text" value="<?php echo !empty($data['editProduct']) ? $data['editProduct']->product_name : ""; ?>" name="product_name" />
				</div>

				<div class="row g-3">
					<div class="col-md-3">
						<label class="form-label wp-wer_pk-label"><?php echo __('Quantity', 'wer_pk'); ?></label>
						<input class="form-control" type="number" min="1" max="5000" value="<?php echo !empty($data['editProduct']) ? $data['editProduct']->product_quantity : "";?>" name="product_quantity" />
					</div>
					<div class="col-md-3">
						<label class="form-label wp-wer_pk-label"><?php echo __('GST', 'wer_pk'); ?></label>
						<input class="form-control" type="text" value="<?php echo !empty($data['editProduct']) ? $data['editProduct']->product_GST : "0.00";?>" name="product_GST" />
					</div>
					<div class="col-md-3">
						<label class="form-label wp-wer_pk-label"><?php echo __('Product Price', 'wer_pk'); ?></label>
						<input class="form-control" type="text" value="<?php echo !empty($data['editProduct']) ? $data['editProduct']->product_price : "";?>" name="product_price" />
					</div>
					<div class="col-md-3">
						<label class="form-label wp-wer_pk-label"><?php echo __('Discount %', 'wer_pk'); ?></label>
						<input class="form-control" type="text" value="<?php echo !empty($data['editProduct']) ? $data['editProduct']->product_discount : "0";?>" name="product_discount" />
					</div>
				</div>

				<div class="mt-4">
					<?php if ( isset($data['editing']) ) { ?>
						<button
							type="submit"
							name="Update"
							id="UpdateMe"
							class="btn btn-primary"
							onClick="wer_pkUpdateForm();"
						>
							<?php echo __('Update Changes', 'wer_pk'); ?> <span class="spinner" id="mainSpinner" style="visibility: visible;"></span>
						</button>
					<?php } else { ?>
						<button
							type="submit"
							name="Save"
							id="SaveMe"
							class="btn btn-success"
							onClick="wer_pkSaveProduct();"
						>
							<?php echo __('Save product', 'wer_pk'); ?> <span class="spinner" id="mainSpinner" style="visibility: visible;"></span>
						</button>
					<?php } ?>
				</div>
			</form>
		</div>
	</div>

	<dialog id="my-dialog" class="p-4 border-0 rounded-3 shadow">
		<form method="dialog" id="variant_Add_Edit" action="javascript:void(0)">
			<label class="form-label wp-wer_pk-label"><?php echo __('Product Variants', 'wer_pk'); ?></label>
			<input class="form-control mb-3" type="text" value="" name="product_variant"/>
			<button id="variantSubmit" class="btn btn-primary" type="submit" onClick="savePVariant()" ><?php echo __('Submit', 'wer_pk'); ?></button>
		</form>

		<?php $WER_PK_Products::getPVariant(); ?>
	</dialog>
</div>
