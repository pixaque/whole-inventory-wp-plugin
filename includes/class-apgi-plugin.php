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

		add_action( 'plugins_loaded', array( $this, 'maybe_load_redux' ), 1 );
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
		Inventory_Service::ensure_tables();
	}

	/**
	 * Attempt to load Redux Framework from common install paths.
	 *
	 * @return void
	 */
	public function maybe_load_redux(): void {
		if ( class_exists( '\\Redux' ) ) {
			return;
		}

		$candidate_files = array(
			WP_PLUGIN_DIR . '/redux-framework/redux-framework.php',
			WP_PLUGIN_DIR . '/redux-framework/ReduxCore/framework.php',
			APGI_PLUGIN_PATH . 'vendor/redux-framework/redux-framework/redux-framework.php',
			APGI_PLUGIN_PATH . 'vendor/redux-framework/redux-core/framework.php',
			APGI_PLUGIN_PATH . 'lib/redux-framework/redux-framework.php',
			APGI_PLUGIN_PATH . 'lib/redux-framework/redux-core/framework.php',
		);

		foreach ( $candidate_files as $file_path ) {
			if ( is_readable( $file_path ) ) {
				require_once $file_path;
				break;
			}
		}
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
		Inventory_Service::ensure_tables();
		Redux_Config::register();

		new Admin();
		new Frontend( new Inventory_Service() );
	}
}
