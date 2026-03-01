<?php
/**
 * Plugin Name:       All Purpose General Inventory Plugin
 * Description:       Inventory and order management plugin powered by Redux Framework settings.
 * Version:           2.0.0
 * Author:            Asad Ullah
 * Author URI:        https://github.com/nothing
 * Text Domain:       wer_pk
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Requires PHP:      7.4
 *
 * @package APGI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'APGI_PLUGIN_FILE', __FILE__ );
define( 'APGI_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'APGI_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'APGI_PLUGIN_VERSION', '2.0.0' );

require_once APGI_PLUGIN_PATH . 'includes/class-apgi-plugin.php';

APGI\Plugin::instance();
