<?php
/**
 * Admin bootstrap.
 *
 * @package APGI
 */

namespace APGI;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles admin-only hooks.
 */
class Admin {

	/**
	 * Inventory manager menu slug.
	 */
	private const MENU_SLUG = 'apgi_inventory_manager';

	/**
	 * Inventory submenu slug.
	 */
	private const INVENTORY_SLUG = 'apgi_manage_inventory';

	/**
	 * Orders submenu slug.
	 */
	private const ORDERS_SLUG = 'apgi_place_orders';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_management_pages' ) );
		add_action( 'admin_menu', array( $this, 'register_settings_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( APGI_PLUGIN_FILE ), array( $this, 'plugin_action_links' ) );
	}

	/**
	 * Register plugin inventory and order management pages.
	 *
	 * @return void
	 */
	public function register_management_pages(): void {
		add_menu_page(
			esc_html__( 'Inventory Manager', 'wer_pk' ),
			esc_html__( 'Inventory Manager', 'wer_pk' ),
			'manage_options',
			self::MENU_SLUG,
			array( $this, 'render_inventory_page' ),
			'dashicons-products',
			57
		);

		add_submenu_page(
			self::MENU_SLUG,
			esc_html__( 'Manage Inventory', 'wer_pk' ),
			esc_html__( 'Manage Inventory', 'wer_pk' ),
			'manage_options',
			self::INVENTORY_SLUG,
			array( $this, 'render_inventory_page' )
		);

		add_submenu_page(
			self::MENU_SLUG,
			esc_html__( 'Place Orders', 'wer_pk' ),
			esc_html__( 'Place Orders', 'wer_pk' ),
			'manage_options',
			self::ORDERS_SLUG,
			array( $this, 'render_orders_page' )
		);

	}

	/**
	 * Register plugin fallback settings page endpoint.
	 *
	 * If Redux exists, it owns the settings page rendering. We only register
	 * a fallback page when Redux is unavailable.
	 *
	 * @return void
	 */
	public function register_settings_page(): void {
		if ( class_exists( '\\Redux' ) ) {
			return;
		}

		add_menu_page(
			esc_html__( 'Inventory Settings', 'wer_pk' ),
			esc_html__( 'Inventory Settings', 'wer_pk' ),
			'manage_options',
			Settings::OPTION_NAME,
			array( $this, 'render_settings_page' ),
			'dashicons-archive',
			58
		);
	}

	/**
	 * Render inventory management page.
	 *
	 * @return void
	 */
	public function render_inventory_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Sorry, you are not allowed to access this page.', 'wer_pk' ) );
		}

		$inventory_page = get_page_by_path( 'seller-account' );
		$inventory_url  = $inventory_page ? get_permalink( $inventory_page ) : home_url( '/seller-account/' );

		echo '<div class="wrap">';
		echo '<h1>' . esc_html__( 'Manage Inventory', 'wer_pk' ) . '</h1>';
		echo '<p>' . esc_html__( 'Use the frontend inventory workspace to add or review product inventory records.', 'wer_pk' ) . '</p>';
		echo '<p><a class="button button-primary" target="_blank" rel="noopener noreferrer" href="' . esc_url( $inventory_url ) . '">' . esc_html__( 'Open Inventory Workspace', 'wer_pk' ) . '</a></p>';
		echo '<p>' . esc_html__( 'Tip: if the page is missing, reactivate the plugin once to regenerate required pages.', 'wer_pk' ) . '</p>';
		echo '</div>';
	}

	/**
	 * Render orders management page.
	 *
	 * @return void
	 */
	public function render_orders_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Sorry, you are not allowed to access this page.', 'wer_pk' ) );
		}

		$order_page = get_page_by_path( 'orders' );
		$order_url  = $order_page ? get_permalink( $order_page ) : home_url( '/orders/' );

		echo '<div class="wrap">';
		echo '<h1>' . esc_html__( 'Place and Manage Orders', 'wer_pk' ) . '</h1>';
		echo '<p>' . esc_html__( 'Use the frontend order workspace to place orders and track order activity.', 'wer_pk' ) . '</p>';
		echo '<p><a class="button button-primary" target="_blank" rel="noopener noreferrer" href="' . esc_url( $order_url ) . '">' . esc_html__( 'Open Orders Workspace', 'wer_pk' ) . '</a></p>';
		echo '<p>' . esc_html__( 'This plugin version intentionally routes operational workflows to frontend pages while settings are managed via Redux.', 'wer_pk' ) . '</p>';
		echo '</div>';
	}

	/**
	 * Render fallback settings page content.
	 *
	 * @return void
	 */
	public function render_settings_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Sorry, you are not allowed to access this page.', 'wer_pk' ) );
		}

		echo '<div class="wrap"><h1>' . esc_html__( 'Inventory Settings', 'wer_pk' ) . '</h1><p>' . esc_html__( 'Redux Framework is required to render the settings panel. Please install and activate Redux Framework plugin.', 'wer_pk' ) . '</p></div>';
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @return void
	 */
	public function enqueue_assets(): void {
		wp_enqueue_style(
			'apgi-admin',
			APGI_PLUGIN_URL . 'assets/css/admin.css',
			array(),
			APGI_PLUGIN_VERSION
		);
	}

	/**
	 * Add settings link.
	 *
	 * @param array<int,string> $links Existing links.
	 *
	 * @return array<int,string>
	 */
	public function plugin_action_links( array $links ): array {
		$settings_url = admin_url( 'admin.php?page=' . Settings::OPTION_NAME );
		$links[]      = '<a href="' . esc_url( $settings_url ) . '">' . esc_html__( 'Settings', 'wer_pk' ) . '</a>';

		return $links;
	}
}
