<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\wer_pkeMail', false ) ) :
	/**
	 * Class Main
	 */
	class wer_pkeMail {
		
		public function __construct(){

			// Add hooks Initialization
			add_action( 'wer_pk_email_header', array( $this, 'email_header' ) );
			add_action( 'wer_pk_email_footer', array( $this, 'email_footer' ) );
			add_action( 'wer_pk_email_body', array( $this, 'email_body' ) );

			add_filter( 'wp_new_user_notification_email' , 'edit_user_notification_email', 10, 3 );
			
		}


		/**
		 * Get the email header.
		 *
		 * @param mixed $email_heading Heading for the email.
		 */
		public function email_header( $email_heading ) {

			// Start output buffering
			ob_start();
			
			require_once plugin_dir_path(WP_WER_PK_PLUGIN_FILE) . 'templates/emails/email-header.php';
			
			echo ob_get_clean();

		}

		/**
		 * Get the email footer.
		 */
		public function email_footer() {
			ob_start();
			require_once plugin_dir_path(WP_WER_PK_PLUGIN_FILE) . 'templates/emails/email-footer.php';
			echo ob_get_clean();
		}

		/**
		 * Get the email footer.
		 */
		public function email_body($mailContents) {
			
			$message = array();
			$i= 0;

			$message = $mailContents;

			/*
			foreach($mailContents as $contents){
				$message[$i] = $mailContents;
				$i++;
			}
			*/
			
			require_once plugin_dir_path(WP_WER_PK_PLUGIN_FILE) . 'templates/emails/new-orders.php';
			$temp_content = ob_get_contents();
			$temp_content = ob_get_clean();
			return $temp_content;
			//wc_get_template( 'emails/email-footer.php' );
		}

		/**
		 *
		 * @param string $email_heading Heading text.
		 * @param string $message       Email message.
		 * @param bool   $plain_text    Set true to send as plain text. Default to false.
		 *
		 * @return string
		 */
		public function wrap_message( $email_heading, $message, $plain_text = false ) {
			
			// Buffer.
			ob_start();

			do_action( 'wer_pk_email_header', $email_heading, null );

			//do_action( 'wer_pk_email_body', $message, null );
			
			if( is_array($message) || is_object($message) ){

				//do_action( 'wer_pk_email_body', $message, null );
				//print_r($message);
				//require_once plugin_dir_path(WP_WER_PK_PLUGIN_FILE) . 'templates/emails/new-orders.php';
				?>

				<table width="100%" cellpadding="5" cellspacing=="3" border='0' Class="sellerTable">
					<tr>
						<th><strong><?php echo __("Id", "wer_pk"); ?></strong></th>
						<th><strong><?php echo __("Bill Id", "wer_pk"); ?></strong></th>
						<th><strong><?php echo __("Store Name", "wer_pk"); ?></strong></th>
						<th><strong><?php echo __("Product id", "wer_pk"); ?></strong></th>
						<th><strong><?php echo __("Product", "wer_pk"); ?></strong></th>
						<th><strong><?php echo __("Quantity", "wer_pk"); ?></strong></th>
						<th><strong><?php echo __("GST", "wer_pk"); ?></strong></th>
						<th><strong><?php echo __("Price", "wer_pk"); ?></strong></th>
						<th><strong><?php echo __("Discount %", "wer_pk"); ?></strong></th>
					</tr>
					<?php foreach ( $message as $result) { ?>
					<tr>
						<td><?php echo esc_html($result->id) ?></td>
						<td><?php echo esc_html($result->billid) ?></td>
						<td><?php echo esc_html($result->supplierName) ?></td>
						<td><?php echo esc_html($result->productid) ?></td>
						<td><?php echo esc_html($result->materialsName) ?></td>
						<td><?php echo esc_html($result->quantity) ?></td>
						<td><span class="dashicons-before <?php echo esc_attr(Settings::set_currency_symbol()); ?>"></span>: <?php echo esc_html($result->GST) ?></td>
						<td><span class="dashicons-before <?php echo esc_attr(Settings::set_currency_symbol()); ?>"></span>: <?php echo esc_html($result->totalPrice) ?></td>
						<td><?php echo esc_html($result->discount) ?> % </td>
					</tr>
					<?php } ?>
				</table>

				<?php

			} else {

				echo wpautop( wptexturize( $message ) ); // WPCS: XSS ok.

			}

			

			do_action( 'wer_pk_email_footer', null );

			// Get contents.
			$message = ob_get_clean();

			return $message;
		}

		/**
		 * Send the email.
		 *
		 * @param mixed  $to          Receiver.
		 * @param mixed  $subject     Email subject.
		 * @param mixed  $message     Message.
		 * @param string $headers     Email headers (default: "Content-Type: text/html\r\n").
		 * @param string $attachments Attachments (default: "").
		 * @return bool
		 */
		public function send( $to, $subject, $message, $headers = "Content-Type: text/html\r\n", $attachments = '' ) {
			// Send.
			$email = new wer_pkeMail();
			return $email->send( $to, $subject, $message, $headers, $attachments );
		}



		public function edit_user_notification_email( $wp_new_user_notification_email, $user, $blogname ) {

			$newpass = wp_generate_password( 10, true, false );
			wp_set_password($newpass,$user->ID);
			$message = sprintf(__( "Welcome to %s! Here's how to log in:", "wer_pk" ), $blogname ) . "\r\n";
			//$message .= wp_login_url() . "\r\n\r\n";
			$message .= site_url( 'login' ) . "\r\n\r\n";
			$message .= sprintf(__( 'Username: %s', "wer_pk" ), $user->user_login ) . "\r\n";
			$message .= sprintf(__( 'Password: %s', "wer_pk" ), $newpass) . "\r\n\r\n";
			$message .= sprintf(__( 'If you have any problems, please contact me at %s.', "wer_pk"), get_option( 'admin_email' ) ) . "\r\n";
			$message .= sprintf(__($blogname));
    
			$wp_new_user_notification_email['message'] = $message;
    
			return $wp_new_user_notification_email;

		}		

	}

endif;
