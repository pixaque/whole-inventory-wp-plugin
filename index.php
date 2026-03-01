<?php
/*
  Plugin Name: All Purpose General Inventory Plugin
  Version: 1.0
  Author: Asad Ullah
  Author URI: https://github.com/nothing
*/


if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Define WP_WER_PK_PLUGIN_FILE.
if ( ! defined( 'WP_WER_PK_PLUGIN_FILE' ) ) {
	define( 'WP_WER_PK_PLUGIN_FILE', __FILE__ );
}
if ( ! defined( 'SITEURL'  ) ) {
	$urlparts = explode( '//', home_url() );
	define( 'SITEURL', $urlparts[1] );
}

class wer_pkMain {


	/**
	* Menu slug.
	*
	* @var string
	*/
	const MENU_SLUG = 'wp_wer_pk_main';

	/**
	 * The plugin assets directory.
	 *
	 * @var string
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 *
	 * @var string
	 */
	public $assets_url;

	/**
	 * The plugin languages directory.
	 *
	 * @var string
	 */

	public $languages_dir;

	
	/**
	 * The full path to the plugin languages directory.
	 *
	 * @var string
	 */
	public $languages_dir_full;

	function __construct() {
		add_action('init', array($this, 'onInit'));

		// Add settings page to menu
		add_action( 'admin_menu', array( __CLASS__, 'add_menu_item' ) );

		// Add hooks Initialization
		add_action( 'admin_init', array( __CLASS__, 'init_hooks' ) );
		
		add_action( "admin_init", array( '\Main', 'init' ) );

		$page = get_page_by_path( "login" , OBJECT );
		$page1 = get_page_by_path( "orders" , OBJECT );

		if ( isset($page) &&  isset($page1) ) {
			
		} else {
			register_activation_hook( __FILE__, array(__CLASS__, 'add_my_custom_page'));
		}
		
	}

	function onInit(){

		$this->init_plugin_environment();


		global $wer_pk_db;
		$wer_pk_db = new WER_PKDb();
		$wer_pk_db->wp_get_WER_PKdb_schema(); 


		$this->init_hooks();
		
		Settings::init( $this->assets_dir, $this->assets_url );

		if ( class_exists( 'WER_PK_MetaBox' ) ) {
			WER_PK_MetaBox::init();
		}
		
		require_once __DIR__ . '/classes/class-wer_pkShortcodes.php';

		$wer_pkShortcodes = new wer_pkShortcodes();

	}

	public static function testFunction() {
		
		echo "<pre>";
		print_r( $_REQUEST["orderDeails"] );
		echo "</pre>";

	}

  	/**
	 * Initializes hooks.
	 */
	public static function init_hooks() {
		add_action( 'wp_ajax_saveProject', array ( '\Projects', 'saveProject' ));
		add_action( 'wp_ajax_getProjectById', array ( '\Projects', 'getProjectById' ));

		add_action( 'wp_ajax_saveProjectOrder', array ( '\ProjectDetails', 'saveProjectOrder' ));
		add_action( 'wp_ajax_getProjectOrderById', array ( '\ProjectDetails', 'getProjectOrderById' ));

		add_action( 'wp_ajax_get_expenses_aggrigation', array ( '\ProjectDetails', 'get_expenses_aggrigation' ));

		add_action( 'wp_ajax_getSuppliersItems', array ( '\OrderDetail', 'getSuppliersItems' ));
		add_action( 'wp_ajax_getSuppliersItemsPrice', array ( '\OrderDetail', 'getSuppliersItemsPrice' ));
		add_action( 'wp_ajax_saveorder', array ( '\OrderDetail', 'saveorder' ));
		add_action( 'wp_ajax_updateorder', array ( '\OrderDetail', 'updateorder' ));

		add_action( 'wp_ajax_getOrdersData', array ( '\OrderDetail', 'getOrdersData' ));

		add_action( 'wp_ajax_updateOrderConfirmation', array ( '\OrderDetail', 'updateOrderConfirmation' ));

		add_action( 'wp_ajax_wer_pkOrderEmail', array ( '\OrderDetail', 'wer_pkOrderEmail' ));

		add_filter('heartbeat_received', array ( '\OrderDetail', 'hbdemo_heartbeat_received' ), 10, 2);
		add_filter( 'heartbeat_nopriv_received', array ( '\OrderDetail', 'hbdemo_heartbeat_received' ), 10, 2 );

		add_filter('order_init', array ( __CLASS__, 'testFunction' ), 10, 2);

		add_action( 'wp_ajax_saveProduct', array ( '\Products', 'saveProduct' ));
		add_action( 'wp_ajax_deleteProduct', array ( '\Products', 'deleteProduct' ));
		add_action( 'wp_ajax_getproductById', array ( '\Products', 'getproductById' ));
		add_action( 'wp_ajax_updateProduct', array ( '\Products', 'updateProduct' ));
		
		add_action( 'wp_ajax_getSelectedVariantData', array ( '\Products', 'getSelectedVariantData' ));

		add_action( 'wp_ajax_savePVariant', array ( '\Products', 'savePVariant' ));
		add_action( 'wp_ajax_deletePVariant', array ( '\Products', 'deletePVariant' ));
		add_action( 'wp_ajax_editPVariant', array ( '\Products', 'editPVariant' ));
		add_action( 'wp_ajax_getPVariantById', array ( '\Products', 'getPVariantById' ));
		add_action( 'wp_ajax_deletePVariantById', array ( '\Products', 'deletePVariantById' ));

		add_action( 'wp_ajax_empty_and_populate_tables', array ( '\Settings', 'empty_and_populate_tables' ));

		add_action( 'wp_ajax_reset_wer_pk_tables', array ( '\Settings', 'reset_wer_pk_tables' ));
		

	}

	/**
	* Add settings page to admin menu.
	*/
	public static function add_menu_item() {
			
		add_menu_page(
			__( 'Manager Dashboard', 'wer_pk' ), 
			__( 'Manager Dashboard', 'wer_pk' ), 
			'manage_options', 
			"wp_wer_pk_main", 
			array( "\Main", 'page_cb' ),
			"dashicons-welcome-edit-page" 
		);

		add_submenu_page( 
			self::MENU_SLUG, 
			__( 'Record Management', 'wer_pk' ), 
			__( 'Record Management', 'wer_pk' ), 
			'manage_options',
			"wp_wer_pk_projects", 
			array( "\Projects", 'printProjects')
		);

		add_submenu_page( 
			"wp_wer_pk_projects", 
			__( 'Record Management Orders', 'wer_pk' ), 
			__( 'Record Management Orders', 'wer_pk' ), 
			'manage_options',
			"wp_wer_pk_project_detail", 
			array( "\ProjectDetails", 'printProjectDetails')
		);

		add_submenu_page( 
			"wp_wer_pk_projects", 
			__( 'Record Management Orders Details', 'wer_pk' ), 
			__( 'Record Management Orders Details', 'wer_pk' ), 
			'manage_options',
			"wp_wer_pk_order_detail", 
			array( "\OrderDetail", 'printOrderDetail')
		);
		
		add_submenu_page( 
			self::MENU_SLUG, 
			__( 'Inventory Products/Items', 'wer_pk' ), 
			__( 'Inventory Products/Items', 'wer_pk' ), 
			'manage_options',
			"wp_wer_pk_products", 
			array( "\Products", 'getProducts')
		);

		add_submenu_page( 
			self::MENU_SLUG, 
			__( 'We\'r pk Settings', 'wer_pk' ), 
			__( 'We\'r pk Settings', 'wer_pk' ), 
			'manage_options',
			"wp_wer_pk_settings", 
			array( "\Settings", 'settings_page')
		);
			
	}

	/**
	 * Initializes plugin environment variables
	 */
	public function init_plugin_environment() {
	

		// Register script.
		wp_register_script(
			'wer_pk-script',
			plugin_dir_url( WP_WER_PK_PLUGIN_FILE ) . 'adminpages/assets/wer_pk-script.js',
			array(),
			1.1,
			array('strategy' => 'async', 'in_footer' => true)
		);
		// Enqueue theme Script.
		wp_enqueue_script( 'wer_pk-script' );

		// Pass WordPress AJAX URL to JS
		wp_localize_script('wer_pk-script', 'ajax_object', [
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('wer_pk_ajax_nonce'),
		]);

		
			
		// Register script.
		wp_register_script(
			'wer_pk-canvasjs-script',
			plugin_dir_url( WP_WER_PK_PLUGIN_FILE ) . 'adminpages/assets/charts/jquery.canvasjs.stock.min.js',
			array(),
			1.1,
			array('strategy' => 'async', 'in_footer' => true)
		);
		// Enqueue theme Script.
		wp_enqueue_script( 'wer_pk-canvasjs-script' );

		// Register script.
		wp_register_style(
			'wer_pk-style',
			plugin_dir_url( WP_WER_PK_PLUGIN_FILE ) . 'adminpages/assets/main.css',
			array()
		);
		// Enqueue theme stylesheet.
		wp_enqueue_style( 'wer_pk-style' );

		// Register script.
		wp_register_style(
			'wer_pk-bootstrap-style',
			plugin_dir_url( WP_WER_PK_PLUGIN_FILE ) . 'adminpages/assets/entireframework.min.css',
			array()
		);
		// Enqueue theme stylesheet.
		wp_enqueue_style( 'wer_pk-bootstrap-style' );

		//enqueue the Heartbeat API
		wp_enqueue_script('heartbeat');

		$this->assets_dir = get_stylesheet_directory_uri();
		//$this->assets_url = $this->assets_dir . '/inc/wp-wer_pk-blocks/build/';
		$this->assets_url = $this->assets_dir . '/build/';

		$test = spl_autoload_register( function($classname) {

			// WordPress
			$parts      = explode('\\', $classname);
			$class      = 'class-' . strtolower( array_pop($parts) );
			$folders    = strtolower( implode(DIRECTORY_SEPARATOR, $parts) );
			$wppath     = dirname(__FILE__) .  DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . $folders . $class . '.php';
	

			if(  file_exists( $wppath ) ) {

				include_once $wppath;

			}
   
		} );

		$this->languages_dir = dirname( plugin_basename( WP_WER_PK_PLUGIN_FILE ) ) . '/languages/';
		$this->languages_dir_full = plugin_dir_path( WP_WER_PK_PLUGIN_FILE ) . 'languages/';


	}

public static function add_my_custom_page() {

	$pages = array(
		'seller-account'           => array(
			'post_title'    => wp_strip_all_tags( 'Seller Account' ),
			'post_content' => '<!-- wp:shortcode -->[wer_pk_frontProducts]<!-- /wp:shortcode -->',
			'post_status'   => 'publish',
			'post_author'   => 1,
			'post_type'     => 'page',
		),
		'login'           => array(
			'post_title'    => wp_strip_all_tags( 'Login' ),
			'post_content' => '<!-- wp:shortcode -->[wer_pk_frontendLogin]<!-- /wp:shortcode -->',
			'post_status'   => 'publish',
			'post_author'   => 1,
			'post_type'     => 'page',
		),
		'registration'           => array(
			'post_title'    => wp_strip_all_tags( 'Registration' ),
			'post_content' => '<!-- wp:shortcode -->[wer_pk_frontendRegistration]<!-- /wp:shortcode -->',
			'post_status'   => 'publish',
			'post_author'   => 1,
			'post_type'     => 'page',
		),
		'orders'           => array(
			'post_title'    => wp_strip_all_tags( 'Orders' ),
			'post_content' => '<!-- wp:shortcode -->[wer_pk_frontendOrders]<!-- /wp:shortcode -->',
			'post_status'   => 'publish',
			'post_author'   => 1,
			'post_type'     => 'page',
		)
	);

	foreach ( $pages as $key => $page ) {
						
		// Insert the post into the database
		wp_insert_post( $page );

	}

}


}

$wer_pkMain = new wer_pkMain();



	global $pagenow;
		
	if ( 'wp-login.php' === $pagenow ) {

	} else if( 'index.php' === $pagenow ) {
			
		add_filter( 'register', 'registration_custom_url' );

	}

	/* Disable WordPress Admin Bar for all users except administrators */
	add_filter( 'show_admin_bar', 'restrict_admin_bar' );
 
	function restrict_admin_bar( $show ) {
		return current_user_can( 'administrator' ) ? true : false;
	}

	function registration_custom_url(){

			$registration_url = sprintf( '<a class="wp-login-register" href="%s">%s</a>', esc_url( site_url() . "/registration" ), __( 'Register' ) );

			echo $registration_url;
		
	}

	//set_option( 'users_can_register', true );
	
	
	//echo get_option( 'users_can_register' );

	function my_plugin_enqueue_styles() {
		?>
		<style>
			<?php foreach (Settings::$currencies as $key => $currency): ?>
			.dashicons-<?php echo esc_attr($key); ?>::before {
				content: "<?php echo esc_html($currency['symbol']); ?>";
				font-size: 14px !important;
				font-weight: bold !important;
			}
			<?php endforeach; ?>
		</style>
		<?php
	}

	add_action('wp_head', 'my_plugin_enqueue_styles');


	// Enqueue in admin area
add_action('admin_enqueue_scripts', 'my_plugin_enqueue_admin_styles');

function my_plugin_enqueue_admin_styles() {
    
?>
		<style>
			<?php foreach (Settings::$currencies as $key => $currency): ?>
			.dashicons-<?php echo esc_attr($key); ?>::before {
				content: "<?php echo esc_html($currency['symbol']); ?>";
				font-size: 14px !important;
				font-weight: bold !important;
			}
			<?php endforeach; ?>
		</style>
		<?php

}

function all_purpose_inventory_plugin_init() {
    load_plugin_textdomain('wer_pk', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'all_purpose_inventory_plugin_init');



// Handle the login process
add_action('init', 'handle_frontend_login');
add_action('init', 'handle_registration');

function handle_frontend_login() {
    if (isset($_POST['wer_pk_login_submit'])) {
        
		$loginNounce = isset($_POST['custom_login_nonce']) ? sanitize_text_field(wp_unslash($_POST['custom_login_nonce'])) : '';
		
		// Verify nonce
        if (! $loginNounce || 
            ! wp_verify_nonce($loginNounce, 'custom_login_nonce_action')) {
            echo "<div class='error'>" . __("Nonce verification failed.", "wer_pk") . "</div>";
            return;
        }

		// Check if 'log' and 'pwd' are set in $_POST
        if (isset($_POST['log']) && isset($_POST['pwd'])) {
            $creds = array();
            $creds['user_login'] = sanitize_text_field(wp_unslash($_POST['log']));
            $creds['user_password'] = sanitize_text_field(wp_unslash($_POST['pwd']));
            $creds['remember'] = true;

            $user = wp_signon($creds, false);

            if (is_wp_error($user)) {
                echo '<p>' . esc_html($user->get_error_message()) . '</p>';
            } else {
                wp_safe_redirect(home_url('/seller-account/'));
                exit;
            }

        } else {

            echo '<p>' . __("Please enter both username and password.", "wer_pk") . '</p>';

        }
    }
}


function handle_registration() {
    if (isset($_POST['wer_pk_registration_submit'])) {
		$registrationNounce = isset($_POST['custom_registration_nonce']) ? sanitize_text_field(wp_unslash($_POST['custom_registration_nonce'])) : '';
		$username = isset($_POST['username']) ? sanitize_user(wp_unslash($_POST['username'])) : '';
        $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
        $password = isset($_POST['password']) ? wp_unslash($_POST['password']) : '';

		// Verify nonce
        if (! $registrationNounce || 
            !wp_verify_nonce($registrationNounce, 'custom_registration_nonce_action')) {
            echo "<div class='error'>" . __("Nonce verification failed.", "wer_pk") . "</div>";
            return;
        }


        // Validate the input
        $errors = new WP_Error();

        if (empty($username) || empty($email) || empty($password)) {
            $errors->add('field', __('Required form field is missing', 'wer_pk') );
        }

        if (!is_email($email)) {
            $errors->add('email_invalid', __('Email is not valid', 'wer_pk') );
        }

        if (username_exists($username) || email_exists($email)) {
            $errors->add('user_exists', __('Username or email already exists', 'wer_pk') );
        }

        if (empty($errors->errors)) {
            $user_id = wp_create_user($username, $password, $email);
            if (!is_wp_error($user_id)) {
				// Send notification email
                $to = get_option('admin_email'); // Change to your desired email
                $subject = __("New User Registration", "wer_pk");
                $message = __("A new user has registered:\n\nUsername: $username\nEmail: $email", "wer_pk");
                wp_mail($to, $subject, $message);

                // User created, you can also log them in or redirect
                echo __("Registration successful!", "wer_pk");

				wp_safe_redirect(home_url("/login/"));
				exit;


            } else {
                echo esc_html(sprintf(__("Registration failed: %s", "wer_pk"), $user_id->get_error_message()));
            }
        } else {
            foreach ($errors->get_error_messages() as $error) {
                echo "<div class='error'>" . esc_html($error) . "</div>";
            }
        }
    }
}



