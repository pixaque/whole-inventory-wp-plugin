<?php
/**
 * Inventory and order service.
 *
 * @package APGI
 */

namespace APGI;

use wpdb;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Provides inventory and order CRUD for frontend workflows.
 */
class Inventory_Service {

	/**
	 * Ensure required plugin tables exist.
	 *
	 * @return void
	 */
	public static function ensure_tables(): void {
		global $wpdb;

		if ( ! ( $wpdb instanceof wpdb ) ) {
			return;
		}

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();
		$products_table  = $wpdb->prefix . 'products';
		$orders_table    = $wpdb->prefix . 'apgi_orders';

		$sql_products = "CREATE TABLE {$products_table} (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			storeId BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
			materialsName VARCHAR(200) NOT NULL,
			stockQty INT(11) NOT NULL DEFAULT 0,
			unitPrice DECIMAL(10,2) NOT NULL DEFAULT 0,
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY storeId (storeId)
		) {$charset_collate};";

		$sql_orders = "CREATE TABLE {$orders_table} (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			product_id BIGINT(20) UNSIGNED NOT NULL,
			quantity INT(11) NOT NULL DEFAULT 1,
			note TEXT NULL,
			created_by BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY product_id (product_id),
			KEY created_by (created_by)
		) {$charset_collate};";

		dbDelta( $sql_products );
		dbDelta( $sql_orders );
	}

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
			"SELECT id, materialsName, storeId, stockQty, unitPrice FROM {$table} ORDER BY id DESC LIMIT %d",
			$per_page
		);

		$results = $wpdb->get_results( $sql );
		return is_array( $results ) ? $results : array();
	}

	/**
	 * Get product by ID.
	 *
	 * @param int $product_id Product ID.
	 *
	 * @return object|null
	 */
	public function get_product_by_id( int $product_id ) {
		global $wpdb;

		if ( ! ( $wpdb instanceof wpdb ) || $product_id < 1 ) {
			return null;
		}

		$table = $wpdb->prefix . 'products';
		return $wpdb->get_row( $wpdb->prepare( "SELECT id, materialsName, storeId, stockQty, unitPrice FROM {$table} WHERE id = %d", $product_id ) );
	}

	/**
	 * Create a product row.
	 *
	 * @param string $name      Product name.
	 * @param int    $store_id  Store ID.
	 * @param int    $stock_qty Stock quantity.
	 * @param float  $unit_price Unit price.
	 *
	 * @return bool
	 */
	public function create_product( string $name, int $store_id, int $stock_qty, float $unit_price ): bool {
		global $wpdb;

		if ( ! ( $wpdb instanceof wpdb ) ) {
			return false;
		}

		$table = $wpdb->prefix . 'products';

		$inserted = $wpdb->insert(
			$table,
			array(
				'materialsName' => $name,
				'storeId'       => $store_id,
				'stockQty'      => $stock_qty,
				'unitPrice'     => $unit_price,
			),
			array( '%s', '%d', '%d', '%f' )
		);

		return false !== $inserted;
	}

	/**
	 * Update product row.
	 *
	 * @param int    $product_id Product ID.
	 * @param string $name       Product name.
	 * @param int    $store_id   Store ID.
	 * @param int    $stock_qty  Stock quantity.
	 * @param float  $unit_price Unit price.
	 *
	 * @return bool
	 */
	public function update_product( int $product_id, string $name, int $store_id, int $stock_qty, float $unit_price ): bool {
		global $wpdb;

		if ( ! ( $wpdb instanceof wpdb ) || $product_id < 1 ) {
			return false;
		}

		$table = $wpdb->prefix . 'products';
		$rows  = $wpdb->update(
			$table,
			array(
				'materialsName' => $name,
				'storeId'       => $store_id,
				'stockQty'      => $stock_qty,
				'unitPrice'     => $unit_price,
			),
			array( 'id' => $product_id ),
			array( '%s', '%d', '%d', '%f' ),
			array( '%d' )
		);

		return false !== $rows;
	}

	/**
	 * Delete product and related orders.
	 *
	 * @param int $product_id Product ID.
	 *
	 * @return bool
	 */
	public function delete_product( int $product_id ): bool {
		global $wpdb;

		if ( ! ( $wpdb instanceof wpdb ) || $product_id < 1 ) {
			return false;
		}

		$products_table = $wpdb->prefix . 'products';
		$orders_table   = $wpdb->prefix . 'apgi_orders';

		$wpdb->query( 'START TRANSACTION' );
		$wpdb->delete( $orders_table, array( 'product_id' => $product_id ), array( '%d' ) );
		$deleted = $wpdb->delete( $products_table, array( 'id' => $product_id ), array( '%d' ) );

		if ( false === $deleted ) {
			$wpdb->query( 'ROLLBACK' );
			return false;
		}

		$wpdb->query( 'COMMIT' );
		return true;
	}

	/**
	 * Get product options for order form.
	 *
	 * @return array<int,object>
	 */
	public function get_product_options(): array {
		global $wpdb;

		if ( ! ( $wpdb instanceof wpdb ) ) {
			return array();
		}

		$table = $wpdb->prefix . 'products';
		$rows  = $wpdb->get_results( "SELECT id, materialsName, stockQty FROM {$table} ORDER BY materialsName ASC" );

		return is_array( $rows ) ? $rows : array();
	}

	/**
	 * Get order by ID.
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return object|null
	 */
	public function get_order_by_id( int $order_id ) {
		global $wpdb;

		if ( ! ( $wpdb instanceof wpdb ) || $order_id < 1 ) {
			return null;
		}

		$orders_table = $wpdb->prefix . 'apgi_orders';
		return $wpdb->get_row( $wpdb->prepare( "SELECT id, product_id, quantity, note FROM {$orders_table} WHERE id = %d", $order_id ) );
	}

	/**
	 * Create order and adjust product stock.
	 *
	 * @param int    $product_id Product ID.
	 * @param int    $quantity   Quantity.
	 * @param string $note       Note.
	 * @param int    $created_by User ID.
	 *
	 * @return bool
	 */
	public function create_order( int $product_id, int $quantity, string $note, int $created_by ): bool {
		global $wpdb;

		if ( ! ( $wpdb instanceof wpdb ) || $product_id < 1 || $quantity < 1 ) {
			return false;
		}

		$products_table = $wpdb->prefix . 'products';
		$orders_table   = $wpdb->prefix . 'apgi_orders';

		$product = $wpdb->get_row( $wpdb->prepare( "SELECT id, stockQty FROM {$products_table} WHERE id = %d", $product_id ) );
		if ( ! $product || (int) $product->stockQty < $quantity ) {
			return false;
		}

		$wpdb->query( 'START TRANSACTION' );

		$order_ok = $wpdb->insert(
			$orders_table,
			array(
				'product_id' => $product_id,
				'quantity'   => $quantity,
				'note'       => $note,
				'created_by' => $created_by,
			),
			array( '%d', '%d', '%s', '%d' )
		);

		$stock_ok = $wpdb->query(
			$wpdb->prepare(
				"UPDATE {$products_table} SET stockQty = stockQty - %d WHERE id = %d",
				$quantity,
				$product_id
			)
		);

		if ( false === $order_ok || false === $stock_ok ) {
			$wpdb->query( 'ROLLBACK' );
			return false;
		}

		$wpdb->query( 'COMMIT' );
		return true;
	}

	/**
	 * Update order and reconcile stock.
	 *
	 * @param int    $order_id   Order ID.
	 * @param int    $product_id Product ID.
	 * @param int    $quantity   Quantity.
	 * @param string $note       Note.
	 *
	 * @return bool
	 */
	public function update_order( int $order_id, int $product_id, int $quantity, string $note ): bool {
		global $wpdb;

		if ( ! ( $wpdb instanceof wpdb ) || $order_id < 1 || $product_id < 1 || $quantity < 1 ) {
			return false;
		}

		$orders_table   = $wpdb->prefix . 'apgi_orders';
		$products_table = $wpdb->prefix . 'products';

		$current = $this->get_order_by_id( $order_id );
		if ( ! $current ) {
			return false;
		}

		$current_qty = (int) $current->quantity;

		$product = $wpdb->get_row( $wpdb->prepare( "SELECT id, stockQty FROM {$products_table} WHERE id = %d", $product_id ) );
		if ( ! $product ) {
			return false;
		}

		$delta = $quantity - $current_qty;
		if ( $delta > 0 && (int) $product->stockQty < $delta ) {
			return false;
		}

		$wpdb->query( 'START TRANSACTION' );

		$updated = $wpdb->update(
			$orders_table,
			array(
				'product_id' => $product_id,
				'quantity'   => $quantity,
				'note'       => $note,
			),
			array( 'id' => $order_id ),
			array( '%d', '%d', '%s' ),
			array( '%d' )
		);

		$stock_query = true;
		if ( 0 !== $delta ) {
			$stock_query = $wpdb->query(
				$wpdb->prepare(
					"UPDATE {$products_table} SET stockQty = stockQty - %d WHERE id = %d",
					$delta,
					$product_id
				)
			);
		}

		if ( false === $updated || false === $stock_query ) {
			$wpdb->query( 'ROLLBACK' );
			return false;
		}

		$wpdb->query( 'COMMIT' );
		return true;
	}

	/**
	 * Delete order and restore product stock.
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return bool
	 */
	public function delete_order( int $order_id ): bool {
		global $wpdb;

		if ( ! ( $wpdb instanceof wpdb ) || $order_id < 1 ) {
			return false;
		}

		$orders_table   = $wpdb->prefix . 'apgi_orders';
		$products_table = $wpdb->prefix . 'products';

		$current = $this->get_order_by_id( $order_id );
		if ( ! $current ) {
			return false;
		}

		$wpdb->query( 'START TRANSACTION' );

		$deleted = $wpdb->delete( $orders_table, array( 'id' => $order_id ), array( '%d' ) );
		$stocked = $wpdb->query(
			$wpdb->prepare(
				"UPDATE {$products_table} SET stockQty = stockQty + %d WHERE id = %d",
				(int) $current->quantity,
				(int) $current->product_id
			)
		);

		if ( false === $deleted || false === $stocked ) {
			$wpdb->query( 'ROLLBACK' );
			return false;
		}

		$wpdb->query( 'COMMIT' );
		return true;
	}

	/**
	 * Fetch recent orders.
	 *
	 * @return array<int,object>
	 */
	public function get_orders(): array {
		global $wpdb;

		if ( ! ( $wpdb instanceof wpdb ) ) {
			return array();
		}

		$orders_table   = $wpdb->prefix . 'apgi_orders';
		$products_table = $wpdb->prefix . 'products';

		$sql = "SELECT o.id, o.product_id, o.quantity, o.note, o.created_at, p.materialsName
			FROM {$orders_table} o
			LEFT JOIN {$products_table} p ON p.id = o.product_id
			ORDER BY o.id DESC
			LIMIT 20";

		$rows = $wpdb->get_results( $sql );
		return is_array( $rows ) ? $rows : array();
	}
}
