<?php

//namespace WP_WER_PK_Blocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//if ( ! class_exists( '\WP_WER_PK_Blocks\ProjectDetails', false ) ) :

if ( ! class_exists( '\ProjectDetails', false ) ) :
	/**
	 * Class ProjectDetails
	 */
	class ProjectDetails {

			/**
		 * Menu slug.
		 *
		 * @var string
		 */
		const MENU_SLUG = 'wp_wer_pk_blocks_ProjectDetails';
		
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

		public static function printProjectDetails(){
			
			/*
			 * @param none
			 * @return none
			*/
			self::$admin_view_path = plugin_dir_path(WP_WER_PK_PLUGIN_FILE) . 'adminpages/views/project_detail/';
			
			require_once plugin_dir_path(__FILE__) . '/class-wp-project-detail-list-table.php';

			self::ProjectDetails_page();

			//$this->register_settings();

		}

		public static function saveProjectOrder($request){		
			/**
			* @global wpdb $wpdb WordPress database abstraction object.
			*/
			global $wpdb;
			$table_name = $wpdb->base_prefix . 'projects_details';

			if(!empty($_POST)){

				$wpdb->insert($table_name, 
					array(
						'projectid' => $_POST['projectid'],
						'billNo' => $_POST['billNo'],
						'expenseType' => $_POST['expenseType'],
						'billdate' => $_POST['billdate'],
						'description' => $_POST['description']
					)
				);

			}

			return $request;


		}
		

		public static function updateProject(){			
			/**
			* @global wpdb $wpdb WordPress database abstraction object.
			*/
			global $wpdb;
			$table_name = $wpdb->base_prefix . 'projects_details';

			return $table_name;

			if(!empty($_POST)){

				$q = $wpdb->prepare("UPDATE $table_name SET
									site_name=%s,
									site_size=%d,
									site_location=%s,
									start_date=%s,
									status=%s
								WHERE id=%d
				;", $_POST['project_name'], $_POST['size'],
						$_POST['location'], $_POST['start_date'], !isset($_POST['status']) ? false : true,
						$_POST['project_id']
				);
				$wpdb->query($q);
			}

		}

		public static function getProjectDetails() {

			$projectId = $_REQUEST['project'];
			
			if(! empty($_REQUEST['orderDelete']) ){
				$orderId = $_REQUEST['orderDelete'];
			}
			

			if(! empty( $_REQUEST['action'] )){
				
				if(sanitize_text_field($_REQUEST['action']) === 'trash'){
					SELF::deleteProject($projectId, $orderId);
				}

				if(sanitize_text_field($_REQUEST['action']) === 'save'){
					SELF::saveProject();
				}

				if(sanitize_text_field($_REQUEST['action']) === 'edit'){
					SELF::getProjectOrderById($projectId);
				}
				
			}

			/**
			* @global wpdb $wpdb WordPress database abstraction object.
			*/
			global $wpdb;
			$table_name = $wpdb->base_prefix . 'projects_details';
			
			$results = $wpdb->get_results(
				"SELECT * FROM $table_name WHERE `projectid` = $projectId ORDER BY `id` DESC;"
			);

			require_once self::$admin_view_path . 'project-details.php';
			
		}

		public static function getProjectOrderById($projectId=null){
			/**
			* @global wpdb $wpdb WordPress database abstraction object.
			*/
			global $wpdb;

			$projectId = $_REQUEST['project'];
			$projectOrder = $_REQUEST['projectOrder'];

			if(isset($_REQUEST["projectid"]) || isset($_REQUEST["orderid"])){

				$projectId = $_REQUEST['projectid'];
				$projectOrder = $_REQUEST['orderid'];

			}
			

			$table_name = $wpdb->base_prefix . 'projects_details';

			$result = $wpdb->get_results(
				"SELECT * FROM $table_name WHERE `id` = $projectOrder;"
			);


			$resultEdit = $result[0];
			
			//print_r($resultEdit);

			$editing = true;
			

			if($editing){

				
			//window.location.href = `?page=wp_wer_pk_project_detail&project=${o}&projectOrder=${i}&action=edit`;

				$data['url-add_edit'] = admin_url('admin.php?page=wp_wer_pk_project_detail&project='.$resultEdit->projectid.'&projectOrder='.$resultEdit->id.'&action=edit');
				$data['editProject'] = $resultEdit;
				$data['editing'] = $editing;
			
				if(!empty($_POST)){

					$q = $wpdb->prepare("UPDATE $table_name SET
										expenseType=%s,
										billdate=%s,
										description=%s
									WHERE id=%d;",
									$_POST['expenseType'], $_POST['billdate'],
									$_POST['description'], $projectOrder
					);

					$wpdb->query($q);


				}

			} else {

				$this->editing = false;
				$data['editing'] = $this->editing;
				$data['url-add_edit'] = admin_url('admin.php?page=wp_wer_pk_ProjectDetails&project='.$projectId.'&action=save');
				saveProject();

			}
			
			//require_once $this->admin_view_path . 'project-details-form.php';
			require_once plugin_dir_path(WP_WER_PK_PLUGIN_FILE) . 'adminpages/views/project_detail/project-details-form.php';

		}

		public static function deleteProject($projectId=null, $orderId=null){
			
			/**
			* @global wpdb $wpdb WordPress database abstraction object.
			*/
			global $wpdb;
			$table_name = $wpdb->base_prefix . 'projects_details';

			$orders = OrderDetail::hasOrders($orderId);

			if(!empty($orders)){
				
				echo '<div class="components-placeholder"><div class="notice notice-error">' . __( '<p><strong>Please first delete all the child orders.</strong></p>' ) . '</div></div>';

			} else {
				$results = $wpdb->delete($table_name, array('id' => $orderId));
				return;
			}
			
		}

		/**
		 * Load settings page content.
		 */
		public static function ProjectDetails_page() {

			$data['url-add_edit'] = admin_url('admin.php?page=wp_wer_pk_ProjectDetails&action=save');

			/*
			echo self::$admin_view_path . "ASad";
			require_once self::$admin_view_path . 'project-details-form.php';	
			self::getProjectDetails();
			*/

			if(empty( $_REQUEST['action'] )){
				
				require_once self::$admin_view_path . 'project-details-form.php';	
				self::getProjectDetails();

			} else if(sanitize_text_field($_REQUEST['action']) === 'edit') {
				
				self::getProjectDetails();
				require_once self::$admin_view_path . 'project-details-form.php';

			} else {
				
				require_once self::$admin_view_path . 'project-details-form.php';	
				self::getProjectDetails();

			}

		}


		public static function hasOrders($billId=null) {

			/**
			* @global wpdb $wpdb WordPress database abstraction object.
			*/
			global $wpdb;
			$table_name = $wpdb->base_prefix . 'projects_details';

			$billId = $_REQUEST['project'];
			
			$results = $wpdb->get_results(
				"SELECT projectid FROM $table_name WHERE `projectid` = $billId;"
			);

			return count($results );
			
		}

		public static function get_expenses_aggrigation() {
			global $wpdb;

			// Query to aggregate expenses by date where confirmed is true
			$query = "
				SELECT 
					billdate AS date,
					SUM(orderTotal) AS sale
				FROM 
					{$wpdb->base_prefix}projects_details
				WHERE 
					confirmed = 1
				GROUP BY 
					billdate
				ORDER BY 
					billdate
			";

			// Execute the query
			$results = $wpdb->get_results($query);

			// Format results as an array of objects
			$formatted_results = [];
			foreach ($results as $row) {
				$formatted_results[] = [
					'date' => $row->date,
					'sale' => floatval($row->sale), // Ensure sale is a float
				];
			}

			// Return the results as JSON
			return wp_send_json($formatted_results);
		}




	}

endif;	