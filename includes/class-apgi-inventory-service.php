<?php
/**
 * Inventory read service.
 *
 * @package APGI
 */

namespace APGI;

use wpdb;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Provides frontend inventory access.
 */
class Inventory_Service {

	/**
	 * Return paginated inventory rows.
	 *
	 * @return array<int,object>
	 */
	public function get_products(): array {
		global $wpdb;

		if ( ! ( $wpdb instanceof wpdb ) ) {
			return array();
		}

		$per_page = absint( Settings::get( 'products_per_page', 20 ) );
		if ( $per_page < 1 ) {
			$per_page = 20;
		}

		$table = $wpdb->prefix . 'products';
		$sql   = $wpdb->prepare(
			"SELECT id, materialsName, storeId FROM {$table} ORDER BY id DESC LIMIT %d",
			$per_page
		);

		$results = $wpdb->get_results( $sql );
		return is_array( $results ) ? $results : array();
	}
}
