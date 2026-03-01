<?php
/**
 * Redux Framework settings registration.
 *
 * @package APGI
 */

namespace APGI;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers Redux option panel and sections.
 */
class Redux_Config {

	/**
	 * Register Redux options.
	 *
	 * @return void
	 */
	public static function register(): void {
		if ( ! class_exists( '\\Redux' ) ) {
			add_action( 'admin_notices', array( __CLASS__, 'missing_redux_notice' ) );
			return;
		}

		$opt_name = Settings::OPTION_NAME;

		\Redux::set_args(
			$opt_name,
			array(
				'opt_name'           => $opt_name,
				'display_name'       => __( 'All Purpose General Inventory', 'wer_pk' ),
				'menu_title'         => __( 'Inventory Settings', 'wer_pk' ),
				'page_title'         => __( 'Inventory Settings', 'wer_pk' ),
				'menu_type'          => 'menu',
				'allow_sub_menu'     => true,
				'menu_icon'          => 'dashicons-archive',
				'admin_bar'          => false,
				'dev_mode'           => false,
				'update_notice'      => false,
				'customizer'         => false,
				'page_permissions'   => 'manage_options',
				'page_priority'      => 58,
				'show_options_object'=> false,
				'defaults'           => Settings::defaults(),
			)
		);

		\Redux::set_section(
			$opt_name,
			array(
				'title'  => __( 'General', 'wer_pk' ),
				'id'     => 'apgi_general',
				'icon'   => 'el el-cog',
				'fields' => array(
					array(
						'id'       => 'brand_name',
						'type'     => 'text',
						'title'    => __( 'Brand Name', 'wer_pk' ),
						'default'  => Settings::defaults()['brand_name'],
						'validate' => 'no_html',
					),
					array(
						'id'      => 'company_logo',
						'type'    => 'media',
						'title'   => __( 'Company Logo', 'wer_pk' ),
						'library_filter' => array( 'jpg', 'jpeg', 'png', 'svg', 'webp' ),
					),
					array(
						'id'       => 'currency',
						'type'     => 'select',
						'title'    => __( 'Default Currency', 'wer_pk' ),
						'options'  => Settings::currency_choices(),
						'default'  => Settings::defaults()['currency'],
					),
					array(
						'id'       => 'products_per_page',
						'type'     => 'spinner',
						'title'    => __( 'Products Per Page', 'wer_pk' ),
						'default'  => 20,
						'min'      => 1,
						'max'      => 200,
						'step'     => 1,
					),
				),
			)
		);

		\Redux::set_section(
			$opt_name,
			array(
				'title'      => __( 'Frontend Access', 'wer_pk' ),
				'id'         => 'apgi_frontend',
				'icon'       => 'el el-user',
				'subsection' => false,
				'fields'     => array(
					array(
						'id'      => 'enable_registration',
						'type'    => 'switch',
						'title'   => __( 'Enable Frontend Registration', 'wer_pk' ),
						'default' => true,
					),
					array(
						'id'       => 'login_redirect_page',
						'type'     => 'text',
						'title'    => __( 'Login Redirect Slug', 'wer_pk' ),
						'subtitle' => __( 'Slug only (example: seller-account).', 'wer_pk' ),
						'default'  => 'seller-account',
						'validate' => 'no_special_chars',
					),
					array(
						'id'       => 'order_page_slug',
						'type'     => 'text',
						'title'    => __( 'Orders Page Slug', 'wer_pk' ),
						'default'  => 'orders',
						'validate' => 'no_special_chars',
					),
				),
			)
		);

		\Redux::set_section(
			$opt_name,
			array(
				'title'  => __( 'Store Locations', 'wer_pk' ),
				'id'     => 'apgi_locations',
				'icon'   => 'el el-map-marker',
				'fields' => array(
					array(
						'id'       => 'store_locations',
						'type'     => 'repeater',
						'title'    => __( 'Store Locations', 'wer_pk' ),
						'fields'   => array(
							array(
								'id'       => 'name',
								'type'     => 'text',
								'title'    => __( 'Location Name', 'wer_pk' ),
								'validate' => 'no_html',
							),
							array(
								'id'       => 'address',
								'type'     => 'textarea',
								'title'    => __( 'Address', 'wer_pk' ),
								'validate' => 'no_html',
							),
						),
						'default'  => array(),
					),
				),
			)
		);

		\Redux::set_section(
			$opt_name,
			array(
				'title'  => __( 'Email', 'wer_pk' ),
				'id'     => 'apgi_email',
				'icon'   => 'el el-envelope',
				'fields' => array(
					array(
						'id'       => 'email_footer_text',
						'type'     => 'textarea',
						'title'    => __( 'Email Footer Text', 'wer_pk' ),
						'default'  => Settings::defaults()['email_footer_text'],
						'validate' => 'no_html',
					),
				),
			)
		);
	}

	/**
	 * Missing Redux warning.
	 *
	 * @return void
	 */
	public static function missing_redux_notice(): void {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		echo '<div class="notice notice-warning"><p>' . esc_html__( 'All Purpose General Inventory Plugin requires Redux Framework to manage settings.', 'wer_pk' ) . '</p></div>';
	}
}
