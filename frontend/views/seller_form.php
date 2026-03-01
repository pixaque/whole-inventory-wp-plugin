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

include_once( 'header.php' );

$current_page_id = get_queried_object_id();

$seller_add_products_heading   = __( 'Add Product(s)', 'wer_pk' );
$seller_manage_variants_legend = __( 'Manage Product Variants', 'wer_pk' );
$seller_product_name_label     = __( 'Product Name', 'wer_pk' );
$seller_quantity_label         = __( 'Quantity', 'wer_pk' );
$seller_gst_label              = __( 'GST', 'wer_pk' );
$seller_product_price_label    = __( 'Product Price', 'wer_pk' );
$seller_discount_label         = __( 'Discount %', 'wer_pk' );
$seller_update_button_text     = __( 'Update Changes', 'wer_pk' );
$seller_save_button_text       = __( 'Save product', 'wer_pk' );
$seller_variant_label          = __( 'Product Variants', 'wer_pk' );
$seller_variant_submit_text    = __( 'Submit', 'wer_pk' );

if ( function_exists( 'rwmb_get_value' ) && $current_page_id ) {
	$map = array(
		'wer_pk_seller_add_products_heading'   => 'seller_add_products_heading',
		'wer_pk_seller_manage_variants_legend' => 'seller_manage_variants_legend',
		'wer_pk_seller_product_name_label'     => 'seller_product_name_label',
		'wer_pk_seller_quantity_label'         => 'seller_quantity_label',
		'wer_pk_seller_gst_label'              => 'seller_gst_label',
		'wer_pk_seller_product_price_label'    => 'seller_product_price_label',
		'wer_pk_seller_discount_label'         => 'seller_discount_label',
		'wer_pk_seller_update_button_text'     => 'seller_update_button_text',
		'wer_pk_seller_save_button_text'       => 'seller_save_button_text',
		'wer_pk_seller_variant_label'          => 'seller_variant_label',
		'wer_pk_seller_variant_submit_text'    => 'seller_variant_submit_text',
	);

	foreach ( $map as $meta_key => $var_name ) {
		$meta_value = rwmb_get_value( $meta_key, array(), $current_page_id );
		if ( ! empty( $meta_value ) ) {
			${$var_name} = $meta_value;
		}
	}
}

global $WER_PK_Products;
$WER_PK_Products = new Products();
?>

<div class="wrap" style="max-width: 100%;">
	</br>
	<h4><?php echo esc_html( $seller_add_products_heading ); ?></h4>
	<form method="post" id="product_Add_Edit" action="javascript:void(0)" enctype="multipart/form-data">
		<input type="hidden" name="product_store" value="<?php echo ! empty( $data['editProduct'] ) ? esc_attr( $data['editProduct']->id ) : esc_attr( $currentUser ); ?>" />
		<input type="hidden" name="product_id" value="<?php echo ! empty( $data['editProduct'] ) ? esc_attr( $data['editProduct']->product_id ) : ''; ?>" />
		<input type="hidden" name="variant_id" value="<?php echo ! empty( $data['editProduct'] ) ? esc_attr( $data['editProduct']->variant_id ) : ''; ?>" />
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td colspan="4">
					<fieldset id="variantFieldset">
						<legend><?php echo esc_html( $seller_manage_variants_legend ); ?></legend>
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
					<label class="wp-wer_pk-label"><?php echo esc_html( $seller_product_name_label ); ?></label><br/>
					<input type="text" value="<?php echo ! empty( $data['editProduct'] ) ? esc_attr( $data['editProduct']->product_name ) : ''; ?>" name="product_name"  />
				</td>

			</tr>
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>
			<tr>
				<td>
					<label class="wp-wer_pk-label"><?php echo esc_html( $seller_quantity_label ); ?></label><br/>
					<input type="number" min="1" max="5000" value="<?php echo ! empty( $data['editProduct'] ) ? esc_attr( $data['editProduct']->product_quantity ) : ''; ?>" name="product_quantity" />
				</td>
				<td>
					<label class="wp-wer_pk-label"><?php echo esc_html( $seller_gst_label ); ?></label><br/>
					<input type="text" value="<?php echo ! empty( $data['editProduct'] ) ? esc_attr( $data['editProduct']->product_GST ) : '0.00'; ?>" name="product_GST" />
				</td>
				<td>
					<label class="wp-wer_pk-label"><?php echo esc_html( $seller_product_price_label ); ?></label><br/>
					<input type="text" value="<?php echo ! empty( $data['editProduct'] ) ? esc_attr( $data['editProduct']->product_price ) : ''; ?>" name="product_price" />
				</td>
				<td>
					<label class="wp-wer_pk-label"><?php echo esc_html( $seller_discount_label ); ?></label><br/>
					<input type="text" value="<?php echo ! empty( $data['editProduct'] ) ? esc_attr( $data['editProduct']->product_discount ) : '0'; ?>" name="product_discount" />
				</td>
			</tr>
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="4">
					<?php if ( isset( $data['editing'] ) ) : ?>
						<button
							type="submit"
							name="Update"
							id="UpdateMe"
							class="button button-secondary button-large"
							onClick="wer_pkUpdateForm();"
						>
							<?php echo esc_html( $seller_update_button_text ); ?> <span class="spinner" id="mainSpinner" style="visibility: visible;"></span>
						</button>
					<?php else : ?>
						<button
							type="submit"
							name="Save"
							id="SaveMe"
							class="button button-secondary button-large"
							onClick="wer_pkSaveProduct();"
						>
							<?php echo esc_html( $seller_save_button_text ); ?> <span class="spinner" id="mainSpinner" style="visibility: visible;"></span>
						</button>

					<?php endif; ?>
				</td>
			</tr>

		</table>



	</form>


	<dialog id="my-dialog">
		<form method="dialog" id="variant_Add_Edit" action="javascript:void(0)">
			<label class="wp-wer_pk-label"><?php echo esc_html( $seller_variant_label ); ?></label><br/>
					<input
						type="text"
						value=""
						name="product_variant"
						/>
			<button id="variantSubmit" type="submit" onClick="savePVariant()" ><?php echo esc_html( $seller_variant_submit_text ); ?></button>
		</form>

		<?php $WER_PK_Products::getPVariant(); ?>

	</dialog>

</div><!--end #wrap -->
