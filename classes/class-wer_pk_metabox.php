<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WER_PK_MetaBox', false ) ) :
	/**
	 * Meta Box integration for frontend pages.
	 */
	class WER_PK_MetaBox {
		/**
		 * Boot integration.
		 */
		public static function init() {
			add_filter( 'rwmb_meta_boxes', array( __CLASS__, 'register_meta_boxes' ) );
		}

		/**
		 * Register plugin fields in Meta Box framework.
		 *
		 * @param array $meta_boxes Existing meta boxes.
		 *
		 * @return array
		 */
		public static function register_meta_boxes( $meta_boxes ) {
			$login_page_id        = self::get_page_id_by_path( 'login' );
			$registration_page_id = self::get_page_id_by_path( 'registration' );
			$seller_account_page_id = self::get_page_id_by_path( 'seller-account' );

			if ( $login_page_id ) {
				$meta_boxes[] = array(
					'id'         => 'wer_pk_login_page_settings',
					'title'      => __( 'Inventory Login Page Settings', 'wer_pk' ),
					'post_types' => array( 'page' ),
					'include'    => array(
						'ID' => array( $login_page_id ),
					),
					'fields'     => array(
						array(
							'id'   => 'wer_pk_login_heading',
							'name' => __( 'Form Heading', 'wer_pk' ),
							'type' => 'text',
							'std'  => __( 'Login', 'wer_pk' ),
						),
						array(
							'id'   => 'wer_pk_login_username_label',
							'name' => __( 'Username Label', 'wer_pk' ),
							'type' => 'text',
							'std'  => __( 'Username or Email Address', 'wer_pk' ),
						),
						array(
							'id'   => 'wer_pk_login_password_label',
							'name' => __( 'Password Label', 'wer_pk' ),
							'type' => 'text',
							'std'  => __( 'Password', 'wer_pk' ),
						),
						array(
							'id'   => 'wer_pk_login_button_text',
							'name' => __( 'Button Label', 'wer_pk' ),
							'type' => 'text',
							'std'  => __( 'Log In', 'wer_pk' ),
						),
						array(
							'id'   => 'wer_pk_login_forgot_password_label',
							'name' => __( 'Forgot Password Label', 'wer_pk' ),
							'type' => 'text',
							'std'  => __( 'Forgot Password?', 'wer_pk' ),
						),
						array(
							'id'   => 'wer_pk_login_register_label',
							'name' => __( 'Register Label', 'wer_pk' ),
							'type' => 'text',
							'std'  => __( 'Register', 'wer_pk' ),
						),
						array(
							'id'   => 'wer_pk_login_show_register_link',
							'name' => __( 'Show registration link', 'wer_pk' ),
							'type' => 'checkbox',
							'std'  => 1,
						),
						array(
							'id'   => 'wer_pk_login_register_url',
							'name' => __( 'Registration URL', 'wer_pk' ),
							'type' => 'url',
							'std'  => home_url( '/registration/' ),
						),
					),
				);
			}

			if ( $registration_page_id ) {
				$meta_boxes[] = array(
					'id'         => 'wer_pk_registration_page_settings',
					'title'      => __( 'Inventory Registration Page Settings', 'wer_pk' ),
					'post_types' => array( 'page' ),
					'include'    => array(
						'ID' => array( $registration_page_id ),
					),
					'fields'     => array(
						array(
							'id'   => 'wer_pk_registration_heading',
							'name' => __( 'Form Heading', 'wer_pk' ),
							'type' => 'text',
							'std'  => __( 'Register', 'wer_pk' ),
						),
						array(
							'id'   => 'wer_pk_registration_username_label',
							'name' => __( 'Username Label', 'wer_pk' ),
							'type' => 'text',
							'std'  => __( 'Username', 'wer_pk' ),
						),
						array(
							'id'   => 'wer_pk_registration_email_label',
							'name' => __( 'Email Label', 'wer_pk' ),
							'type' => 'text',
							'std'  => __( 'Email', 'wer_pk' ),
						),
						array(
							'id'   => 'wer_pk_registration_password_label',
							'name' => __( 'Password Label', 'wer_pk' ),
							'type' => 'text',
							'std'  => __( 'Password', 'wer_pk' ),
						),
						array(
							'id'   => 'wer_pk_registration_button_text',
							'name' => __( 'Button Label', 'wer_pk' ),
							'type' => 'text',
							'std'  => __( 'Register', 'wer_pk' ),
						),
						array(
							'id'   => 'wer_pk_registration_show_login_link',
							'name' => __( 'Show login link', 'wer_pk' ),
							'type' => 'checkbox',
							'std'  => 1,
						),
						array(
							'id'   => 'wer_pk_registration_login_label',
							'name' => __( 'Login Link Label', 'wer_pk' ),
							'type' => 'text',
							'std'  => __( 'Already have an account? Login', 'wer_pk' ),
						),
						array(
							'id'   => 'wer_pk_registration_login_url',
							'name' => __( 'Login URL', 'wer_pk' ),
							'type' => 'url',
							'std'  => home_url( '/login/' ),
						),
					),
				);
			}

			if ( $seller_account_page_id ) {
				$meta_boxes[] = array(
					'id'         => 'wer_pk_seller_account_page_settings',
					'title'      => __( 'Inventory Seller Account Page Settings', 'wer_pk' ),
					'post_types' => array( 'page' ),
					'include'    => array(
						'ID' => array( $seller_account_page_id ),
					),
					'fields'     => array(
						array(
							'id'   => 'wer_pk_seller_add_products_heading',
							'name' => __( 'Add Products Heading', 'wer_pk' ),
							'type' => 'text',
							'std'  => __( 'Add Product(s)', 'wer_pk' ),
						),
						array(
							'id'   => 'wer_pk_seller_manage_variants_legend',
							'name' => __( 'Manage Variants Legend', 'wer_pk' ),
							'type' => 'text',
							'std'  => __( 'Manage Product Variants', 'wer_pk' ),
						),
						array(
							'id'   => 'wer_pk_seller_product_name_label',
							'name' => __( 'Product Name Label', 'wer_pk' ),
							'type' => 'text',
							'std'  => __( 'Product Name', 'wer_pk' ),
						),
						array(
							'id'   => 'wer_pk_seller_quantity_label',
							'name' => __( 'Quantity Label', 'wer_pk' ),
							'type' => 'text',
							'std'  => __( 'Quantity', 'wer_pk' ),
						),
						array(
							'id'   => 'wer_pk_seller_gst_label',
							'name' => __( 'GST Label', 'wer_pk' ),
							'type' => 'text',
							'std'  => __( 'GST', 'wer_pk' ),
						),
						array(
							'id'   => 'wer_pk_seller_product_price_label',
							'name' => __( 'Product Price Label', 'wer_pk' ),
							'type' => 'text',
							'std'  => __( 'Product Price', 'wer_pk' ),
						),
						array(
							'id'   => 'wer_pk_seller_discount_label',
							'name' => __( 'Discount Label', 'wer_pk' ),
							'type' => 'text',
							'std'  => __( 'Discount %', 'wer_pk' ),
						),
						array(
							'id'   => 'wer_pk_seller_update_button_text',
							'name' => __( 'Update Button Label', 'wer_pk' ),
							'type' => 'text',
							'std'  => __( 'Update Changes', 'wer_pk' ),
						),
						array(
							'id'   => 'wer_pk_seller_save_button_text',
							'name' => __( 'Save Button Label', 'wer_pk' ),
							'type' => 'text',
							'std'  => __( 'Save product', 'wer_pk' ),
						),
						array(
							'id'   => 'wer_pk_seller_variant_label',
							'name' => __( 'Variant Label', 'wer_pk' ),
							'type' => 'text',
							'std'  => __( 'Product Variants', 'wer_pk' ),
						),
						array(
							'id'   => 'wer_pk_seller_variant_submit_text',
							'name' => __( 'Variant Submit Label', 'wer_pk' ),
							'type' => 'text',
							'std'  => __( 'Submit', 'wer_pk' ),
						),
					),
				);
			}

			return $meta_boxes;
		}

		/**
		 * Resolve page ID by path.
		 *
		 * @param string $path Page slug.
		 *
		 * @return int
		 */
		private static function get_page_id_by_path( $path ) {
			$page = get_page_by_path( $path, OBJECT, 'page' );

			return $page ? (int) $page->ID : 0;
		}
	}
endif;
