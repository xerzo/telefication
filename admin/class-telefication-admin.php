<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://telefication.ir/wordpress-plugin
 * @since      1.0.0
 *
 * @package    Telefication
 * @subpackage Telefication/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Generate setting pageDefines the plugin name, version, and enqueue the admin-specific stylesheet.
 *
 * @package    Telefication
 * @subpackage Telefication/admin
 * @author     Foad Tahmasebi <tahmasebi.f@gmail.com>
 */
class Telefication_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Options of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array $options Options of the plugin from database.
	 */
	private $options;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of this plugin.
	 * @param      string $version     The version of this plugin.
	 * @param array       $options     Telefication options from WP database
	 */
	public function __construct( $plugin_name, $version, $options ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->options     = $options;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 *
	 * @param string $hook Hook name
	 */
	public function enqueue_styles( $hook ) {

		if ( $hook === 'settings_page_telefication-setting' ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/telefication-admin.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the scripts for the admin area.
	 *
	 * @since    1.1.0
	 *
	 * @param string $hook Hook name
	 */
	public function enqueue_scripts( $hook ) {

		if ( $hook === 'settings_page_telefication-setting' ) {

			// Register the script
			wp_register_script( 'telefication-admin-js', plugin_dir_url( __FILE__ ) . 'js/telefication-admin.js', array( 'jquery' ), $this->version, true );

			// Localize the script with new data
			$translation_array = array(
				'error_occurred' => __( 'An error occurred', 'telefication' ),
				'test_message'   => __( 'This is a test message from Telefication', 'telefication' )
			);
			wp_localize_script( 'telefication-admin-js', 'telefication', $translation_array );

			// Enqueued script with localized data.
			wp_enqueue_script( 'telefication-admin-js' );
		}
	}

	/**
	 * Add link of Telefication setting page in plugins page.
	 *
	 * @since 1.0.0
	 */
	public function add_action_links( $links ) {

		$telefication_links = '<a href="' . admin_url( 'options-general.php?page=telefication-setting' ) . '">' .
		                      __( 'Settings', 'telefication' ) . '</a>';
		array_push( $links, $telefication_links );

		return $links;
	}

	/**
	 * Add Telefication setting page in admin area.
	 *
	 * @since 1.0.0
	 */
	public function add_telefication_page() {

		add_options_page(
			__( 'Telefication Settings', 'telefication' ),
			__( 'Telefication', 'telefication' ),
			'manage_options',
			'telefication-setting',
			array( $this, 'create_telefication_page' )
		);

	}

	/**
	 * Create Telefication setting page display.
	 *
	 * @since 1.0.0
	 */
	public function create_telefication_page() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/telefication-admin-display.php';

	}

	/**
	 * Add setting sections and fields to Telefication setting page.
	 *
	 * @since 1.0.0
	 */
	public function init_telefication_page() {

		register_setting(
			'telefication_option_group',
			'telefication',
			array( $this, 'sanitize_input' )
		);

		add_settings_section(
			'general_setting_section',
			__( 'General Setting', 'telefication' ),
			array( $this, 'general_setting_section_callback' ),
			'telefication-setting'
		);

		add_settings_field(
			'chat_id', // ID
			__( 'Telefication Bot ID', 'telefication' ),
			array( $this, 'chat_id_callback' ),
			'telefication-setting',
			'general_setting_section'
		);

		add_settings_field(
			'match_emails',
			__( 'Email(s):', 'telefication' ),
			array( $this, 'match_emails_callback' ),
			'telefication-setting',
			'general_setting_section'
		);

		add_settings_field(
			'is_woocommerce_only',
			__( 'Only Woocommerce Orders', 'telefication' ),
			array( $this, 'woocommerce_only_callback' ),
			'telefication-setting',
			'general_setting_section'
		);

	}

	/**
	 * Sanitize user inputs.
	 *
	 * @since 1.0.0
	 *
	 * @param array $input Inputs from setting form.
	 *
	 * @return array
	 *
	 */
	public function sanitize_input( $input ) {

		if ( isset( $input['chat_id'] ) ) {
			$input['chat_id'] = sanitize_text_field( $input['chat_id'] );
		}

		if ( isset( $input['match_emails'] ) ) {

			$new_emails = [];
			$emails     = explode( ',', $input['match_emails'] );
			foreach ( $emails as $email ) {
				$email = trim( $email );
				if ( filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
					$new_emails[] = $email;
				}
			}
			$input['match_emails'] = implode( ',', $new_emails );
		}

		return $input;
	}

	/**
	 * General setting Section callback to print information.
	 *
	 * @since 1.0.0
	 */
	public function general_setting_section_callback() {

		printf( '<p>' . __( 'Join to %s at Telegram to receive notifications.', 'telefication' ) . '</p>',
			'<a href="https://t.me/teleficationbot" target="_blank">@teleficationbot</a>' );

	}

	/**
	 * Generate chat_id field display
	 *
	 * @since 1.0.0
	 */
	public function chat_id_callback() {

		printf(
			'<input type="text" id="chat_id" name="telefication[chat_id]" value="%s" /> ' .
			'<a href="#" id="test_message" class="button">' . __( 'Send test message', 'telefication' ) . '</a>' .
			'<p class="description">' . __( 'Please enter your Telefication bot id.', 'telefication' ) . '</p>',

			isset( $this->options['chat_id'] ) ? esc_attr( $this->options['chat_id'] ) : ''
		);

	}

	/**
	 * Generate woocommerce checkbox field display
	 *
	 * @since 1.0.0
	 */
	public function woocommerce_only_callback() {

		if ( isset( $this->options['is_woocommerce_only'] ) ) {
			$checked = checked( 1, $this->options['is_woocommerce_only'], false );
		}

		if ( ! defined( 'WC_VERSION' ) ) {
			$woocommerce_is_active = '<p>' . __( '⚠ Woocommerce is not active!', 'telefication' ) . '</p>';
		}

		printf(
			'<input type="checkbox" id="is_woocommerce_only" name="telefication[is_woocommerce_only]" value="1" %s/>' .
			'<label for="is_woocommerce_only">' . __( 'If enabled, you receive only woocommerce new orders notification. (on woocommerce thank you page)', 'telefication' ) . '</label>' .
			'%s',
			isset( $checked ) ? $checked : '',
			isset( $woocommerce_is_active ) ? $woocommerce_is_active : ''
		);

	}

	/**
	 * Generate emails field display
	 *
	 * @since 1.1.0
	 */
	public function match_emails_callback() {

		printf(
			'<input type="text" id="match_emails" name="telefication[match_emails]" value="%s" /> ' .
			'<p class="description">' . __( 'Notify me only of the emails that are sent to this list. (Comma separated.) <br> Leave it empty if you want to get all notifications.', 'telefication' ) . '</p>',

			isset( $this->options['match_emails'] ) ? esc_attr( $this->options['match_emails'] ) : ''
		);

	}

}
