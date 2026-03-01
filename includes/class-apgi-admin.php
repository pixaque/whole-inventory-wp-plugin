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
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_settings_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( APGI_PLUGIN_FILE ), array( $this, 'plugin_action_links' ) );
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
