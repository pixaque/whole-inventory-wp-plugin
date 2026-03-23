<?php
/**
 * Frontend features.
 *
 * @package APGI
 */

namespace APGI;

use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers shortcodes and request handlers.
 */
class Frontend {

	/**
	 * Inventory service.
	 *
	 * @var Inventory_Service
	 */
	private $inventory_service;

	/**
	 * Constructor.
	 *
	 * @param Inventory_Service $inventory_service Inventory service.
	 */
	public function __construct( Inventory_Service $inventory_service ) {
		$this->inventory_service = $inventory_service;

		add_shortcode( 'wer_pk_frontProducts', array( $this, 'render_products' ) );
		add_shortcode( 'wer_pk_frontendOrders', array( $this, 'render_orders' ) );
		add_shortcode( 'wer_pk_frontendLogin', array( $this, 'render_login' ) );
		add_shortcode( 'wer_pk_frontendRegistration', array( $this, 'render_registration' ) );

		add_action( 'init', array( $this, 'handle_login' ) );
		add_action( 'init', array( $this, 'handle_registration' ) );
		add_action( 'init', array( $this, 'handle_inventory_submission' ) );
		add_action( 'init', array( $this, 'handle_inventory_delete' ) );
		add_action( 'init', array( $this, 'handle_order_submission' ) );
		add_action( 'init', array( $this, 'handle_order_delete' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Create required pages at activation.
	 *
	 * @return void
	 */
	public static function create_required_pages(): void {
		$pages = array(
			'seller-account' => '[wer_pk_frontProducts]',
			'login'          => '[wer_pk_frontendLogin]',
			'registration'   => '[wer_pk_frontendRegistration]',
			'orders'         => '[wer_pk_frontendOrders]',
		);

		foreach ( $pages as $slug => $shortcode ) {
			if ( get_page_by_path( $slug ) ) {
				continue;
			}

			wp_insert_post(
				array(
					'post_title'   => ucwords( str_replace( '-', ' ', $slug ) ),
					'post_name'    => $slug,
					'post_content' => wp_kses_post( $shortcode ),
					'post_status'  => 'publish',
					'post_type'    => 'page',
				)
			);
		}
	}

	/**
	 * Enqueue frontend styles.
	 *
	 * @return void
	 */
	public function enqueue_assets(): void {
		wp_enqueue_style( 'apgi-frontend', APGI_PLUGIN_URL . 'assets/css/frontend.css', array(), APGI_PLUGIN_VERSION );
	}

	/**
	 * Render products shortcode.
	 *
	 * @return string
	 */
	public function render_products(): string {
		$products      = $this->inventory_service->get_products();
		$symbol        = Settings::currency_symbols()[ Settings::get( 'currency', 'USD' ) ] ?? '$';
		$status        = isset( $_GET['apgi_status'] ) ? sanitize_key( wp_unslash( $_GET['apgi_status'] ) ) : '';
		$editing_id    = isset( $_GET['apgi_edit_product'] ) ? absint( wp_unslash( $_GET['apgi_edit_product'] ) ) : 0;
		$editing_item  = $editing_id > 0 ? $this->inventory_service->get_product_by_id( $editing_id ) : null;

		ob_start();
		require APGI_PLUGIN_PATH . 'templates/seller-account.php';
		return (string) ob_get_clean();
	}

	/**
	 * Render orders shortcode.
	 *
	 * @return string
	 */
	public function render_orders(): string {
		$product_options = $this->inventory_service->get_product_options();
		$orders          = $this->inventory_service->get_orders();
		$status          = isset( $_GET['apgi_status'] ) ? sanitize_key( wp_unslash( $_GET['apgi_status'] ) ) : '';
		$editing_id      = isset( $_GET['apgi_edit_order'] ) ? absint( wp_unslash( $_GET['apgi_edit_order'] ) ) : 0;
		$editing_order   = $editing_id > 0 ? $this->inventory_service->get_order_by_id( $editing_id ) : null;

		ob_start();
		require APGI_PLUGIN_PATH . 'templates/orders.php';
		return (string) ob_get_clean();
	}

	/**
	 * Handle inventory form submit.
	 *
	 * @return void
	 */
	public function handle_inventory_submission(): void {
		if ( ! isset( $_POST['apgi_add_inventory_submit'] ) ) {
			return;
		}

		if ( ! is_user_logged_in() ) {
			wp_die( esc_html__( 'Please log in to add inventory.', 'wer_pk' ) );
		}

		check_admin_referer( 'apgi_add_inventory_action', 'apgi_add_inventory_nonce' );

		$action     = isset( $_POST['apgi_inventory_action'] ) ? sanitize_key( wp_unslash( $_POST['apgi_inventory_action'] ) ) : 'create';
		$product_id = isset( $_POST['product_id'] ) ? absint( wp_unslash( $_POST['product_id'] ) ) : 0;
		$name       = isset( $_POST['product_name'] ) ? sanitize_text_field( wp_unslash( $_POST['product_name'] ) ) : '';
		$store_id   = isset( $_POST['store_id'] ) ? absint( wp_unslash( $_POST['store_id'] ) ) : 0;
		$stock_qty  = isset( $_POST['stock_qty'] ) ? absint( wp_unslash( $_POST['stock_qty'] ) ) : 0;
		$unit_price = isset( $_POST['unit_price'] ) ? (float) sanitize_text_field( wp_unslash( $_POST['unit_price'] ) ) : 0;

		if ( '' === $name || $stock_qty < 0 || $unit_price < 0 ) {
			$this->safe_status_redirect( 'seller-account', 'invalid' );
		}

		if ( 'update' === $action && $product_id > 0 ) {
			$ok = $this->inventory_service->update_product( $product_id, $name, $store_id, $stock_qty, $unit_price );
			$this->safe_status_redirect( 'seller-account', $ok ? 'inventory_updated' : 'failed' );
		}

		$created = $this->inventory_service->create_product( $name, $store_id, $stock_qty, $unit_price );
		$this->safe_status_redirect( 'seller-account', $created ? 'inventory_added' : 'failed' );
	}

	/**
	 * Handle inventory delete.
	 *
	 * @return void
	 */
	public function handle_inventory_delete(): void {
		if ( ! isset( $_POST['apgi_delete_inventory_submit'] ) ) {
			return;
		}

		if ( ! is_user_logged_in() ) {
			wp_die( esc_html__( 'Please log in to delete inventory.', 'wer_pk' ) );
		}

		check_admin_referer( 'apgi_delete_inventory_action', 'apgi_delete_inventory_nonce' );
		$product_id = isset( $_POST['product_id'] ) ? absint( wp_unslash( $_POST['product_id'] ) ) : 0;

		if ( $product_id < 1 ) {
			$this->safe_status_redirect( 'seller-account', 'invalid' );
		}

		$deleted = $this->inventory_service->delete_product( $product_id );
		$this->safe_status_redirect( 'seller-account', $deleted ? 'inventory_deleted' : 'failed' );
	}

	/**
	 * Handle order form submit.
	 *
	 * @return void
	 */
	public function handle_order_submission(): void {
		if ( ! isset( $_POST['apgi_place_order_submit'] ) ) {
			return;
		}

		if ( ! is_user_logged_in() ) {
			wp_die( esc_html__( 'Please log in to place orders.', 'wer_pk' ) );
		}

		check_admin_referer( 'apgi_place_order_action', 'apgi_place_order_nonce' );

		$action     = isset( $_POST['apgi_order_action'] ) ? sanitize_key( wp_unslash( $_POST['apgi_order_action'] ) ) : 'create';
		$order_id   = isset( $_POST['order_id'] ) ? absint( wp_unslash( $_POST['order_id'] ) ) : 0;
		$product_id = isset( $_POST['product_id'] ) ? absint( wp_unslash( $_POST['product_id'] ) ) : 0;
		$quantity   = isset( $_POST['quantity'] ) ? absint( wp_unslash( $_POST['quantity'] ) ) : 0;
		$note       = isset( $_POST['order_note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['order_note'] ) ) : '';

		if ( $product_id < 1 || $quantity < 1 ) {
			$this->safe_status_redirect( 'orders', 'invalid' );
		}

		if ( 'update' === $action && $order_id > 0 ) {
			$ok = $this->inventory_service->update_order( $order_id, $product_id, $quantity, $note );
			$this->safe_status_redirect( 'orders', $ok ? 'order_updated' : 'failed' );
		}

		$created = $this->inventory_service->create_order( $product_id, $quantity, $note, get_current_user_id() );
		$this->safe_status_redirect( 'orders', $created ? 'order_added' : 'failed' );
	}

	/**
	 * Handle order delete.
	 *
	 * @return void
	 */
	public function handle_order_delete(): void {
		if ( ! isset( $_POST['apgi_delete_order_submit'] ) ) {
			return;
		}

		if ( ! is_user_logged_in() ) {
			wp_die( esc_html__( 'Please log in to delete orders.', 'wer_pk' ) );
		}

		check_admin_referer( 'apgi_delete_order_action', 'apgi_delete_order_nonce' );
		$order_id = isset( $_POST['order_id'] ) ? absint( wp_unslash( $_POST['order_id'] ) ) : 0;

		if ( $order_id < 1 ) {
			$this->safe_status_redirect( 'orders', 'invalid' );
		}

		$deleted = $this->inventory_service->delete_order( $order_id );
		$this->safe_status_redirect( 'orders', $deleted ? 'order_deleted' : 'failed' );
	}

	/**
	 * Safe redirect helper.
	 *
	 * @param string $slug   Page slug.
	 * @param string $status Status code.
	 *
	 * @return void
	 */
	private function safe_status_redirect( string $slug, string $status ): void {
		wp_safe_redirect(
			add_query_arg(
				array( 'apgi_status' => sanitize_key( $status ) ),
				home_url( '/' . sanitize_title( $slug ) . '/' )
			)
		);
		exit;
	}

	/**
	 * Render login shortcode.
	 *
	 * @return string
	 */
	public function render_login(): string {
		if ( is_user_logged_in() ) {
			return '<p>' . esc_html__( 'You are already logged in.', 'wer_pk' ) . '</p>';
		}

		ob_start();
		require APGI_PLUGIN_PATH . 'templates/form-login.php';
		return (string) ob_get_clean();
	}

	/**
	 * Render registration shortcode.
	 *
	 * @return string
	 */
	public function render_registration(): string {
		if ( ! Settings::get( 'enable_registration', true ) ) {
			return '<p>' . esc_html__( 'Registration is currently disabled.', 'wer_pk' ) . '</p>';
		}

		ob_start();
		require APGI_PLUGIN_PATH . 'templates/registration.php';
		return (string) ob_get_clean();
	}

	/**
	 * Handle frontend login.
	 *
	 * @return void
	 */
	public function handle_login(): void {
		if ( ! isset( $_POST['apgi_login_submit'] ) ) {
			return;
		}

		check_admin_referer( 'apgi_login_action', 'apgi_login_nonce' );

		$username = isset( $_POST['log'] ) ? sanitize_user( wp_unslash( $_POST['log'] ) ) : '';
		$password = isset( $_POST['pwd'] ) ? (string) wp_unslash( $_POST['pwd'] ) : '';

		if ( '' === $username || '' === $password ) {
			wp_die( esc_html__( 'Username and password are required.', 'wer_pk' ) );
		}

		$user = wp_signon(
			array(
				'user_login'    => $username,
				'user_password' => $password,
				'remember'      => true,
			),
			is_ssl()
		);

		if ( is_wp_error( $user ) ) {
			wp_die( esc_html( $user->get_error_message() ) );
		}

		$redirect_slug = sanitize_title( (string) Settings::get( 'login_redirect_page', 'seller-account' ) );
		wp_safe_redirect( home_url( '/' . $redirect_slug . '/' ) );
		exit;
	}

	/**
	 * Handle registration.
	 *
	 * @return void
	 */
	public function handle_registration(): void {
		if ( ! isset( $_POST['apgi_registration_submit'] ) ) {
			return;
		}

		if ( ! Settings::get( 'enable_registration', true ) ) {
			wp_die( esc_html__( 'Registration is disabled.', 'wer_pk' ) );
		}

		check_admin_referer( 'apgi_registration_action', 'apgi_registration_nonce' );

		$username = isset( $_POST['username'] ) ? sanitize_user( wp_unslash( $_POST['username'] ) ) : '';
		$email    = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$password = isset( $_POST['password'] ) ? (string) wp_unslash( $_POST['password'] ) : '';

		$errors = new WP_Error();

		if ( '' === $username || '' === $email || '' === $password ) {
			$errors->add( 'missing_field', __( 'Required form field is missing.', 'wer_pk' ) );
		}

		if ( ! is_email( $email ) ) {
			$errors->add( 'invalid_email', __( 'Email is not valid.', 'wer_pk' ) );
		}

		if ( username_exists( $username ) || email_exists( $email ) ) {
			$errors->add( 'user_exists', __( 'Username or email already exists.', 'wer_pk' ) );
		}

		if ( $errors->has_errors() ) {
			wp_die( esc_html( implode( ' ', $errors->get_error_messages() ) ) );
		}

		$user_id = wp_create_user( $username, $password, $email );
		if ( is_wp_error( $user_id ) ) {
			wp_die( esc_html( $user_id->get_error_message() ) );
		}

		wp_mail(
			sanitize_email( get_option( 'admin_email' ) ),
			esc_html__( 'New User Registration', 'wer_pk' ),
			sprintf(
				/* translators: 1: username, 2: email */
				esc_html__( 'A new user has registered. Username: %1$s Email: %2$s', 'wer_pk' ),
				sanitize_text_field( $username ),
				sanitize_email( $email )
			)
		);

		wp_safe_redirect( home_url( '/login/' ) );
		exit;
	}
}
