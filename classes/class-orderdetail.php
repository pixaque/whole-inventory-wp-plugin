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
			global $wpdb;

			$table_name = $wpdb->base_prefix . 'project_order';

			$billResult = $wpdb->get_results(
				"SELECT * FROM $table_name WHERE `billid` = $_POST[billid];"
			);

			$totalCount = array();

			foreach($billResult as $s){
				
				if($_POST['materialsName'] === $s->materialsName){
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
			global $wpdb;
			
			$existingItems = array();

			if( !empty( $_POST ) ){

				$existingItems = self::checkExistingProducts();

				$table_name = $wpdb->base_prefix . 'product_variants';

				$results = $wpdb->get_results(
					"SELECT * FROM $table_name WHERE `variant_id` = $_POST[productid];"
				);

				$inHand = $results[0]->variantStock - $_POST['quantity'];

				if( !empty( $existingItems)  && ($inHand >= 0) ){
					
					//echo "found record.";
					//print_r($existingItems);

					self::updateorder($existingItems);


				} else {

					if( $inHand >= 0 ){

						$table_name = $wpdb->base_prefix . 'project_order';

						$wpdb->insert($table_name, 
							array(
								'billid' => $_POST['billid'],
								'supplierName' => $_POST['supplierName'],
								'materialsName' => $_POST['materialsName'],
								'discount' => $_POST['discount'],
								'quantity' => $_POST['quantity'],
								'GST' => $_POST['GST'],
								'totalPrice' => $_POST['totalPrice'],
								'productid' => $_POST['productid']
							)
						);

						$table_name = $wpdb->base_prefix . 'product_variants';

						$q = $wpdb->prepare("UPDATE $table_name SET
										variantStock=%d
									WHERE variant_id=%d
						;", $inHand, $_POST['productid']
						);
						$wpdb->query($q);


						$table_name = $wpdb->base_prefix . 'project_order';

						$billResult = $wpdb->get_results(
							"SELECT * FROM $table_name WHERE `billid` = $_POST[billid];"
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
						;", array_sum($orderTotal), $_POST["billid"]
						);

						$wpdb->query($q);

					} else {

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
			global $wpdb;

			$table_name = $wpdb->base_prefix . 'product_variants';

			$productId = !empty($existingItems) ? $_POST['productid'] : $_POST['product'];

			//echo "ORDER ID:" . $_POST['productid'];

			//echo "PRODUCT ID:" . $_POST['product'];


			$results = $wpdb->get_results(
				"SELECT * FROM $table_name WHERE `variant_id` = $productId;"
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
				;", $inHand, $_POST['productid']
				);
				$wpdb->query($q);


				$table_name = $wpdb->base_prefix . 'project_order';

				$billResult = $wpdb->get_results(
					"SELECT * FROM $table_name WHERE `billid` = $_POST[billid];"
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
				;", array_sum($orderTotal), $_POST['billid']
				);

				$wpdb->query($q);


			} else {

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
				"SELECT * FROM $table_name WHERE `variant_id` = $productid;"
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
						echo "multi";
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
					echo "single";
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
							wp_products p
						LEFT JOIN 
							wp_product_variants v ON p.id = v.product_id
						LEFT JOIN 
							wp_variant_attributes va ON v.variant_id = va.variant_id
						LEFT JOIN 
							wp_product_attributes pa ON va.attribute_id = pa.attribute_id
						WHERE 
							p.storeId = $supplier  -- Replace ? with the desired storeId
						GROUP BY 
							p.id, v.variant_id
						ORDER BY 
							p.id, v.variant_id;";

				$materials = $wpdb->get_results( $query );

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
							wp_products p
						LEFT JOIN 
							wp_product_variants v ON p.id = v.product_id
						LEFT JOIN 
							wp_variant_attributes va ON v.variant_id = va.variant_id
						LEFT JOIN 
							wp_product_attributes pa ON va.attribute_id = pa.attribute_id
						WHERE 
							p.storeId = $_REQUEST[supplierName]  -- Replace ? with the desired storeId
						GROUP BY 
							p.id, v.variant_id
						ORDER BY 
							p.id, v.variant_id;";
				
				$materials = $wpdb->get_results( $query );

			}

			return wp_send_json( $materials );

		}

		public static function getSuppliersItemsPrice($itemId=null) {
			global $wpdb;

			$table_name = $wpdb->base_prefix . 'products';

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
							v.variant_id = $_REQUEST[materialsName]  -- Replace ? with the desired storeId
						GROUP BY 
							p.id, v.variant_id
						ORDER BY 
							p.id, v.variant_id"
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

			/**
			* @global wpdb $wpdb WordPress database abstraction object.
			*/
			global $wpdb;
			$table_name = $wpdb->base_prefix . 'project_order';

			$result = $wpdb->get_results(
					"SELECT * FROM $table_name WHERE `id` = $orderId;"
			);

			if(!empty($result)){
				
				$table_name = $wpdb->base_prefix . 'product_variants';

				$productid = $result[0]->productid;

				$productResult = $wpdb->get_results(
					"SELECT * FROM $table_name WHERE `variant_id` = $productid;"
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
					"SELECT * FROM $table_name WHERE `id` = $orderId;"
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
					"SELECT * FROM $table_name WHERE `id` = $orderId;"
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
			
			if($_REQUEST["orderStatus"] == 4){
				
				$results = $wpdb->get_results(
					"SELECT
					wp_project_order.*,  -- Select the ID from wp_project_order 
					wp_project_order.id AS order_id,  -- Select the ID from wp_project_order
					wp_projects_details.*               -- Select all columns from wp_projects_details
					FROM $table_name 
					RIGHT JOIN `wp_projects_details` ON `wp_project_order`.`billid` = `wp_projects_details`.`id`
					WHERE `wp_project_order`.`supplierName` = $billId
					ORDER BY `wp_projects_details`.`confirmed` DESC"
				);

			} else {

				$results = $wpdb->get_results(
					"SELECT 
					wp_project_order.*,  -- Select the ID from wp_project_order 
					wp_project_order.id AS order_id,  -- Select the ID from wp_project_order
					wp_projects_details.*               -- Select all columns from wp_projects_details
					FROM $table_name 
					RIGHT JOIN `wp_projects_details` ON `wp_project_order`.`billid` = `wp_projects_details`.`id`
					WHERE `wp_projects_details`.`confirmed` = $orderStatus
					AND `wp_project_order`.`processed` = $orderProcessed
					AND `wp_project_order`.`supplierName` = $billId
					-- GROUP BY `wp_projects_details`.`id`
					ORDER BY `wp_projects_details`.`confirmed` DESC"
				);

			}

			$table_name = $wpdb->base_prefix . 'project_order';

			$ordersConfirmed = $wpdb->get_results(
				"SELECT 
				wp_project_order.*,  -- Select the ID from wp_project_order 
				wp_project_order.id AS order_id,  -- Select the ID from wp_project_order
				wp_projects_details.*               -- Select all columns from wp_projects_details
				FROM $table_name 
				RIGHT JOIN `wp_projects_details` ON `wp_project_order`.`billid` = `wp_projects_details`.`id`
				WHERE `wp_projects_details`.`confirmed` = 1
				AND `wp_project_order`.`processed` = 0
				AND `wp_project_order`.`supplierName` = $billId
				--GROUP BY `wp_projects_order`.`billid`
				ORDER BY `wp_projects_details`.`confirmed` DESC"
			);

			/* AND WHERE `wp_projects_details`.`confirmed` = 1 */

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

			$results = $wpdb->get_results(
				"SELECT 
				wp_project_order.*,  -- Select the ID from wp_project_order 
				wp_project_order.id AS order_id,  -- Select the ID from wp_project_order
				wp_projects_details.*               -- Select all columns from wp_projects_details
				FROM $table_name 
				RIGHT JOIN `wp_projects_details` ON `wp_project_order`.`billid` = `wp_projects_details`.`id`
				WHERE `wp_projects_details`.`confirmed` = 1
				AND `wp_project_order`.`processed` = 0
				AND `wp_project_order`.`supplierName` = $billId
				-- GROUP BY `wp_projects_details`.`id`
				ORDER BY `wp_projects_details`.`confirmed` DESC"
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