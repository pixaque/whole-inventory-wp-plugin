<?php

//namespace WP_WER_PK_Blocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//if ( ! class_exists( '\WP_WER_PK_Blocks\products', false ) ) :

if ( ! class_exists( '\Products', false ) ) :
	/**
	 * Class products
	 */
	class Products {

			/**
		 * Menu slug.
		 *
		 * @var string
		 */
		const MENU_SLUG = 'wp_wer_pk_products';
		
		/**
		* Whether to suppress errors during the DB bootstrapping. Default false.
		*
		* @since 2.5.0
		*
		* @var array
		*/
		private $resultEdit = array();
		
		/**
			* Whether to suppress errors during the DB bootstrapping. Default false.
			*
			* @since 2.5.0
			*
			* @var bool
			*/
		private $editing = false;
		
		private static $admin_view_path = '';

		public function __construct(){}

		public static function printproducts(){
			
			/*
			 * @param none
			 * @return none
			*/
			self::$admin_view_path = plugin_dir_path(WP_WER_PK_PLUGIN_FILE) . 'adminpages/views/products/';
			
			self::products_page();

			//$this->register_settings();

		}

		public static function saveProduct($request){		
			/**
			* @global wpdb $wpdb WordPress database abstraction object.
			*/
			global $wpdb;

			// Start a transaction
			$wpdb->query('START TRANSACTION');

			$wpdb->insert(
				'wp_products',
				array(
					'storeId' => $_POST['product_store'],
					'materialsName' => $_POST['product_name'],
				)
			);

			// Get the last inserted ID
			$product_id = $wpdb->insert_id;

			// Insert into wp_product_variants
			$wpdb->insert(
				'wp_product_variants',
				array(
					'product_id' => $product_id,
					'variantSKU' => $_POST['product_SKU'].$product_id,
					'variantStock' => $_POST['product_quantity'],
					'variantPrice' => $_POST['product_price'],
					'variantDiscount' => $_POST['product_discount'],
					'variantGST' => $_POST['product_GST'],
				)
			);

			// Get the last inserted ID
			$variant_id = $wpdb->insert_id;


			$table_name = $wpdb->base_prefix . 'variant_attributes';
			
			// Split the attributes and IDs into arrays
			$attributeArray = explode(',', $_POST['attributes']);
			$attributeIdArray = explode(',', $_POST['attributesIds']);

			foreach( $attributeArray as $index => $attribute ){

				// Trim spaces and split into key-value pairs
				$attributePair = explode(':', trim($attribute));
				// Trim spaces and split into key-value pairs
				$attributeIdPair = explode(':', trim($attributeIdArray[$index]));

				$key = $attributePair[0];
				$value = $attributePair[1];

				// Combine values with the same keys
				if (!isset($combined[$key])) {
					$combined[$key] = [];
				}

				$combined[$key][] = $value;

				// Convert the array to a comma-separated string
				//$commaSeparated[] = implode(", ", $combined[$key]);

			}

			foreach ($combined as $key => $values) {
				// Convert the array of values to a comma-separated string
				$commaSeparatedArray[] = implode(",", $values);
			}

			foreach($attributeIdArray as $index => $attributeId){
				// Trim spaces and split into key-value pairs
				$attributeIdPair = explode(':', trim($attributeIdArray[$index]));

				$key = $attributeIdPair[0];
				$value = $attributeIdPair[1];

				// Combine values with the same keys
				if (!isset($combinedId[$key])) {
					$combinedId[$key] = [];
				}
				$combinedId[$key][] = $value;

			}

			$uniqueArray = array_unique( $combinedId["value"] );

			// Reset the indices
			$resetArray = array_values($uniqueArray);

			foreach($resetArray as $index => $variantEntry){
				
				//print_r( $commaSeparatedArray[$index] );// $index . "\n\r";
				// Insert into the database
				$result = $wpdb->insert($table_name, 
					array(
						'variant_id' => $variant_id,
						'attribute_id' => $variantEntry,
						'attributeValue' => trim($commaSeparatedArray[$index])
					)
				);

			}


			// Commit the transaction
			$wpdb->query('COMMIT');

			return wp_send_json(__('New Product Added.', 'wer_pk'));

		}
		

		public static function updateProduct(){			
			/**
			* @global wpdb $wpdb WordPress database abstraction object.
			*/
			global $wpdb;

			
			$table_name = $wpdb->base_prefix . 'products';

			if(!empty($_POST)){

				$q = $wpdb->prepare("UPDATE $table_name SET
									materialsName=%s
								WHERE id=%d
				;", $_POST['product_name'], $_POST['id']
				);

				$wpdb->query($q);

				$table_name = $wpdb->base_prefix . 'product_variants';

				if( $_POST['variant_id'] && $_POST['product_id'] ){
					$p = $wpdb->prepare("UPDATE $table_name SET
									variantStock=%s,
									variantGST=%s,
									variantPrice=%s,
									variantDiscount=%s,
									product_id=%s
									WHERE variant_id =%d
					;", $_POST['product_quantity'], $_POST['product_GST'], 
					$_POST['product_price'], $_POST['product_discount'], 
					$_POST['product_id'], $_POST['variant_id']
					);

					$wpdb->query($p);

				} else {

					$wpdb->insert($table_name, 
						array(
							'variantStock' => $_POST['product_quantity'],
							'variantGST' => $_POST['product_GST'],
							'variantPrice' => $_POST['product_price'],
							'variantDiscount' => $_POST['product_discount'],
							'variantSKU' => $_POST['product_SKU'],
							'product_id' => $_POST['product_id']
						)
					);

				}
				
			}

			return wp_send_json(__('Product Updated successfully.', 'wer_pk'));

		}

		public static function getProducts() {
			/**
			* @global wpdb $wpdb WordPress database abstraction object.
			*/
			global $wpdb, $current_user;
			
			$table_name = $wpdb->base_prefix . 'products';

			$currentUser = !is_object($current_user) ? "" : $current_user->data->ID;

			if( empty($currentUser) ){
					
				wp_safe_redirect( '../login.php' );
				//die();

			} else {
				$data['Admin'] = false;

				if( ! empty( $current_user->caps["administrator"]) ){

					$results = $wpdb->get_results(
						"SELECT 
							p.id AS product_id,
							p.storeId,
							p.materialsName,
							v.variant_id,
							v.variantSKU,
							v.variantStock,
							v.variantPrice,
							v.variantDiscount,
							v.variantGST,
							GROUP_CONCAT(CONCAT(pa.attributeName, ': ', va.attributeValue) ORDER BY pa.attributeName SEPARATOR ', ') AS attributes
						FROM 
							wp_products p
						LEFT JOIN 
							wp_product_variants v ON p.id = v.product_id
						LEFT JOIN 
							wp_variant_attributes va ON v.variant_id = va.variant_id
						LEFT JOIN 
							wp_product_attributes pa ON va.attribute_id = pa.attribute_id
						GROUP BY 
							p.id, v.variant_id
						ORDER BY 
							p.id, v.variant_id;"
					);
					$data['Admin'] = true;

				} else {

					$results = $wpdb->get_results(
						"SELECT 
							p.id AS product_id,
							p.storeId,
							p.materialsName,
							v.variant_id,
							v.variantSKU,
							v.variantStock,
							v.variantPrice,
							v.variantDiscount,
							v.variantGST,
							GROUP_CONCAT(CONCAT(pa.attributeName, ': ', va.attributeValue) ORDER BY pa.attributeName SEPARATOR ', ') AS attributes
						FROM 
							wp_products p
						LEFT JOIN 
							wp_product_variants v ON p.id = v.product_id
						LEFT JOIN 
							wp_variant_attributes va ON v.variant_id = va.variant_id
						LEFT JOIN 
							wp_product_attributes pa ON va.attribute_id = pa.attribute_id
						WHERE 
							p.storeId = $currentUser  -- Replace ? with the desired storeId
						GROUP BY 
							p.id, v.variant_id
						ORDER BY 
							p.id, v.variant_id;");

					//require_once plugin_dir_path(WP_WER_PK_PLUGIN_FILE) . 'frontend\views\seller_form.php';
					
					require_once __DIR__ . '/../frontend/views/seller_form.php';


				}

				require_once __DIR__ . '/../frontend/views/sellers.php';
				
				//require_once plugin_dir_path(WP_WER_PK_PLUGIN_FILE) . 'frontend\views\sellers.php';

			}

		}


		public static function getproductById($productId=null, $variantId=null){
			/**
			* @global wpdb $wpdb WordPress database abstraction object.
			*/
			global $wpdb;


			$productId = $_REQUEST['productId'];
			$variantId = $_REQUEST['variantId'];
			
			$resultEdit = "";

			$table_name = $wpdb->base_prefix . 'products';
			//"SELECT * FROM $table_name WHERE `id` = $productId;"

			if(!$_REQUEST['variantId']){
				//echo "Making Product..";
			
				$result = $wpdb->get_results(	
					"SELECT *
						FROM 
							$table_name
						WHERE 
							id = $productId;  -- Replace ? with the desired storeId"
				);

				$resultEdit = $result[0];

			} else if( $_REQUEST['productId'] && $_REQUEST['variantId'] ) {
				//echo "Editting..";

				$result = $wpdb->get_results(	
					"SELECT 
								p.id AS product_id,
								p.storeId,
								p.materialsName,
								v.variant_id,
								v.variantSKU,
								v.variantStock,
								v.variantPrice,
								v.variantDiscount,
								v.variantGST,
								GROUP_CONCAT(CONCAT(pa.attributeName, ': ', va.attributeValue) ORDER BY pa.attributeName SEPARATOR ', ') AS attributes
							FROM 
								wp_products p
							LEFT JOIN 
								wp_product_variants v ON p.id = v.product_id
							LEFT JOIN 
								wp_variant_attributes va ON v.variant_id = va.variant_id
							LEFT JOIN 
								wp_product_attributes pa ON va.attribute_id = pa.attribute_id
							WHERE 
								v.variant_id = $variantId  -- Replace ? with the desired storeId
							GROUP BY 
								p.id, v.variant_id
							ORDER BY 
								p.id, v.variant_id;"
				);


				$resultEdit = $result[0];
			}

		
			return wp_send_json($resultEdit);

		}

		public static function getSelectedVariantData(){
			global $wpdb;

			// Get the attribute ID from the AJAX request
			$attribute_id = intval($_GET['attribute_id']);

			// Query to get distinct attribute values for the given attribute_id
			$query = $wpdb->prepare("
				SELECT DISTINCT attributeValue
				FROM wp_variant_attributes
				WHERE attribute_id = %d
			", $attribute_id);

			$results = $wpdb->get_col($query); // Get an array of values

			// Return JSON response
			wp_send_json($results);

		}

		public static function deleteProduct(){
			
			/**
			* @global wpdb $wpdb WordPress database abstraction object.
			*/
			global $wpdb;
			$table_name = $wpdb->base_prefix . 'products';

			$productId = $_REQUEST['productId'];

			$results = $wpdb->delete($table_name, array('id' => $productId));

			return wp_send_json(__('Product deleted successfully.', 'wer_pk'));
			
		}

		public static function getProductVariant() {
			/**
			* @global wpdb $wpdb WordPress database abstraction object.
			*/
			global $wpdb;
			$table_name = $wpdb->base_prefix . 'product_attributes';

			$query = "
				SELECT * FROM $table_name";

			$results = $wpdb->get_results($query);

			echo '<label for="">'. __('Select product Variant(s): ', 'wer_pk');
			echo '<select class="variantSelect" name="selectVariants" id="selectProductVariants">';
			echo '<option value="null" >'. __('Select product Variant(s)', 'wer_pk') . '</option>';
			
			

			foreach ($results as $row) {
				
				// Generate the select element
				echo '<option value="' . esc_attr(trim($row->attribute_id)) . '" >' . esc_html(trim($row->attributeName)) . '</option>';
			}

			echo '</select></label>';

			echo ' OR <a href="#" id="open-dialog" onClick="document.getElementById(`my-dialog`).showModal();">'. __('Manage Variants', 'wer_pk') . '</a>';

			$_REQUEST["totalVariants"] = count($results);

			//return wp_send_json($results[0]);
			
		}

		/**
		 * Load settings page content.
		 */
		public static function products_page() {

			$data['url-add_edit'] = admin_url('admin.php?page=wp_wer_pk_products&action=save');

			if(empty( $_REQUEST['action'] )){
				
				require_once self::$admin_view_path . 'products_form.php';	
				self::getproducts();

			} else if(sanitize_text_field($_REQUEST['action']) === 'edit') {
				
				self::getproducts();
				//require_once self::$admin_view_path . 'products_form.php';

			} else {
				
				require_once self::$admin_view_path . 'products_form.php';	
				self::getproducts();

			}

		}

		public static function product_detail(){

			//require_once plugin_dir_path(WP_WER_PK_PLUGIN_FILE) . 'classes\class-product-details.php';

			require_once __DIR__ . '/class-product-details.php';

			productDetails::printproductDetails();


		}

		
		public static function savePVariant(){		
			/**
			* @global wpdb $wpdb WordPress database abstraction object.
			*/
			global $wpdb;
			
			$table_name = $wpdb->base_prefix . 'product_attributes';

			$product_variant = $_REQUEST['product_variant'];

			if(empty( $_REQUEST['product_variant'] )){
				return;
			}

			// Insert into wp_product_variants
			$wpdb->insert(
				$table_name,
				array(
					'attributeName' => $product_variant
				)
			);


			return wp_send_json( __('New Product Variant Added.', 'wer_pk') );

		}

		public static function getPVariant(){		
			/**
			* @global wpdb $wpdb WordPress database abstraction object.
			*/
			global $wpdb;

			$table_name = $wpdb->base_prefix . 'product_attributes';

			$results = $wpdb->get_results(
				"SELECT * FROM $table_name"
			);

			//require_once plugin_dir_path(WP_WER_PK_PLUGIN_FILE) . 'frontend\views\variants.php';

			require_once __DIR__ . '/../frontend/views/variants.php';

			//return wp_send_json($billResult);

		}

		public static function getPVariantById(){		
			/**
			* @global wpdb $wpdb WordPress database abstraction object.
			*/
			global $wpdb;

			$variant_id = $_REQUEST["variant_id"];

			$table_name = $wpdb->base_prefix . 'product_attributes';

			$results = $wpdb->get_results(
				"SELECT * FROM $table_name WHERE `attribute_id` = $variant_id"
			);

			return wp_send_json($results[0]);

		}

		public static function deletePVariant(){		
			/**
			* @global wpdb $wpdb WordPress database abstraction object.
			*/
			global $wpdb;

			$variant_id = $_REQUEST["variant_id"];

			$table_name = $wpdb->base_prefix . 'product_attributes';

			$results = $wpdb->delete($table_name, array('attribute_id' => $variant_id));

			return wp_send_json(__('Product\'s variant deleted successfully.', 'wer_pk'));


		}

		public static function deletePVariantById(){		
			/**
			* @global wpdb $wpdb WordPress database abstraction object.
			*/
			global $wpdb;

			$variant_id = sanitize_text_field($_REQUEST["variant_id"]);
			$attributeValue = sanitize_text_field($_REQUEST["attributeValue"]);

			$table_name = $wpdb->base_prefix . 'variant_attributes';
			
			$deleted_rows = $wpdb->delete($table_name, array('variant_id' => $variant_id, 'attributeValue' => $attributeValue ));

			if ($deleted_rows !== false) {
				// Deletion was successful
				wp_send_json("{$deleted_rows} rows deleted.");
			} else {
				// Handle error
				wp_send_json("Error deleting rows: " . $wpdb->last_error);
			}

			return wp_send_json(__('Selected Product\'s variant deleted successfully.', 'wer_pk'));

		}

		public static function editPVariant(){		
			/**
			* @global wpdb $wpdb WordPress database abstraction object.
			*/
			global $wpdb;


			$variant_id = $_REQUEST["variant_id"];

			$table_name = $wpdb->base_prefix . 'product_attributes';

			$q = $wpdb->prepare("UPDATE $table_name SET
									attributeName=%s
								WHERE attribute_id=%d
				;", $_REQUEST['attributeName'], $variant_id
				);

			$wpdb->query($q);

			return wp_send_json(__('Product\'s variant updated successfully.', 'wer_pk'));


		}

	}

endif;	