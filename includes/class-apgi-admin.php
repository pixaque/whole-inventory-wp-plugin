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
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( APGI_PLUGIN_FILE ), array( $this, 'plugin_action_links' ) );
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
