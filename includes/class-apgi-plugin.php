<?php
/**
 * Main plugin orchestrator.
 *
 * @package APGI
 */

namespace APGI;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once APGI_PLUGIN_PATH . 'includes/class-apgi-settings.php';
require_once APGI_PLUGIN_PATH . 'includes/class-apgi-redux-config.php';
require_once APGI_PLUGIN_PATH . 'includes/class-apgi-admin.php';
require_once APGI_PLUGIN_PATH . 'includes/class-apgi-frontend.php';
require_once APGI_PLUGIN_PATH . 'includes/class-apgi-inventory-service.php';

/**
 * Bootstraps plugin services.
 */
final class Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var Plugin|null
	 */
	private static $instance = null;

	/**
	 * Returns singleton instance.
	 *
	 * @return Plugin
	 */
	public static function instance(): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		register_activation_hook( APGI_PLUGIN_FILE, array( __CLASS__, 'activate' ) );

		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Activation callback.
	 *
	 * @return void
	 */
	public static function activate(): void {
		Frontend::create_required_pages();
		Settings::migrate_legacy_options();
	}

	/**
	 * Load translations.
	 *
	 * @return void
	 */
	public function load_textdomain(): void {
		load_plugin_textdomain( 'wer_pk', false, dirname( plugin_basename( APGI_PLUGIN_FILE ) ) . '/languages/' );
	}

	/**
	 * Initialize plugin modules.
	 *
	 * @return void
	 */
	public function init(): void {
		Settings::migrate_legacy_options();
		Redux_Config::register();

		new Admin();
		new Frontend( new Inventory_Service() );
	}
}
