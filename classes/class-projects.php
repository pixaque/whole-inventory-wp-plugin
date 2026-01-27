<?php

//namespace WP_WER_PK_Blocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//if ( ! class_exists( '\WP_WER_PK_Blocks\Projects', false ) ) :

if ( ! class_exists( '\Projects', false ) ) :
	/**
	 * Class Projects
	 */
	class Projects {

			/**
		 * Menu slug.
		 *
		 * @var string
		 */
		const MENU_SLUG = 'wp_wer_pk_projects';
		
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

		public static function printProjects(){
			
			/*
			 * @param none
			 * @return none
			*/
			self::$admin_view_path = plugin_dir_path(WP_WER_PK_PLUGIN_FILE) . 'adminpages/views/projects/';
			
			self::projects_page();

			//$this->register_settings();

		}

		public static function saveProject($request){		
			/**
			* @global wpdb $wpdb WordPress database abstraction object.
			*/
			global $wpdb;
			$table_name = $wpdb->base_prefix . 'projects';

			if(!empty($_POST)){

				$wpdb->insert($table_name, 
					array(
						'site_name' => $_POST['project_name'],
						'site_size' => $_POST['size'],
						'site_location' => $_POST['location'],
						'start_date' => $_POST['start_date'],
						'status' => $_POST['status'] == "true" ? 1 : 0
					)
				);

			}

			return $request;

			//$data['editing'] = $this->editing;
			//$data['editProject'] = $this->resultEdit;

		}
		

		public static function updateProject(){			
			/**
			* @global wpdb $wpdb WordPress database abstraction object.
			*/
			global $wpdb;
			$table_name = $wpdb->base_prefix . 'projects';

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

		public static function getProjects() {

			if(! empty( $_REQUEST['action'] )){
				
				if(sanitize_text_field($_REQUEST['action']) === 'trash'){
					SELF::deleteProject($_REQUEST['project']);
				}

				if(sanitize_text_field($_REQUEST['action']) === 'save'){
					SELF::saveProject();
				}

				if(sanitize_text_field($_REQUEST['action']) === 'edit'){
					SELF::getProjectById($_REQUEST['project']);
				}
				
			}

			/**
			* @global wpdb $wpdb WordPress database abstraction object.
			*/
			global $wpdb, $current_user;
			$table_name = $wpdb->base_prefix . 'projects';
			
			$results = $wpdb->get_results(
				"SELECT * FROM $table_name ORDER BY `id` DESC;"
			);

			require_once self::$admin_view_path . 'projects.php';			
			
		}

		public static function getProjectById($projectId=null){
			/**
			* @global wpdb $wpdb WordPress database abstraction object.
			*/
			global $wpdb;

			$projectId = $_REQUEST['project'];

			$table_name = $wpdb->base_prefix . 'projects';

			$result = $wpdb->get_results(
				"SELECT * FROM $table_name WHERE `id` = $projectId;"
			);

			$resultEdit = $result[0];

			$editing = true;
			

			if($editing){
				
				$data['url-add_edit'] = admin_url('admin.php?page=wp_wer_pk_projects&project='.$projectId.'&action=edit');
				$data['editProject'] = $resultEdit;
				$data['editing'] = $editing;
			
				if(!empty($_POST)){

					$q = $wpdb->prepare("UPDATE $table_name SET
										site_name=%s,
										site_size=%d,
										site_location=%s,
										start_date=%s,
										status=%s
									WHERE id=%d
					;", $_POST['project_name'], $_POST['size'],
							$_POST['location'], $_POST['start_date'], $_POST['status'] == "true" ? 1 : 0,
							$projectId
					);

					$wpdb->query($q);

				}

			} else {

				$this->editing = false;
				$data['editing'] = $this->editing;
				$data['url-add_edit'] = admin_url('admin.php?page=wp_wer_pk_projects&project='.$projectId.'&action=save');
				saveProject();

			}

			// Validate the input
			$errors = new WP_Error();
			
			//require_once $this->admin_view_path . 'projects_form.php';
			require_once plugin_dir_path(WP_WER_PK_PLUGIN_FILE) . 'adminpages/views/projects_form.php';

		}

		public static function deleteProject($projectId=null){
			
			/**
			* @global wpdb $wpdb WordPress database abstraction object.
			*/
			global $wpdb;
			$table_name = $wpdb->base_prefix . 'projects';

			$projectDetails = ProjectDetails::hasOrders($projectId);


			if(!empty($projectDetails)){
				
				echo '<div class="components-placeholder"><div class="notice notice-error">' . __( '<p><strong>Please first delete all the child orders.</strong></p>' ) . '</div></div>';

			} else {
				$results = $wpdb->delete($table_name, array('id' => $projectId));
				return;
			}

			

			//wp_redirect( admin_url( 'admin.php' ) );
			//exit;

			return;
			
		}

		/**
		 * Load settings page content.
		 */
		public static function projects_page() {

			$data['url-add_edit'] = admin_url('admin.php?page=wp_wer_pk_projects&action=save');

			if(empty( $_REQUEST['action'] )){
				
				require_once self::$admin_view_path . 'projects_form.php';	
				self::getProjects();

			} else if(sanitize_text_field($_REQUEST['action']) === 'edit') {
				
				self::getProjects();
				//require_once self::$admin_view_path . 'projects_form.php';

			} else {
				
				require_once self::$admin_view_path . 'projects_form.php';	
				self::getProjects();

			}

		}

		public static function project_detail(){

			require_once plugin_dir_path(WP_WER_PK_PLUGIN_FILE) . 'classes\class-project-details.php';

			ProjectDetails::printProjectDetails();


		}

		
		

	}

endif;	