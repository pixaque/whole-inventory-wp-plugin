<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\WP_WER_PK_Blocks\Settings', false ) ) :
	/**
	 * Class Settings
	 */
	class Settings {
		/**
		 * Prefix for plugin settings.
		 *
		 * @var string
		 */
		const OPTION_PREFIX = 'wp_wer_pk_blocks';

		/**
		* Menu slug.
		*
		* @var string
		*/
		const MENU_SLUG = 'wp_wer_pk_settings';

		/**
		 * Name of bootstrap version constant.
		 *
		 * @var string
		 */
		const WER_PK_CURRENCY = 'WP_WER_PK_CURRENCY';

		const WER_PK_SAMPLE_DATA = 'WP_WER_PK_SAMPLE_DATA';
		

		/**
		 * Name of bootstrap version option.
		 *
		 * @var string
		 */
		const BOOTSTRAP_VERSION_OPTION_NAME = self::OPTION_PREFIX . 'bootstrap_version';

		/**
		 * Default bootstrap version value.
		 *
		 * @var int
		 */
		const BOOTSTRAP_VERSION_DEFAULT_VALUE = '5';

		/**
		 * The plugin assets directory.
		 *
		 * @var string
		 */
		public static $assets_dir = '';

		/**
		 * The plugin assets URL.
		 *
		 * @var string
		 */
		public static $assets_url = '';

		/**
		 * True if settings are already initialized.
		 *
		 * @var bool
		 */
		private static $initialized = false;

		public static $currencies = array(
				'Please Select a Currency.' => array('symbol' => '', 'class' => ''),
				'Us_Dollar' => array('symbol' => '$', 'class' => 'dashicons-Us_Dollar'),
				'Uk_Pound' => array('symbol' => '£', 'class' => 'dashicons-Uk_Pound'),
				'Japanese_Yen' => array('symbol' => '¥', 'class' => 'dashicons-Japanese_Yen'),
				'Pakistani_Rupees' => array('symbol' => '₨', 'class' => 'dashicons-Pakistani_Rupees'),
				'Indian_Rupees' => array('symbol' => '₹', 'class' => 'dashicons-Indian_Rupees'),
				'Euro' => array('symbol' => '€', 'class' => 'dashicons-Euro'),
				'Australian_Dollar' => array('symbol' => 'A$', 'class' => 'dashicons-Australian_Dollar'),
				'Canadian_Dollar' => array('symbol' => 'C$', 'class' => 'dashicons-Canadian_Dollar'),
				'Swiss_Franc' => array('symbol' => 'CHF', 'class' => 'dashicons-Swiss_Franc'),
				'Chinese_Yuan' => array('symbol' => '¥', 'class' => 'dashicons-Chinese_Yuan'),
				'Brazilian_Real' => array('symbol' => 'R$', 'class' => 'dashicons-Brazilian_Real'),
				'South_African_Rand' => array('symbol' => 'R', 'class' => 'dashicons-South_African_Rand'),
				'Mexican_Peso' => array('symbol' => '$', 'class' => 'dashicons-Mexican_Peso'),
				'Russian_Ruble' => array('symbol' => '₽', 'class' => 'dashicons-Russian_Ruble'),
				'Turkish_Lira' => array('symbol' => '₺', 'class' => 'dashicons-Turkish_Lira'),
				'Singapore_Dollar' => array('symbol' => 'S$', 'class' => 'dashicons-Singapore_Dollar'),
				'New_Zealand_Dollar' => array('symbol' => 'NZ$', 'class' => 'dashicons-New_Zealand_Dollar'),
				'Norwegian_Krone' => array('symbol' => 'kr', 'class' => 'dashicons-Norwegian_Krone'),
				'Swedish_Krona' => array('symbol' => 'kr', 'class' => 'dashicons-Swedish_Krona'),
				'Danish_Krone' => array('symbol' => 'kr', 'class' => 'dashicons-Danish_Krone'),
				'Thai_Baht' => array('symbol' => '฿', 'class' => 'dashicons-Thai_Baht'),
				'Indonesian_Rupiah' => array('symbol' => 'Rp', 'class' => 'dashicons-Indonesian_Rupiah'),
				'Malaysian_Ringgit' => array('symbol' => 'RM', 'class' => 'dashicons-Malaysian_Ringgit'),
				'Hong_Kong_Dollar' => array('symbol' => 'HK$', 'class' => 'dashicons-Hong_Kong_Dollar'),
				'Philippine_Peso' => array('symbol' => '₱', 'class' => 'dashicons-Philippine_Peso'),
				'Vietnamese_Dong' => array('symbol' => '₫', 'class' => 'dashicons-Vietnamese_Dong'),
				'Colombian_Peso' => array('symbol' => 'COL$', 'class' => 'dashicons-Colombian_Peso'),
				'Argentine_Peso' => array('symbol' => '$', 'class' => 'dashicons-Argentine_Peso'),
				'Saudi_Riyal' => array('symbol' => 'ر.س', 'class' => 'dashicons-Saudi_Riyal'),
				'UAE_Dirham' => array('symbol' => 'د.إ', 'class' => 'dashicons-UAE_Dirham'),
				'Qatari_Riyal' => array('symbol' => 'ر.ق', 'class' => 'dashicons-Qatari_Riyal'),
				'Kuwaiti_Dinar' => array('symbol' => 'د.ك', 'class' => 'dashicons-Kuwaiti_Dinar'),
				'Bahraini_Dinar' => array('symbol' => 'ب.د', 'class' => 'dashicons-Bahraini_Dinar'),
				'Omani_Rial' => array('symbol' => 'ر.ع.', 'class' => 'dashicons-Omani_Rial'),
				'Jamaican_Dollar' => array('symbol' => 'J$', 'class' => 'dashicons-Jamaican_Dollar'),
				'Trinidad_and_Tobago_Dollar' => array('symbol' => 'TT$', 'class' => 'dashicons-Trinidad_and_Tobago_Dollar'),
				'Bulgarian_Lev' => array('symbol' => 'лв', 'class' => 'dashicons-Bulgarian_Lev'),
				'Romanian_Leu' => array('symbol' => 'lei', 'class' => 'dashicons-Romanian_Leu'),
				'Croatian_Kuna' => array('symbol' => 'kn', 'class' => 'dashicons-Croatian_Kuna'),
				'Serbian_Dinar' => array('symbol' => 'дин.', 'class' => 'dashicons-Serbian_Dinar'),
				'Czech_Koruna' => array('symbol' => 'Kč', 'class' => 'dashicons-Czech_Koruna'),
				'Hungarian_Forint' => array('symbol' => 'Ft', 'class' => 'dashicons-Hungarian_Forint'),
				'Lithuanian_Litas' => array('symbol' => 'Lt', 'class' => 'dashicons-Lithuanian_Litas'),
				'Latvian_Lats' => array('symbol' => 'Ls', 'class' => 'dashicons-Latvian_Lats'),
				'Slovak_Koruna' => array('symbol' => 'Sk', 'class' => 'dashicons-Slovak_Koruna'),
				'Georgian_Lari' => array('symbol' => '₾', 'class' => 'dashicons-Georgian_Lari'),
				'Armenian_Dram' => array('symbol' => '֏', 'class' => 'dashicons-Armenian_Dram'),
				'Azerbaijani_Manat' => array('symbol' => '₼', 'class' => 'dashicons-Azerbaijani_Manat'),
				'Bangladeshi_Taka' => array('symbol' => '৳', 'class' => 'dashicons-Bangladeshi_Taka'),
				'Nepalese_Rupee' => array('symbol' => 'Rs', 'class' => 'dashicons-Nepalese_Rupee'),
				'Sri_Lankan_Rupee' => array('symbol' => 'Rs', 'class' => 'dashicons-Sri_Lankan_Rupee'),
				'Cambodian_Riel' => array('symbol' => '៛', 'class' => 'dashicons-Cambodian_Riel'),
				'Mongolian_Tugrik' => array('symbol' => '₮', 'class' => 'dashicons-Mongolian_Tugrik'),
				'Zambian_Kwacha' => array('symbol' => 'ZK', 'class' => 'dashicons-Zambian_Kwacha'),
				'Kenyan_Shilling' => array('symbol' => 'KSh', 'class' => 'dashicons-Kenyan_Shilling'),
				'Tanzanian_Shilling' => array('symbol' => 'TSh', 'class' => 'dashicons-Tanzanian_Shilling'),
				'Ugandan_Shilling' => array('symbol' => 'USh', 'class' => 'dashicons-Ugandan_Shilling'),
				'Rwandan_Franc' => array('symbol' => 'RF', 'class' => 'dashicons-Rwandan_Franc'),
				'Burundian_Franc' => array('symbol' => 'FBu', 'class' => 'dashicons-Burundian_Franc'),
				'East_Carribean_Dollar' => array('symbol' => 'EC$', 'class' => 'dashicons-East_Carribean_Dollar'),
				'Fijian_Dollar' => array('symbol' => 'FJ$', 'class' => 'dashicons-Fijian_Dollar'),
				'Papua_New_Guinean_Kina' => array('symbol' => 'K', 'class' => 'dashicons-Papua_New_Guinean_Kina'),


			);

		public static $sampleData = array(
			'Please Select Data to import.' => array(
				'file_name' => '',
				'description' => ''
			),
			'Construction Site Inventory' => array(
				'file_name' => 'construction_inventory.csv',
				'description' => 'Sample data for Real Estate Invontory.'
			),
			'Retail Store Inventory' => array(
				'file_name' => 'store_inventory.csv',
				'description' => 'Sample data for Store Inventory.'
			),
			'Warehouse Inventory' => array(
				'file_name' => 'warehouse_inventory.csv',
				'description' => 'Sample data for Warehouse Inventory.'
			),
			'Healthcare Facility' => array(
				'file_name' => 'healthcare_facility.csv',
				'description' => 'Sample data for Healthcare Facility.'
			),
			'Food and Beverage' => array(
				'file_name' => 'food_and_beverage.csv',
				'description' => 'Sample data for Food and Beverage.'
			)
		);


		/**
		 * Settings constructor.
		 *
		 * @param string $assets_dir The plugin assets directory.
		 * @param string $assets_url The plugin assets URL.
		 */
		public static function init( $assets_dir, $assets_url ) {

			if ( ! self::$initialized ) {
				self::$assets_dir = $assets_dir;
				self::$assets_url = $assets_url;

				// Add settings page to menu
				//add_action( 'admin_menu', array( __CLASS__, 'add_menu_item' ) );

				// Register plugin settings
				add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );

				// Add settings link to plugin list table
				add_filter(
					'plugin_action_links_' . plugin_basename( WP_WER_PK_PLUGIN_FILE ),
					array(
						__CLASS__,
						'add_settings_link',
					)
				);

				// Filter saving of bootstrap version
				add_filter( 'pre_update_option_' . self::BOOTSTRAP_VERSION_OPTION_NAME, array( __CLASS__, 'pre_update_option_bootstrap_version' ), 10, 2 );

				// Enqueue settings stylesheet
				//add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_styles' ) );
				add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_styles' ) );

				add_action('dummyNotice', array( __CLASS__, 'dummyNoticeDisplay' ) );
				

				self::$initialized = true;
			}
		}

		/**
		 * Enqueue settings specific styles.
		 *
		 * @param string $hook Hook of current screen.
		 */
		public static function enqueue_styles( $hook ) {
			if ( 'settings_page_' . self::MENU_SLUG !== $hook ) {
				return;
			}

			$settings_styles_path = self::$assets_dir . '/build/settings.css';
			$settings_styles_url = esc_url( self::$assets_url ) . '/build/settings.css';
			$settings_asset_file = WP_BOOTSTRAP_BLOCKS_PLUGIN_FILE . '/build/settings.asset.php';

			$settings_asset = file_exists( $settings_asset_file )
				? require_once $settings_asset_file
				: null;

			$settings_version = isset( $settings_asset['version'] ) ? $settings_asset['version'] : filemtime( $settings_styles_path );

			wp_register_style( self::MENU_SLUG . '_styles', $settings_styles_url, false, $settings_version );
			wp_enqueue_style( self::MENU_SLUG . '_styles' );
		}


		/**
		 * Load settings page content.
		 */
		public static function projects_page() {

			if ( class_exists( '\Projects', false ) ) {

				//add_action( 'wp_head', '\WP_WER_PK_Blocks\Projects::projects_page', 10, 3 );
				global $WER_PK_Projects;
				$WER_PK_Projects = new Projects();

				//\WP_WER_PK_Blocks\Projects::projects_page();
			}

		}

		/**
		 * Load settings page content.
		 */
		public static function main_page() {

			if ( class_exists( '\Main', false ) ) {

				global $WER_PK_Main;
				$WER_PK_main = new Main();

				//$WER_PK_main->Main_page();

			}

		}

		/**
		 * Add settings link to plugin list table.
		 *
		 * @param  array $links Existing links.
		 *
		 * @return array Modified links
		 */
		public static function add_settings_link( $links ) {
			$settings_link = '<a href="' . esc_url( admin_url( 'admin.php?page=' . self::MENU_SLUG ) ) . '">' . esc_html__( 'Settings', 'wer_pk' ) . '</a>';
			// add settings link as first element
			array_unshift( $links, $settings_link );

			return $links;
		}

		/**
		 * Register plugin settings.
		 */
		public static function register_settings() {
			$section = 'default';


			$settings_fields = array(
				array(
					'option_name' => self::BOOTSTRAP_VERSION_OPTION_NAME,
					'label' => __( 'Select Currency', 'wer_pk' ),
					'description' => __( 'Selected currency will be rendered on the front end.', 'wer_pk' ),
					'type' => 'select',
					'default' => self::BOOTSTRAP_VERSION_DEFAULT_VALUE,
					'options' => array_keys(self::$currencies), // use currency keys
					'constant_name' => self::WER_PK_CURRENCY,
					'disabled' => false,
				),
				array(
					'option_name' => "sample_data_select",
					'label' => __( 'Select Sample Data', 'wer_pk' ),
					'description' => __( 'Select sample data, save it and then import it.', 'wer_pk' ),
					'type' => 'select',
					'default' => __("Please Select Data to import.", "wer_pk"),
					'options' => array_keys(self::$sampleData), // use Sample Data keys
					'constant_name' => self::WER_PK_SAMPLE_DATA,
					'disabled' => false,
				)
			);

			// Add section to page
			add_settings_section(
				$section,
				__( 'Main settings', 'wer_pk' ),
				array(
					__CLASS__,
					'settings_section',
				),
				self::MENU_SLUG
			);

			foreach ( $settings_fields as $field ) {
				// Register field
				register_setting( self::MENU_SLUG, $field['option_name'] );

				$field_args = array(
					'field' => $field,
				);
				// add label_for argument to all fields which haven't an additional label
				if ( 'radio' !== $field['type'] ) {
					$field_args['label_for'] = $field['option_name'];
				}

				// Add field to page
				add_settings_field(
					$field['option_name'],
					$field['label'],
					array(
						__CLASS__,
						'display_field',
					),
					self::MENU_SLUG,
					$section,
					$field_args
				);
			}
		}

		/**
		 * Print settings section.
		 *
		 * @param array $section Settings section.
		 */
		public static function settings_section( $section ) {
		}

				/**
		 * Load settings page content.
		 */
		public static function settings_page() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wer_pk' ) );
			}
			?>
			<div class="wrap" id="<?php echo esc_attr( self::MENU_SLUG ); ?>">
				<?php do_action('dummyNotice'); ?>

				<form method="post" action="options.php" enctype="multipart/form-data">
					<?php
					// Get settings fields
					settings_fields( self::MENU_SLUG );
					do_settings_sections( self::MENU_SLUG );
					submit_button();
					?>
				</form>
				<?PHP 
					$sampleData = [];
					$sampleData = self::setwer_pk_SampleData();

					if($sampleData["file_name"]){

				?>

				<button 
					type="button" 
					onClick="getCSVData(<?php echo __("`Action is not reversable. All previous Data will be deleted. Want to proceed?`", "wer_pk"); ?>, <?php echo "'".$sampleData["file_name"]."'" ?>);" 
					class="smooth btn-c btn" 
				><?php echo __("IMPORT SAMPLE DATA", "wer_pk"); ?> <span class="spinner" id="mainSpinner" style="visibility: visible;"></span></button>

				<?php } ?>

				<table class="form-table" role="presentation">
					<tbody>
						<tr>
							<td>
								<h3><?php echo __("Plugin Developer's Disclaimer", "wer_pk"); ?></h3>
								<h5><?php echo __("By using this software, you acknowledge and agree to the following:", "wer_pk"); ?></h5>
								<p><strong><?php echo __("Prohibited and Illegal Goods:", "wer_pk"); ?></strong> <?php echo __("You are fully responsible for ensuring that any goods you manage, handle, or distribute through this software comply with all applicable laws and regulations. We do not support or condone the sale or distribution of prohibited or illegal items.", "wer_pk"); ?></p>
								<p><strong><?php echo __("No Responsibility:", "wer_pk"); ?></strong> <?php echo __("We explicitly deny any responsibility for actions taken by users that involve prohibited or illegal goods. If you choose to proceed with such items, you do so at your own risk and will bear all legal consequences.", "wer_pk"); ?></p>
								<p><strong><?php echo __("Tax Obligations:", "wer_pk"); ?></strong> <?php echo __("You are responsible for understanding and fulfilling all tax obligations associated with your transactions. We recommend consulting with a tax professional to ensure compliance with all relevant tax laws.", "wer_pk"); ?></p>
								<p><?php echo __("This disclaimer serves as an agreement between you and the software provider. By using this software, you agree to these terms without the need for explicit consent.", "wer_pk"); ?></p>
								<p><?php echo __("* If you do not agree with these terms, you must refrain from using this software.", "wer_pk"); ?></p>
							</td>
						</tr>
					</tbody>
				</table>

			</div><!--end #wrap -->
			<?php
		}

		/**
		 * Generate HTML for displaying fields
		 *
		 * @param array $data Additional data which is added in add_settings_field() method.
		 */
		public static function display_field( $data = array() ) {
			// Get field info
			if ( ! isset( $data['field'] ) ) {
				_doing_it_wrong( __FUNCTION__, esc_html__( 'Field data missing.', 'wer_pk' ), esc_attr( WP_Bootstrap_Blocks::$version ) );
			}

			$field = $data['field'];

			$is_option_constant_set = ! empty( $field['constant_name'] ) && defined( $field['constant_name'] );
			if ( $is_option_constant_set ) {
				$option_value = constant( $field['constant_name'] );
				$disabled = true;
			} else {
				if ( isset( $field['default'] ) ) {
					$option_value = get_option( $field['option_name'], $field['default'] );
				} else {
					$option_value = get_option( $field['option_name'], '' );
				}
				$disabled = array_key_exists( 'disabled', $field ) ? $field['disabled'] : false;
			}

			$placeholder = ( array_key_exists( 'placeholder', $field ) ? $field['placeholder'] : '' );
			$html = '';

			switch ( $field['type'] ) {
				case 'text':
				case 'url':
				case 'email':
					$html .= '<input id="' . esc_attr( $field['option_name'] ) . '" type="text" name="' . esc_attr( $field['option_name'] ) . '" placeholder="' . esc_attr( $placeholder ) . '" value="' . esc_attr( $option_value ) . '" ' . disabled( $disabled, true, false ) . '/>' . "\n";
					break;

				case 'textarea':
					$html .= '<textarea id="' . esc_attr( $field['option_name'] ) . '" rows="5" cols="50" name="' . esc_attr( $field['option_name'] ) . '" placeholder="' . esc_attr( $placeholder ) . '" ' . disabled( $disabled, true, false ) . '>' . $option_value . '</textarea>' . "\n";
					break;

				case 'checkbox':
					$html .= '<input id="' . esc_attr( $field['option_name'] ) . '" type="checkbox" name="' . esc_attr( $field['option_name'] ) . '" value="1" ' . checked( '1', $option_value, false ) . ' ' . disabled( $disabled, true, false ) . '/>' . "\n";
					break;

				case 'radio':
					foreach ( $field['options'] as $k => $v ) {
						$html .= '<p><label for="' . esc_attr( $field['option_name'] . '_' . $k ) . '"><input type="radio" id="' . esc_attr( $field['option_name'] . '_' . $k ) . '" name="' . esc_attr( $field['option_name'] ) . '" value="' . esc_attr( $k ) . '" ' . checked( strval( $k ), strval( $option_value ), false ) . ' ' . disabled( $disabled, true, false ) . ' /> ' . $v . '</label></p>' . "\n";
					}
					break;

				case 'select':
					$html .= '<select name="' . esc_attr( $field['option_name'] ) . '" id="' . esc_attr( $field['option_name'] ) . '"' . disabled( $disabled, true, false ) . '>' . "\n";
					foreach ( $field['options'] as $k => $v ) {
						$html .= '<option ' . selected( strval( $k ), strval( $option_value ), false ) . ' value="' . esc_attr( $k ) . '">' . $v . '</option>' . "\n";
					}
					$html .= '</select>' . "\n";
					break;
			}

			if ( array_key_exists( 'description', $field ) ) {
				$html .= '<p class="description">' . esc_html( $field['description'] ) . '</p>' . "\n";
			}

			if ( $is_option_constant_set ) {
				$html .= '<p class="description constant-notice">' .
					sprintf(
						// translators: %s contains constant name
						esc_html_x(
							'Option is defined in the following constant: %s',
							'%s contains constant name',
							'wer_pk'
						),
						'<code>' . esc_html( $field['constant_name'] ) . '</code>'
					) . '</p>' . "\n";
			}

			// @codingStandardsIgnoreStart
			echo $html;
			// @codingStandardsIgnoreEnd
		}

		/**
		 * Always use constant value for bootstrap version if set.
		 *
		 * @param string $new_value The new, unserialized option value.
		 * @param string $old_value The old option value.
		 *
		 * @return string
		 */
		public static function pre_update_option_bootstrap_version( $new_value, $old_value ) {
			return defined( self::WER_PK_CURRENCY ) ? strval( constant( self::WER_PK_CURRENCY ) ) : $new_value;
		}

		/**
		 * Only enable CSS grid if bootstrap version is >= 5 and always use constant value if set.
		 *
		 * @param string $new_value The new, unserialized option value.
		 * @param string $old_value The old option value.
		 *
		 * @return string
		 */
		public static function pre_update_option_css_grid_enabled( $new_value, $old_value ) {
			return self::is_bootstrap_5_active()
				? defined( self::ENABLE_CSS_GRID_CONSTANT_NAME ) ? boolval( constant( self::ENABLE_CSS_GRID_CONSTANT_NAME ) ) : $new_value
				: false;
		}

		/**
		 * Get bootstrap version option.
		 *
		 * @return string Bootstrap version from options.
		 */
		public static function get_bootstrap_version() {
			return strval( self::get_option( self::BOOTSTRAP_VERSION_OPTION_NAME, self::BOOTSTRAP_VERSION_CONSTANT_NAME, self::BOOTSTRAP_VERSION_DEFAULT_VALUE ) );
		}

		/**
		 * Get bootstrap version option.
		 *
		 * @return string Bootstrap version from options.
		 */
		public static function get_currency() {
			//return strval( self::get_option( self::BOOTSTRAP_VERSION_OPTION_NAME, self::BOOTSTRAP_VERSION_CONSTANT_NAME, self::BOOTSTRAP_VERSION_DEFAULT_VALUE ) );
			// Retrieve the selected currency from the options table
			$a = get_option(self::BOOTSTRAP_VERSION_OPTION_NAME, self::BOOTSTRAP_VERSION_DEFAULT_VALUE);
			return $a;
		}

		/**
		 * Get bootstrap version option.
		 *
		 * @return string Bootstrap version from options.
		 */
		public static function get_sample_data() {
			//return strval( self::get_option( self::BOOTSTRAP_VERSION_OPTION_NAME, self::BOOTSTRAP_VERSION_CONSTANT_NAME, self::BOOTSTRAP_VERSION_DEFAULT_VALUE ) );
			// Retrieve the selected currency from the options table
			$a = get_option("sample_data_select", "Please Select Data to import.");
			return $a;
		}


		/**
		 * Get option value in the following order:
		 * - from constant if defined
		 * - from database
		 * - default value
		 *
		 * @param string $option_name Name of option.
		 * @param string $constant_name Name of constant.
		 * @param mixed  $default_value Default value if option is not set.
		 *
		 * @return mixed
		 */
		public static function get_option( $option_name, $constant_name, $default_value ) {
			return defined( $constant_name ) ? constant( $constant_name ) : get_option( $option_name, $default_value );
		}

		public static function set_currency_symbol(){
			
			$selected_currency = Settings::get_currency();

			// Convert to a numerically indexed array
			$indexed_currencies = array_values(Settings::$currencies);

			$currency_info = $indexed_currencies[$selected_currency]; // Get the selected currency info

			return $currency_info['class'];
		}


		public static function get_currency_symbol(){
			
			$selected_currency = Settings::get_currency();

			// Convert to a numerically indexed array
			$indexed_currencies = array_values(Settings::$currencies);

			$currency_info = $indexed_currencies[$selected_currency]; // Get the selected currency info

			return $currency_info['symbol'];
		}


		public static function setwer_pk_SampleData(){
			
			$selected_sampleData = Settings::get_sample_data();

			// Convert to a numerically indexed array
			$indexed_sampleData = array_values(Settings::$sampleData);

			$final_sampleData = $indexed_sampleData[$selected_sampleData]; // Get the selected currency info

			return $final_sampleData;
		}

		public static function dummyNoticeDisplay(){
	
			if(esc_attr(self::get_sample_data()) > 0) {
		?>
			<div class="msg warning">
				<strong><?php echo __( "Dummy Content: ", 'wer_pk' ); ?></strong>
				<?php echo __( "Get yourself familiar with the plugin. Press reset content when ready to operate properly.", 'wer_pk' ); ?>
				<button 
					type="button" 
					name="Save" 
					id="SaveMe" 
					class="button button-secondary button-large"
					onClick="wer_pk_resetData('Tables reset successfully. Please proceed with real data.');"
					>
					<?php echo __('Reset Content.', 'wer_pk' );?> <span class="spinner" id="spinnerContent" style="visibility: visible;"></span>
				</button>
			</div>
		<?php
			} 
	
		}


		public static function reset_wer_pk_tables($message) {
			global $wpdb;

			$message = $_REQUEST["message"] ?? "";

			 // Step 1: Disable foreign key checks
			$wpdb->query("SET FOREIGN_KEY_CHECKS = 0;");

			// Step 1: Empty specified tables
			$tables = [
				$wpdb->base_prefix . 'projects',
				$wpdb->base_prefix . 'projects_details',
				$wpdb->base_prefix . 'project_order',
				$wpdb->base_prefix . 'products',
				$wpdb->base_prefix . 'product_attributes',
				$wpdb->base_prefix . 'product_variants',
				$wpdb->base_prefix . 'variant_attributes',
			];

			foreach ($tables as $table) {
				$wpdb->query("TRUNCATE TABLE $table");
			}

			// Step 3: Re-enable foreign key checks
			$wpdb->query("SET FOREIGN_KEY_CHECKS = 1;");

			if($message){
				update_option('sample_data_select', 0);

				return wp_send_json('Tables reset successfully. Please proceed with real data.');
			}

		}

		public static function empty_and_populate_tables() {
			global $wpdb;

			self::reset_wer_pk_tables("");

			// Step 2: Determine the CSV file path safely
			$file_name = $_REQUEST['fileName'] ?? '';
			if (empty($file_name)) {
				return wp_send_json(['error' => 'No file specified']);
			}

			// Use plugin_dir_path instead of plugin_dir_url to get filesystem path
			$file_path = plugin_dir_path(WP_WER_PK_PLUGIN_FILE) . 'sample_data/' . basename($file_name);

			if (!file_exists($file_path)) {
				return wp_send_json(['error' => 'CSV file does not exist: ' . $file_path]);
			}

			if (($handle = fopen($file_path, 'r')) !== FALSE) {
				while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {
					$table_name = $row[0];

					// Handling projects
					if ($table_name === $wpdb->base_prefix . 'projects') {
						$data = [
							'site_name' => $row[1],
							'site_name' => $row[2],
							'site_size' => $row[3],
							'site_location' => $row[4],
							'start_date' => $row[5],
							'status' => $row[6],
						];
						$result = $wpdb->insert($wpdb->base_prefix . 'projects', $data);
						error_log('Insert projects: ' . json_encode($result));
					}

					// Handling projects_details
					if ($table_name === $wpdb->base_prefix . 'projects_details') {
						$data = [
							'projectid' => $row[2],
							'billNo' => $row[3],
							'expenseType' => $row[4],
							'billdate' => $row[5],
							'description' => $row[6],
							'orderTotal' => $row[7],
							'confirmed' => $row[8],
						];
						$result = $wpdb->insert($wpdb->base_prefix . 'projects_details', $data);
						error_log('Insert projects_details: ' . json_encode($result));
					}

					// Handling project_order
					if ($table_name === $wpdb->base_prefix . 'project_order') {
						$data = [
							'billid' => $row[2],
							'supplierName' => $row[3],
							'productid' => $row[4],
							'materialsName' => $row[5],
							'quantity' => $row[6],
							'GST' => $row[7],
							'totalPrice' => $row[8],
							'discount' => $row[9],
							'processed' => $row[10],
						];
						$result = $wpdb->insert($wpdb->base_prefix . 'project_order', $data);
						error_log('Insert project_order: ' . json_encode($result));
					}

					// Handling products
					if ($table_name === $wpdb->base_prefix . 'products') {
						$data = [
							'storeId' => $row[2],
							'materialsName' => $row[3],
						];
						$result = $wpdb->insert($wpdb->base_prefix . 'products', $data);
						error_log('Insert products: ' . json_encode($result));
					}

					// Handling product_attributes
					if ($table_name === $wpdb->base_prefix . 'product_attributes') {
						$data = [
							'attributeName' => $row[2],
						];
						$result = $wpdb->insert($wpdb->base_prefix . 'product_attributes', $data);
						error_log('Insert product_attributes: ' . json_encode($result));
					}

					// Handling product_variants
					if ($table_name === $wpdb->base_prefix . 'product_variants') {
						$data = [
							'product_id' => $row[2],
							'variantSKU' => $row[3],
							'variantStock' => $row[4],
							'variantPrice' => $row[5],
							'variantDiscount' => $row[6],
							'variantGST' => $row[7],
						];
						$result = $wpdb->insert($wpdb->base_prefix . 'product_variants', $data);
						error_log('Insert product_variants: ' . json_encode($result));
					}

					// Step 3: Re-enable foreign key checks
					$wpdb->query("SET FOREIGN_KEY_CHECKS = 1;");

					// Handling variant_attributes
					if ($table_name === $wpdb->base_prefix . 'variant_attributes') {
						$data = [
							'variant_id' => $row[1],
							'attribute_id' => $row[2],
							'attributeValue' => $row[3],
						];
						$result = $wpdb->insert($wpdb->base_prefix . 'variant_attributes', $data);
						error_log('Insert variant_attributes: ' . json_encode($result));
					}

					// Handling users
					if ($table_name === $wpdb->base_prefix . 'users') {
						$username = $row[2];
						$email = $row[3];
						$password = $row[4];
						$role = $row[5];

						if (!username_exists($username) && !email_exists($email)) {
							$new_user_id = wp_create_user($username, $password, $email);
							$user_obj = new WP_User($new_user_id);
							$user_obj->set_role($role);
						}
					}
				}

				fclose($handle);

				return wp_send_json(['success' => 'Tables populated with dummy data successfully.']);
			} else {
				return wp_send_json(['error' => 'Could not open CSV file: ' . $file_path]);
			}
		}




		/*******************
		public static function empty_and_populate_tables() {
			global $wpdb;

			self::reset_wer_pk_tables("");

			// Step 2: Populate tables from the CSV
			$file_path = plugin_dir_url( WP_WER_PK_PLUGIN_FILE ) . 'sample_data/' . $_REQUEST["fileName"];
			

			if (!file_exists($file_path)) {
				//return new WP_Error('file_not_found', 'The specified CSV file does not exist.');
			}


			if (($handle = fopen($file_path, 'r')) !== FALSE) {
				while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {


					$table_name = $row[0];

					// Handling projects
					if ($table_name === $wpdb->base_prefix . 'projects') {
						$data = [
							'site_name' => $row[1],
							'site_name' => $row[2],
							'site_size' => $row[3],
							'site_location' => $row[4],
							'start_date' => $row[5],
							'status' => $row[6],
						];
						$result = $wpdb->insert($wpdb->base_prefix . 'projects', $data);
						error_log('Insert result: ' . json_encode($result));
					}

					// Handling projects_details
					if ($table_name === $wpdb->base_prefix . 'projects_details') {
						$data = [
							'projectid' => $row[2],
							'billNo' => $row[3],
							'expenseType' => $row[4],
							'billdate' => $row[5],
							'description' => $row[6],
							'orderTotal' => $row[7],
							'confirmed' => $row[8],
						];
						
						$result = $wpdb->insert($wpdb->base_prefix . 'projects_details', $data);
						error_log('Insert result: ' . json_encode($result));
					}

					// Handling project_order
					if ($table_name === $wpdb->base_prefix . 'project_order') {
						$data = [
							'billid' => $row[2],
							'supplierName' => $row[3],
							'productid' => $row[4],
							'materialsName' => $row[5],
							'quantity' => $row[6],
							'GST' => $row[7],
							'totalPrice' => $row[8],
							'discount' => $row[9],
							'processed' => $row[10],
						];
						
						$result = $wpdb->insert($wpdb->base_prefix . 'project_order', $data);
						error_log('Insert result: ' . json_encode($result));
					}

					// Handling products
					if ($table_name === $wpdb->base_prefix . 'products') {
						$data = [
							'storeId' => $row[2],
							'materialsName' => $row[3],
						];

						$result = $wpdb->insert($wpdb->base_prefix . 'products', $data);
						error_log('Insert result: ' . json_encode($result));
					}

					// Handling product_attributes
					if ($table_name === $wpdb->base_prefix . 'product_attributes') {
						$data = [
							'attributeName' => $row[2],
						];
						
						$result = $wpdb->insert($wpdb->base_prefix . 'product_attributes', $data);
						error_log('Insert result: ' . json_encode($result));
					}
					
					

					// Handling product_variants
					if ($table_name === $wpdb->base_prefix . 'product_variants') {
						$data = [
							'product_id' => $row[2],
							'variantSKU' => $row[3],
							'variantStock' => $row[4],
							'variantPrice' => $row[5],
							'variantDiscount' => $row[6],
							'variantGST' => $row[7],
						];

						$result = $wpdb->insert($wpdb->base_prefix . 'product_variants', $data);
						error_log('Insert result: ' . json_encode($result));
					}

					// Step 3: Re-enable foreign key checks
					$wpdb->query("SET FOREIGN_KEY_CHECKS = 1;");
					// Handling variant_attributes
					if ($table_name === $wpdb->base_prefix . 'variant_attributes') {

						$data = [
							'variant_id' => $row[1],
							'attribute_id' => $row[2],
							'attributeValue' => $row[3],
						];
						
						$result = $wpdb->insert($wpdb->base_prefix . 'variant_attributes', $data);
						error_log('Insert result: ' . json_encode($result));
					}

					// Handling users
					if ($table_name === $wpdb->base_prefix . 'users') {
						$username = $row[2];
						$email = $row[3];
						$password = $row[4];
						$role = $row[5];

						if (!username_exists($username) && !email_exists($email)) {
							$new_user_id = wp_create_user($username, $password, $email);
							$user_obj = new WP_User($new_user_id);
							$user_obj->set_role($role);
						}
					}
				}
				fclose($handle);
				//return true;


				 //return new WP_Error('file_read_error', 'Could not read the CSV file.');
				new WP_Error('file_read_error', 'Could not read the CSV file.');

				if (is_wp_error($result)) {
					return wp_send_json( $result->get_error_message() );
				} else {
					return wp_send_json('Tables populated with dummy data successfully.');
				}


		}

		******************/


}

endif;