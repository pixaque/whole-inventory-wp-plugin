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
			$login_page_id = self::get_page_id_by_path( 'login' );

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
							'id'      => 'wer_pk_login_heading',
							'name'    => __( 'Form Heading', 'wer_pk' ),
							'type'    => 'text',
							'std'     => __( 'Login', 'wer_pk' ),
						),
						array(
							'id'      => 'wer_pk_login_button_text',
							'name'    => __( 'Button Label', 'wer_pk' ),
							'type'    => 'text',
							'std'     => __( 'Log In', 'wer_pk' ),
						),
						array(
							'id'      => 'wer_pk_login_show_register_link',
							'name'    => __( 'Show registration link', 'wer_pk' ),
							'type'    => 'checkbox',
							'std'     => 1,
						),
						array(
							'id'      => 'wer_pk_login_register_url',
							'name'    => __( 'Registration URL', 'wer_pk' ),
							'type'    => 'url',
							'std'     => home_url( '/registration/' ),
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
