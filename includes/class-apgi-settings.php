<?php
/**
 * Settings access layer.
 *
 * @package APGI
 */

namespace APGI;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles Redux options access and migration.
 */
class Settings {

	/**
	 * Redux option key.
	 */
	public const OPTION_NAME = 'apgi_options';

	/**
	 * Legacy option key.
	 */
	private const LEGACY_CURRENCY_KEY = 'wp_wer_pk_blockscurrency';

	/**
	 * Returns supported currencies.
	 *
	 * @return array<string,string>
	 */
	public static function currency_choices(): array {
		return array(
			'USD' => __( 'US Dollar ($)', 'wer_pk' ),
			'GBP' => __( 'UK Pound (£)', 'wer_pk' ),
			'EUR' => __( 'Euro (€)', 'wer_pk' ),
			'INR' => __( 'Indian Rupee (₹)', 'wer_pk' ),
			'PKR' => __( 'Pakistani Rupee (₨)', 'wer_pk' ),
			'AUD' => __( 'Australian Dollar (A$)', 'wer_pk' ),
		);
	}

	/**
	 * Currency symbol map.
	 *
	 * @return array<string,string>
	 */
	public static function currency_symbols(): array {
		return array(
			'USD' => '$',
			'GBP' => '£',
			'EUR' => '€',
			'INR' => '₹',
			'PKR' => '₨',
			'AUD' => 'A$',
		);
	}

	/**
	 * Get all plugin options merged with defaults.
	 *
	 * @return array<string,mixed>
	 */
	public static function all(): array {
		$saved = get_option( self::OPTION_NAME, array() );
		if ( ! is_array( $saved ) ) {
			$saved = array();
		}

		return wp_parse_args( $saved, self::defaults() );
	}

	/**
	 * Get single option.
	 *
	 * @param string $key     Option key.
	 * @param mixed  $default Fallback.
	 *
	 * @return mixed
	 */
	public static function get( string $key, $default = null ) {
		$options = self::all();
		return $options[ $key ] ?? $default;
	}

	/**
	 * Plugin option defaults.
	 *
	 * @return array<string,mixed>
	 */
	public static function defaults(): array {
		return array(
			'brand_name'          => __( 'All Purpose General Inventory', 'wer_pk' ),
			'currency'            => 'USD',
			'enable_registration' => true,
			'login_redirect_page' => 'seller-account',
			'order_page_slug'     => 'orders',
			'products_per_page'   => 20,
			'company_logo'        => array(),
			'email_footer_text'   => __( 'Thank you for using our inventory system.', 'wer_pk' ),
			'store_locations'     => array(),
		);
	}

	/**
	 * Migrate supported legacy options into Redux option once.
	 *
	 * @return void
	 */
	public static function migrate_legacy_options(): void {
		if ( get_option( 'apgi_migration_done' ) ) {
			return;
		}

		$current = self::all();
		$legacy_currency = get_option( self::LEGACY_CURRENCY_KEY, '' );
		if ( is_string( $legacy_currency ) && ! empty( $legacy_currency ) ) {
			$normalized = strtoupper( substr( $legacy_currency, 0, 3 ) );
			if ( isset( self::currency_symbols()[ $normalized ] ) ) {
				$current['currency'] = $normalized;
			}
		}

		update_option( self::OPTION_NAME, $current );
		update_option( 'apgi_migration_done', 1 );
	}
}
