<?php

//namespace WP_WER_PK_Blocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//if ( ! class_exists( '\WP_WER_PK_Blocks\OrderDetail', false ) ) :

if ( ! class_exists( '\OrderDetail', false ) ) :
	/**
	 * Class OrderDetail
	 */
	class OrderDetail {

			/**
		 * Menu slug.
		 *
		 * @var string
		 */
		const MENU_SLUG = 'wp_wer_pk_blocks_OrderDetail';
		
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

		private $orderDetails = array();
		
		private static $admin_view_path = '';

		public function __construct(){
		
			//add_action( 'order_init', array( '\wer_pkMain', 'testFunction' ) );

		}


		private static function begin_transaction() {
			global $wpdb;
			$wpdb->query( 'START TRANSACTION' );
		}

		private static function commit_transaction() {
			global $wpdb;
			$wpdb->query( 'COMMIT' );
		}

		private static function rollback_transaction() {
			global $wpdb;
			$wpdb->query( 'ROLLBACK' );
		}

		private static function authorize_ajax_mutation() {
			if ( ! current_user_can( 'manage_options' ) ) {
				if ( wp_doing_ajax() ) {
					wp_send_json_error( array( 'message' => __( 'Unauthorized request.', 'wer_pk' ) ), 403 );
				}

				wp_die( esc_html__( 'Unauthorized request.', 'wer_pk' ), 403 );
			}

			if ( wp_doing_ajax() && ! check_ajax_referer( 'wer_pk_ajax_nonce', 'nonce', false ) ) {
				wp_send_json_error( array( 'message' => __( 'Invalid security token.', 'wer_pk' ) ), 403 );
			}
		}

		public static function printOrderDetail(){
		
			/*
			 * @param none
			 * @return none
			*/
			self::$admin_view_path = plugin_dir_path(WP_WER_PK_PLUGIN_FILE) . 'adminpages/views/orders_detail/';
			
			require_once plugin_dir_path(__FILE__) . '/class-wp-order-detail-list-table.php';

			self::OrderDetail_page();

			//$this->register_settings();

		}

		public static function checkExistingProducts(){
			/**
			* @global wpdb $wpdb WordPress database abstraction object.
			*/
			self::authorize_ajax_mutation();

			global $wpdb;

			$table_name = $wpdb->base_prefix . 'project_order';

			$billResult = $wpdb->get_results(
				$wpdb->prepare( "SELECT * FROM $table_name WHERE `billid` = %d;", isset( $_POST['billid'] ) ? (int) wp_unslash( $_POST['billid'] ) : 0 )
			);

			$totalCount = array();

			foreach($billResult as $s){
				
				if( ( isset( $_POST['materialsName'] ) ? sanitize_text_field( wp_unslash( $_POST['materialsName'] ) ) : "" ) === $s->materialsName ){
					$totalCount[] = $s;
				}

			}

			return $totalCount;

		}

		/*
		public static function () {

		}
		*/

		public static function saveorder($request){		
			/**
			* @global wpdb $wpdb WordPress database abstraction object.
			*/
			self::authorize_ajax_mutation();

			global $wpdb;
			
			$existingItems = array();

			if( !empty( $_POST ) ){

				$existingItems = self::checkExistingProducts();

				$product_id = isset( $_POST['productid'] ) ? (int) wp_unslash( $_POST['productid'] ) : 0;
				$bill_id = isset( $_POST['billid'] ) ? (int) wp_unslash( $_POST['billid'] ) : 0;
				$requested_quantity = isset( $_POST['quantity'] ) ? (int) wp_unslash( $_POST['quantity'] ) : 0;

				$table_name = $wpdb->base_prefix . 'product_variants';
				self::begin_transaction();

				$results = $wpdb->get_results(
					$wpdb->prepare( "SELECT * FROM $table_name WHERE `variant_id` = %d FOR UPDATE;", $product_id )
				);

				if ( empty( $results ) ) {
					self::rollback_transaction();
					return wp_send_json( "Product variant not found." );
				}

				$inHand = $results[0]->variantStock - $requested_quantity;

				if( !empty( $existingItems)  && ($inHand >= 0) ){
					
					//echo "found record.";
					//print_r($existingItems);

					self::commit_transaction();
					self::updateorder($existingItems);


				} else {

					if( $inHand >= 0 ){

						$table_name = $wpdb->base_prefix . 'project_order';

						$wpdb->insert($table_name, 
							array(
								'billid' => $bill_id,
								'supplierName' => isset( $_POST['supplierName'] ) ? sanitize_text_field( wp_unslash( $_POST['supplierName'] ) ) : '',
								'materialsName' => isset( $_POST['materialsName'] ) ? sanitize_text_field( wp_unslash( $_POST['materialsName'] ) ) : '',
								'discount' => isset( $_POST['discount'] ) ? (float) wp_unslash( $_POST['discount'] ) : 0,
								'quantity' => $requested_quantity,
								'GST' => isset( $_POST['GST'] ) ? (float) wp_unslash( $_POST['GST'] ) : 0,
								'totalPrice' => isset( $_POST['totalPrice'] ) ? (float) wp_unslash( $_POST['totalPrice'] ) : 0,
								'productid' => $product_id
							)
						);

						$table_name = $wpdb->base_prefix . 'product_variants';

						$q = $wpdb->prepare("UPDATE $table_name SET
										variantStock=%d
									WHERE variant_id=%d
						;", $inHand, $product_id
						);
						$wpdb->query($q);


						$table_name = $wpdb->base_prefix . 'project_order';

						$billResult = $wpdb->get_results(
							$wpdb->prepare( "SELECT * FROM $table_name WHERE `billid` = %d;", $bill_id )
						);

						$orderTotal = [];
						$i = 0;
						foreach ( $billResult as $r) {

							$orderTotal[$i] = $r->totalPrice;
							$i++;

						}

						$table_name = $wpdb->base_prefix . 'projects_details';

						$q = $wpdb->prepare("UPDATE $table_name SET
										orderTotal=%d
									WHERE id=%d
						;", array_sum($orderTotal), $bill_id
						);

						$wpdb->query($q);
						self::commit_transaction();

					} else {

						self::rollback_transaction();
						return wp_send_json( "Please fill stock of this product." );

					}

				}		
				
			}

			return $request;

		}
		

		public static function updateorder( $existingItems=array() ){			
			/**
			* @global wpdb $wpdb WordPress database abstraction object.
			*/
			self::authorize_ajax_mutation();

			global $wpdb;

			$table_name = $wpdb->base_prefix . 'product_variants';
			self::begin_transaction();

			$productId = !empty($existingItems) ? $_POST['productid'] : $_POST['product'];
			$product_id = isset( $_POST['productid'] ) ? (int) wp_unslash( $_POST['productid'] ) : (int) $productId;
			$bill_id = isset( $_POST['billid'] ) ? (int) wp_unslash( $_POST['billid'] ) : 0;

			//echo "ORDER ID:" . $_POST['productid'];

			//echo "PRODUCT ID:" . $_POST['product'];


			$results = $wpdb->get_results(
				$wpdb->prepare( "SELECT * FROM $table_name WHERE `variant_id` = %d FOR UPDATE;", (int) $productId )
			);

			$inHand = $results[0]->variantStock - $_POST['quantity'];

			$finalQuantity = "";

			if(!empty($_POST) && ($inHand >= 0) ){

				if( !empty($_POST['decreasedItem']) ){

					$finalQuantity = ( (int) $_POST['oldQuantity'] - (int) $_POST['decreasedItem'] );

					$inHand = $results[0]->variantStock + (int) $_POST['decreasedItem'];

					//echo "Decreased: ".$finalQuantity;
					
					self::controlQuantity($_POST['decreasedItem'], $productId, 1);


				} else if(empty($_POST['decreasedItem']) && empty($_POST['quantity'])){
					
					$finalQuantity = (int) $_POST['oldQuantity'];

					//echo "No Changed In Quantity: ".$finalQuantity;


				} else if( empty($_POST['decreasedItem']) && $_POST['quantity'] > 0 ){

					

					if(!empty($existingItems)){

						$finalQuantity = ( (int) $_POST['quantity'] + (int) $existingItems[0]->quantity );

						$inHand = $results[0]->variantStock - (int) $_POST['quantity'];

						$_POST['totalPrice'] = ( (int) $_POST['totalPrice'] + (int) $existingItems[0]->totalPrice );

						$_POST['GST'] = ( (int) $_POST['GST'] + (int) $existingItems[0]->GST );

						//echo "Increased: ".$finalQuantity;

						//print_r($existingItems);

						//print_r($_POST);


					} else {

						$finalQuantity = ( (int) $_POST['quantity'] + (int) $_POST['oldQuantity'] );

						self::controlQuantity($_POST['quantity'], $productId, 2);

					}

				}

			
				
				$table_name = $wpdb->base_prefix . 'project_order';

				$getItemId = $existingItems[0]->id != 0 ? !is_object($existingItems[0]->id) ? $existingItems[0]->id : $_POST['product'] : $_POST['productid'];

				$q = $wpdb->prepare("UPDATE $table_name SET
									supplierName=%s,
									materialsName=%s,
									quantity=%d,
									GST=%s,
									totalPrice=%s,
									discount=%s
								WHERE id=%d
				;", $_POST['supplierName'], $_POST['materialsName'], $finalQuantity, 
					$_POST['GST'], $_POST['totalPrice'], $_POST['discount'], $getItemId
				);

				$wpdb->query($q);

				$table_name = $wpdb->base_prefix . 'product_variants';

				$q = $wpdb->prepare("UPDATE $table_name SET
								variantStock=%d
							WHERE variant_id=%d
				;", $inHand, $product_id
				);
				$wpdb->query($q);


				$table_name = $wpdb->base_prefix . 'project_order';

				$billResult = $wpdb->get_results(
					$wpdb->prepare( "SELECT * FROM $table_name WHERE `billid` = %d;", $bill_id )
				);

				$orderTotal = [];
				$i = 0;
				foreach ( $billResult as $r) {

					$orderTotal[$i] = $r->totalPrice;
					$i++;

				}

				$table_name = $wpdb->base_prefix . 'projects_details';

				$q = $wpdb->prepare("UPDATE $table_name SET
								orderTotal=%d
							WHERE id=%d
				;", array_sum($orderTotal), $bill_id
				);

				$wpdb->query($q);
				self::commit_transaction();

			} else {

				self::rollback_transaction();
				return wp_send_json( "Please fill stock of this product." );

			}

		}

		public static function controlQuantity($quantityChange=null, $productid=null, $change=0){
			/**
			* @global wpdb $wpdb WordPress database abstraction object.
			*/
			global $wpdb;


			$table_name = $wpdb->base_prefix . 'product_variants';

			$results = $wpdb->get_results(
				$wpdb->prepare( "SELECT * FROM $table_name WHERE `variant_id` = %d;", (int) $productid )
			);

			if( $change === 1 ){

				$inHand = ($results[0]->variantStock + (int) $quantityChange );

			} else if ( $change === 2 ) {
				
				$inHand = ($results[0]->variantStock - (int) $quantityChange );

			} else {
				//echo "change none: ".$inHand . "\n\r";
				return;
			}

			//echo "final Quantity: ".$inHand . "\n\r";

			$q = $wpdb->prepare("UPDATE $table_name SET
							variantStock=%d
						WHERE variant_id=%d
			;", $inHand, $productid
			);

			//echo $q;

			$wpdb->query($q);

		}

		public static function getOrderDetail() {

			self::authorize_ajax_mutation();

			$billId = $_REQUEST['order'];

			if(isset($_REQUEST['orderDelete'])){
				$orderId = $_REQUEST['orderDelete'];
			}

			if(isset($_REQUEST['orderItem'])){
				$orderItemId = $_REQUEST['orderItem'];
			}
			
			if(! empty( $_REQUEST['action'] )){
				
				if(sanitize_text_field($_REQUEST['action']) === 'trash'){
					
					SELF::deleteorder($billId, $orderId);

				}

				if(sanitize_text_field($_REQUEST['action']) === 'save'){
					SELF::saveorder();
				}

				if(sanitize_text_field($_REQUEST['action']) === 'edit'){
					SELF::getorderById($billId, $orderItemId);
				}
				
			}

			/**
			* @global wpdb $wpdb WordPress database abstraction object.
			*/
			global $wpdb;
			$table_name = $wpdb->base_prefix . 'project_order';
			
			$results = $wpdb->get_results(
				"SELECT * FROM $table_name WHERE `billid` = $billId ORDER BY `ID` DESC;"
			);

			$_REQUEST["orderDeails"] = $results;

			require_once self::$admin_view_path . 'order-details.php';
			
		}

		public static function updateOrderConfirmation($billId=null, $process=false){
			/**
			* @global wpdb $wpdb WordPress database abstraction object.
			*/
			global $wpdb;
			
			
			self::authorize_ajax_mutation();

			$billId = $_REQUEST['order'];
			$process = $_REQUEST['process'];

			if( $process == true ){
			
				$table_name = $wpdb->base_prefix . 'project_order';

				$q = $wpdb->prepare("UPDATE $table_name SET
							processed=%d
						WHERE id=%d
				;", true, $billId
				);

				$wpdb->query($q);

			} else {

				$table_name = $wpdb->base_prefix . 'projects_details';
				$q = $wpdb->prepare("UPDATE $table_name SET
							confirmed=%d
						WHERE id=%d
				;", true, $billId
				);

				$wpdb->query($q);
			}
			

		}

		public static function wer_pkOrderEmail() {

			self::authorize_ajax_mutation();

			$billId = $_REQUEST['order'];

			/**
			* @global wpdb $wpdb WordPress database abstraction object.
			*/
			global $wpdb;
			$table_name = $wpdb->base_prefix . 'project_order';
			
			$results = $wpdb->get_results(
				"SELECT * FROM $table_name WHERE `billid` = $billId ORDER BY `id` DESC;"
			);
			
			$i = 0;
			$isProduct;
			$usersId = array();
			$finalUsersIds = array();

			foreach ( $results as $res ) {

				//$orderDetails[$i] = SELF::getSuppliersItems($res->productid);

				//$isProduct = $orderDetails[$i]->storeId;
				$isProduct = $res->supplierName;
				$usersId[$i] = $isProduct; 

				$i++;

			}

			

			$usersOrders = array_unique($usersId);

			$finalOrders = array();
			$multiOrders = array();
			$x = 0;

			if( count($usersOrders) > 1 ){
				
				foreach ($usersOrders as $orders) {

					//$finalOrders[$x] = json_decode( json_encode( self::checkUserExists($orders, $results) ), true );
					$finalOrders[$x] = self::checkUserExists($orders, $results);
					$x++;

				}
				
			} else {

				//$finalOrders[$x] = json_decode( json_encode($results), true );
				$finalOrders[$x] = $results;

			}

			$i = 0;
			$getUserDetails = array();
			$key = array();
			$message = array();

			//require_once plugin_dir_path(WP_WER_PK_PLUGIN_FILE) . 'classes\class-email.php';
			require_once __DIR__ . '/../classes\class-email.php';

			$iniMail = new wer_pkeMail();

			$x = 0;
			$fixOrder = array();
			$fixOrder = array_values($finalOrders);
			$user = array();
			$headers = array('Content-Type: text/html; charset=UTF-8');

			foreach ($fixOrder as $a) {
			
				if( COUNT( (array) $a ) > 1  ){
						
						//print_r($a[$x]->supplierName) . "\n\r";
				    	//$user[$x] = get_user_by("id", $a[$x]["supplierName"]);
						$user[$x] = get_user_by("id", $a[$x]->supplierName);
						
						//print_r($user[$x]);

					
						wp_mail( $user[$x]->data->user_email,
							strtoupper($user[$x]->data->display_name) . " You've recieved an order from " . get_bloginfo('name'),
							$iniMail->wrap_message( get_bloginfo('name'), $a ), 
							$headers
						);
					
				} else {
					
					//print_r($a);
					//print_r($a[$x]->supplierName) . "\n\r";
					//$user[$x] = get_user_by("id", $a[$x]["supplierName"]);
					$user[$x] = get_user_by("id", $a[$x]->supplierName);
					
					
					wp_mail( $user[$x]->data->user_email,
						strtoupper($user[$x]->data->display_name) . " You've recieved an order from " . get_bloginfo('name'),
						$iniMail->wrap_message( get_bloginfo('name'), $a, ), 
						$headers
					);
					
					
				}
				
				$x++;

			}

			self::updateOrderConfirmation($billId, false);
			

			return wp_send_json( "Email send..." );
			
		}

		public static function checkUserExists( $user, $arrayToFind ){
		
			$userOrders = array();
			$i = 0;
			foreach($arrayToFind as $find){

				//print_r($find);

				if( $find->supplierName === $user ){

					$userOrders[$i] = $find;

				}

				$i++;

			}

			return $userOrders;
			
		}

		public static function getSuppliers() {
			global $wpdb;

			$table_name = $wpdb->base_prefix . 'users';

			$suppliers = $wpdb->get_results(
				"SELECT * FROM $table_name WHERE `ID` != 1 OR `user_nicename` != 'admin' OR `display_name` != 'admin';"
			);

			return $suppliers;

		}

		public static function getSuppliersItems($supplier=null) {
			global $wpdb;

			$table_name = $wpdb->base_prefix . 'products';
			$product_variants_table = $wpdb->base_prefix . 'product_variants';
			$variant_attributes_table = $wpdb->base_prefix . 'variant_attributes';
			$product_attributes_table = $wpdb->base_prefix . 'product_attributes';

			$query = "";

			$supplierName = isset( $_REQUEST["supplierName"] ) ? $_REQUEST["supplierName"] : "";

			$materials = [];

			if( (! $supplierName || !empty( $supplierName ) ) && ! empty( $supplier ) ){

				$query = "SELECT 
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
							{$table_name} p
						LEFT JOIN 
							{$product_variants_table} v ON p.id = v.product_id
						LEFT JOIN 
							{$variant_attributes_table} va ON v.variant_id = va.variant_id
						LEFT JOIN 
							{$product_attributes_table} pa ON va.attribute_id = pa.attribute_id
						WHERE 
							p.storeId = $supplier  -- Replace ? with the desired storeId
						GROUP BY 
							p.id, v.variant_id
						ORDER BY 
							p.id, v.variant_id;";

				$materials = $wpdb->get_results( $wpdb->prepare( $query, (int) $supplier ) );

				$supplier = "";

				return $materials[0];

			}

			if( $_REQUEST["supplierName"] != "Please Select A Supplier." && empty( $supplier ) ) {
				
				$query = "SELECT 
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
							{$table_name} p
						LEFT JOIN 
							{$product_variants_table} v ON p.id = v.product_id
						LEFT JOIN 
							{$variant_attributes_table} va ON v.variant_id = va.variant_id
						LEFT JOIN 
							{$product_attributes_table} pa ON va.attribute_id = pa.attribute_id
						WHERE 
							p.storeId = %d
						GROUP BY 
							p.id, v.variant_id
						ORDER BY 
							p.id, v.variant_id;";
				
				$materials = $wpdb->get_results( $wpdb->prepare( $query, isset( $_REQUEST['supplierName'] ) ? (int) wp_unslash( $_REQUEST['supplierName'] ) : 0 ) );

			}

			return wp_send_json( $materials );

		}

		public static function getSuppliersItemsPrice($itemId=null) {
			global $wpdb;

			$table_name = $wpdb->base_prefix . 'products';
			$product_variants_table = $wpdb->base_prefix . 'product_variants';
			$variant_attributes_table = $wpdb->base_prefix . 'variant_attributes';
			$product_attributes_table = $wpdb->base_prefix . 'product_attributes';

			$materials_id = isset( $_REQUEST['materialsName'] ) ? (int) wp_unslash( $_REQUEST['materialsName'] ) : 0;

			$result = $wpdb->get_results(
				$wpdb->prepare( "SELECT 
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
							{$table_name} p
						LEFT JOIN 
							{$product_variants_table} v ON p.id = v.product_id
						LEFT JOIN 
							{$variant_attributes_table} va ON v.variant_id = va.variant_id
						LEFT JOIN 
							{$product_attributes_table} pa ON va.attribute_id = pa.attribute_id
						WHERE 
							v.variant_id = %d
						GROUP BY 
							p.id, v.variant_id
						ORDER BY 
							p.id, v.variant_id", $materials_id )
			);

			wp_send_json( $result[0] );

		}

		public static function getorderById($orderId=null, $orderItemId=null){
			/**
			* @global wpdb $wpdb WordPress database abstraction object.
			*/
			global $wpdb;

			$table_name = $wpdb->base_prefix . 'projects_details';

			$result = $wpdb->get_results(
				"SELECT * FROM $table_name WHERE `id` = $orderId;"
			);

			$resultEdit = $result[0];

			$table_name = $wpdb->base_prefix . 'project_order';

			$resultItem = $wpdb->get_results(
				"SELECT * FROM $table_name WHERE `id` = $orderItemId;"
			);

			$resultEdit = $resultItem[0];

			$editing = true;
			

			if($editing){
				
				$data['url-add_edit'] = admin_url('admin.php?page=wp_wer_pk_OrderDetail&order='.$orderId.'&orderItem='.$orderItemId.'&action=edit');
				$data['editItem'] = $resultEdit;
				$data['editing'] = $editing;
			
				if(!empty($_POST)){
					/*
					$q = $wpdb->prepare("UPDATE $table_name SET
										site_name=%s,
										site_size=%d,
										site_location=%s,
										start_date=%s,
										status=%s
									WHERE id=%d
					;", $_POST['order_name'], $_POST['size'],
							$_POST['location'], $_POST['start_date'], $_POST['status'] == "true" ? 1 : 0,
							$orderId
					);

					$wpdb->query($q);
					*/
				}

			} else {

				$this->editing = false;
				$data['editing'] = $this->editing;
				$data['url-add_edit'] = admin_url('admin.php?page=wp_wer_pk_OrderDetail&order='.$orderId.'&action=save');
				saveorder();

			}

			$suppliers = self::getSuppliers();

			//require_once $this->admin_view_path . 'order-details-form.php';
			//require_once plugin_dir_path(WP_WER_PK_PLUGIN_FILE) . 'adminpages/views/orders_detail/order-details-form.php';
			require_once __DIR__ . '/../adminpages/views/orders_detail/order-details-form.php';

		}

		public static function deleteorder($billId=null, $orderId=null){

			self::authorize_ajax_mutation();

			/**
			* @global wpdb $wpdb WordPress database abstraction object.
			*/
			global $wpdb;
			$table_name = $wpdb->base_prefix . 'project_order';

			$result = $wpdb->get_results(
					$wpdb->prepare( "SELECT * FROM $table_name WHERE `id` = %d;", (int) $orderId )
			);

			if(!empty($result)){
				
				$table_name = $wpdb->base_prefix . 'product_variants';

				$productid = $result[0]->productid;

				$productResult = $wpdb->get_results(
					$wpdb->prepare( "SELECT * FROM $table_name WHERE `variant_id` = %d;", (int) $productid )
				);

				$reverseQuantity = $result[0]->quantity + $productResult[0]->variantStock;

				$q = $wpdb->prepare("UPDATE $table_name SET
								variantStock=%d
							WHERE variant_id=%d
				;", $reverseQuantity, $result[0]->productid
				);
				$wpdb->query($q);
			
				$table_name = $wpdb->base_prefix . 'project_order';

				$results = $wpdb->delete($table_name, array('id' => $orderId));

				$billResult = $wpdb->get_results(
					"SELECT * FROM $table_name WHERE `billid` = $billId;"
				);

				$orderTotal = [];
				$i = 0;
				foreach ( $billResult as $r) {

					$orderTotal[$i] = $r->totalPrice;
					$i++;

				}

				$table_name = $wpdb->base_prefix . 'projects_details';

				$q = $wpdb->prepare("UPDATE $table_name SET
										orderTotal=%d
									WHERE id=%d
						;", array_sum($orderTotal), $billId
						);
						$wpdb->query($q);

			}


			return;
			
		}

		/**
		 * Load settings page content.
		 */
		public static function OrderDetail_page() {

			$data['url-add_edit'] = admin_url('admin.php?page=wp_wer_pk_order_detail&action=save');

			/*
			echo self::$admin_view_path . "ASad";
			require_once self::$admin_view_path . 'order-details-form.php';	
			self::getOrderDetail();
			*/

			/**
			* @global wpdb $wpdb WordPress database abstraction object.
			*/
			global $wpdb;
			$orderId = $_REQUEST['order'];

			if(empty( $_REQUEST['action'] )){

			
				$table_name = $wpdb->base_prefix . 'projects_details';

				$result = $wpdb->get_results(
					$wpdb->prepare( "SELECT * FROM $table_name WHERE `id` = %d;", (int) $orderId )
				);

				$suppliers = self::getSuppliers();

				//$resultEdit = $result[0];
				
				require_once self::$admin_view_path . 'order-details-form.php';	
				self::getOrderDetail();

			} else if(sanitize_text_field($_REQUEST['action']) === 'edit') {
				
				self::getOrderDetail();

				require_once self::$admin_view_path . 'order-details-form.php';


			} else {
				$suppliers = self::getSuppliers();

			$table_name = $wpdb->base_prefix . 'projects_details';

				$result = $wpdb->get_results(
					$wpdb->prepare( "SELECT * FROM $table_name WHERE `id` = %d;", (int) $orderId )
				);
				
				require_once self::$admin_view_path . 'order-details-form.php';	
				self::getOrderDetail();

			}

		}

		public static function hasOrders($billId=null) {

			/**
			* @global wpdb $wpdb WordPress database abstraction object.
			*/
			global $wpdb;
			$table_name = $wpdb->base_prefix . 'project_order';
			
			$results = $wpdb->get_results(
				"SELECT billid FROM $table_name WHERE `billid` = $billId;"
			);

			return count($results );
			
		}

		public static function getCurrentUserOrders() {
			/**
			* @global wpdb $wpdb WordPress database abstraction object.
			*/
			global $wpdb, $current_user;
			$orderStatus;
			$orderProcessed;

			if($_REQUEST["orderStatus"] == 1){
				
				//echo "Confirmed";
				$orderStatus = 1;
				$orderProcessed = 0;

			} else if($_REQUEST["orderStatus"] == 2){
				
				//echo "Pendng";
				$orderStatus = 0;
				$orderProcessed = 0;

			} else if($_REQUEST["orderStatus"] == 3){
				
				//echo "Processed";
				$orderStatus = 1;
				$orderProcessed = 1;
				

			} else {
				$orderStatus = 0;
				$orderProcessed = 0;
			}

			$billId = $current_user->ID;

			$table_name = $wpdb->base_prefix . 'project_order';
			$projects_details_table = $wpdb->base_prefix . 'projects_details';
			
			if($_REQUEST["orderStatus"] == 4){
				
				$results = $wpdb->get_results(
					"SELECT
					po.*,  -- Select the ID from project_order 
					po.id AS order_id,  -- Select the ID from project_order
					pd.*               -- Select all columns from projects_details
					FROM {$table_name} po 
					RIGHT JOIN {$projects_details_table} pd ON po.billid = pd.id
					WHERE po.supplierName = $billId
					ORDER BY pd.confirmed DESC"
				);

			} else {

				$results = $wpdb->get_results(
					"SELECT 
					po.*,  -- Select the ID from project_order 
					po.id AS order_id,  -- Select the ID from project_order
					pd.*               -- Select all columns from projects_details
					FROM {$table_name} po 
					RIGHT JOIN {$projects_details_table} pd ON po.billid = pd.id
					WHERE pd.confirmed = $orderStatus
					AND po.processed = $orderProcessed
					AND po.supplierName = $billId
					-- GROUP BY pd.id
					ORDER BY pd.confirmed DESC"
				);

			}

			$table_name = $wpdb->base_prefix . 'project_order';
			$projects_details_table = $wpdb->base_prefix . 'projects_details';

			$ordersConfirmed = $wpdb->get_results(
				"SELECT 
				po.*,  -- Select the ID from project_order 
				po.id AS order_id,  -- Select the ID from project_order
				pd.*               -- Select all columns from projects_details
				FROM {$table_name} po 
				RIGHT JOIN {$projects_details_table} pd ON po.billid = pd.id
				WHERE pd.confirmed = 1
				AND po.processed = 0
				AND po.supplierName = $billId
				--GROUP BY po.billid
				ORDER BY pd.confirmed DESC"
			);

			/* AND WHERE pd.confirmed = 1 */

			//require_once plugin_dir_path(WP_WER_PK_PLUGIN_FILE) . 'frontend\views\orders.php';
			require_once __DIR__ . '/../frontend/views/orders.php';
			//return wp_send_json($results);

		}


		/*
		Our server-side code. 
	
		This hooks into the heartbeat_received filter. 
		It checks for a key 'client' in the data array. If it is set to 'marco', a key 'server' is set to 'polo' in the response array.
		*/
		public static function hbdemo_heartbeat_received($response, $data) {
	
			global $wpdb, $current_user;

			$billId = $current_user->ID;
			$table_name = $wpdb->base_prefix . 'project_order';
			$projects_details_table = $wpdb->base_prefix . 'projects_details';

			$results = $wpdb->get_results(
				"SELECT 
				po.*,  -- Select the ID from project_order 
				po.id AS order_id,  -- Select the ID from project_order
				pd.*               -- Select all columns from projects_details
				FROM {$table_name} po 
				RIGHT JOIN {$projects_details_table} pd ON po.billid = pd.id
				WHERE pd.confirmed = 1
				AND po.processed = 0
				AND po.supplierName = $billId
				-- GROUP BY pd.id
				ORDER BY pd.confirmed DESC"
			);

			if($data['client'] == 'marco')
				$response['confirmedOrders'] = count($results);
	
			//return wp_send_json($response['server'][0]->totalOrders);
			return $response;
		}


		public static function getOrdersData(){
			global $wpdb;

			$table_name = $wpdb->prefix . 'project_order'; // Replace with your actual table name
			$query = "SELECT COUNT(*) as total, SUM(processed) as processed_count FROM $table_name";
			$result = $wpdb->get_row($query, ARRAY_A);


			$total = $result['total'];
			$processed_count = $result['processed_count'];
			$pending_count = $total - $processed_count;

			$processed_percentage = ($total > 0) ? ($processed_count / $total) * 100 : 0;
			$pending_percentage = ($total > 0) ? ($pending_count / $total) * 100 : 0;

			$data = [
						[
							'y' => round($processed_percentage, 2),
							'name' => 'Processed'
						],
						[
							'y' => round($pending_percentage, 2),
							'name' => 'Pending'
						]
					];

			// Optionally, add 'exploded' for specific conditions
			foreach ($data as &$item) {
				if ($item['name'] === 'Processed') {
					$item['exploded'] = true;
				}
			}

			// Return the results as JSON
			return wp_send_json($data);

		}



	}

endif;	