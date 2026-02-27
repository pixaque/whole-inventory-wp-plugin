<?php
if (!class_exists('WER_PKDb')){

	/**
	 * Declare these as global in case schema.php is included from a function.
	 *
	 * @global wpdb   $wpdb            WordPress database abstraction object.
	 * @global array  $wp_queries
	 * @global string $charset_collate
	 */
	global $wpdb, $wp_queries, $charset_collate;

	/**
	 * The database character collate.
	 */
	$charset_collate = $wpdb->get_charset_collate();

	class WER_PKDb{
		public function __construct(){
			//$this->wp_get_WER_PKdb_schema();
		}	

		function wp_get_WER_PKdb_schema( $blog_id = null ) {
			global $wpdb;

			$charset_collate = $wpdb->get_charset_collate();

			if ( $blog_id && (int) $blog_id !== $wpdb->blogid ) {
				$old_blog_id = $wpdb->set_blog_id( $blog_id );
			}

			// Engage multisite if in the middle of turning it on from network.php.
			$is_multisite = is_multisite() || ( defined( 'WP_INSTALLING_NETWORK' ) && WP_INSTALLING_NETWORK );

			/*
			 * Indexes have a maximum size of 767 bytes. Historically, we haven't need to be concerned about that.
			 * As of 4.2, however, we moved to utf8mb4, which uses 4 bytes per character. This means that an index which
			 * used to have room for floor(767/3) = 255 characters, now only has room for floor(767/4) = 191 characters.
			 */
			$max_index_length = 191;
			$table_name = $wpdb->base_prefix.'projects';

			if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
				// WER_PK tables.
				$wer_pk_tables = "CREATE TABLE $table_name (
				id bigint(20) unsigned NOT NULL auto_increment,
				site_name text NOT NULL,
				site_size int(11) NOT NULL default '0',
				site_location text NOT NULL,
				start_date date NOT NULL,
				status int(11) NOT NULL,
				PRIMARY KEY  (id),
				KEY site_name_location_date (site_name($max_index_length), site_location($max_index_length), start_date)
				) $charset_collate;";

				$queries = $wer_pk_tables;
				
				$wpdb->query($queries);
			}

			$table_name = $wpdb->base_prefix.'projects_details';

			if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
				// WER_PK tables.
				$wer_pk_tables = "CREATE TABLE $table_name (
					id bigint(20) unsigned NOT NULL auto_increment,
					projectid bigint(20),
					billNo VARCHAR(255) NULL DEFAULT NULL,
					expenseType varchar(250),
					billdate DATE NOT NULL,
					description varchar(250),
					orderTotal decimal(19,2),
					confirmed boolean DEFAULT 0,
					PRIMARY KEY (id),
					KEY mainBillindex (projectid, billNo(100), description(100), orderTotal, billdate)
				) $charset_collate;";

				$queries = $wer_pk_tables;
				
				$wpdb->query($queries);

			}

			$table_name = $wpdb->base_prefix.'project_order';

			if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
				// WER_PK tables.
				$wer_pk_tables = "CREATE TABLE $table_name (
				id bigint(20) unsigned NOT NULL auto_increment,
				billid VARCHAR(255) NULL DEFAULT NULL,
				supplierName varchar(250),
				productid bigint(20) default NULL,
				materialsName varchar(250),
				quantity int,
				GST decimal(11,2) NULL DEFAULT '0.00',
				totalPrice decimal(19,2),
				discount int NULL DEFAULT '0',
				processed boolean DEFAULT 0,
				PRIMARY KEY  (id),
				KEY mainOrderindex (billid(100), supplierName(100), materialsName(100), totalPrice, discount)
				) $charset_collate;";

				$queries = $wer_pk_tables;
				
				$wpdb->query($queries);

			}

			$table_name = $wpdb->base_prefix.'products';

			if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
				// WER_PK tables.
				$wer_pk_tables = "CREATE TABLE $table_name (
				`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				`storeId` bigint(20) DEFAULT NULL,
				`materialsName` varchar(250) DEFAULT NULL,
				PRIMARY KEY (`id`),
				KEY mainProductindex (id, materialsName(100))
				) $charset_collate;";

				$queries = $wer_pk_tables;
				
				$wpdb->query($queries);

			}

			$table_name = $wpdb->base_prefix.'product_attributes';

			if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
				// WER_PK tables.
				$wer_pk_tables = "CREATE TABLE $table_name (
				`attribute_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				`attributeName` varchar(100) NOT NULL,
				PRIMARY KEY (`attribute_id`),
				KEY mainProductindex (attributeName(100))
				) $charset_collate;";

				$queries = $wer_pk_tables;
				
				$wpdb->query($queries);

			}

			$table_name = $wpdb->base_prefix.'product_variants';

			if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
				// WER_PK tables.
				$wer_pk_tables = "CREATE TABLE $table_name (
				`variant_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				`product_id` bigint(20) UNSIGNED NOT NULL,
				`variantSKU` varchar(100) DEFAULT NULL,
				`variantStock` int(11) DEFAULT NULL,
				`variantPrice` decimal(19,2) DEFAULT NULL,
				`variantDiscount` int(11) DEFAULT 0,
				`variantGST` decimal(11,2) DEFAULT 0.00,
				PRIMARY KEY (`variant_id`),
				FOREIGN KEY (`product_id`) REFERENCES `{$wpdb->base_prefix}products`(`id`) ON DELETE CASCADE,
				KEY mainProductindex (variantStock, variantPrice, variantDiscount)
				) $charset_collate;";

				$queries = $wer_pk_tables;
				
				$wpdb->query($queries);

			}

			$table_name = $wpdb->base_prefix.'variant_attributes';

			if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
				// WER_PK tables.
				$wer_pk_tables = "CREATE TABLE $table_name (
				`variant_id` bigint(20) UNSIGNED NOT NULL,
				`attribute_id` bigint(20) UNSIGNED NOT NULL,
				`attributeValue` varchar(100) NOT NULL,
				FOREIGN KEY (`variant_id`) REFERENCES `{$wpdb->base_prefix}product_variants`(`variant_id`) ON DELETE CASCADE,
				FOREIGN KEY (`attribute_id`) REFERENCES `{$wpdb->base_prefix}product_attributes`(`attribute_id`) ON DELETE CASCADE,
				PRIMARY KEY (`variant_id`, `attribute_id`)
				) $charset_collate;";

				$queries = $wer_pk_tables;
				
				$wpdb->query($queries);

			}

			//order_status enum('Processing','Delayed','Delivered','') NOT NULL DEFAULT 'Processing' ),
			
		}


	}//end of class
}//end if