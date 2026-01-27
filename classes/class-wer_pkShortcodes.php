<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\wer_pkShortcodes', false ) ) :
	/**
	 * Class Shortcodes
	 */
	class wer_pkShortcodes {
		
		public function __construct(){

			// Add hooks Initialization
			add_shortcode( 'wer_pk_frontProducts', array($this, 'wer_pk_front_Products_shortcode') );
			add_shortcode( 'wer_pk_frontendOrders', array($this, 'wer_pk_front_Orders_shortcode') );

			add_shortcode( 'wer_pk_frontendLogin', array($this, 'wer_pk_frontend_login_shortcode') );
			add_shortcode( 'wer_pk_frontendRegistration', array($this, 'wer_pk_frontend_registration_shortcode') );
			
		}

		// shortcode [wer_pk_frontProducts]
		function wer_pk_front_Products_shortcode() {
			ob_start();
			Products::getProducts();
			$temp_content = ob_get_contents();
			ob_end_clean();
			return $temp_content;
		}

		// shortcode [wer_pk_frontendOrders]
		function wer_pk_front_Orders_shortcode() {
			ob_start();
			OrderDetail::getCurrentUserOrders();
			$temp_content = ob_get_contents();
			ob_end_clean();
			return $temp_content;
		}

		// shortcode [wer_pk_frontendLogin]
		function wer_pk_frontend_login_shortcode() {
			
			if (is_user_logged_in()) {
				return '<p>' . __("You are already logged in.", "wer_pk") . '</p>';
			}

			ob_start();
			//require_once plugin_dir_path(WP_WER_PK_PLUGIN_FILE) . 'templates\form-login.php';
			require_once __DIR__ . '/../templates/form-login.php';
			$temp_content = ob_get_contents();
			ob_end_clean();
			return $temp_content;
		}

		// shortcode [wer_pk_frontendRegistration]
		function wer_pk_frontend_registration_shortcode() {
			ob_start();
			//require_once plugin_dir_path(WP_WER_PK_PLUGIN_FILE) . 'templates\registration.php';
			require_once __DIR__ . '/../templates/registration.php';
			$temp_content = ob_get_contents();
			ob_end_clean();
			return $temp_content;
		}

	}

endif;