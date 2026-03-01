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
		$products = $this->inventory_service->get_products();
		$symbol   = Settings::currency_symbols()[ Settings::get( 'currency', 'USD' ) ] ?? '$';

		ob_start();
		require APGI_PLUGIN_PATH . 'templates/seller-account.php';
		return (string) ob_get_clean();
	}

	/**
	 * Render orders placeholder shortcode.
	 *
	 * @return string
	 */
	public function render_orders(): string {
		ob_start();
		require APGI_PLUGIN_PATH . 'templates/orders.php';
		return (string) ob_get_clean();
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
				esc_html__( "A new user has registered. Username: %1$s Email: %2$s", 'wer_pk' ),
				sanitize_text_field( $username ),
				sanitize_email( $email )
			)
		);

		wp_safe_redirect( home_url( '/login/' ) );
		exit;
	}
}
