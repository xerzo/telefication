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
	 * @param      string $version The version of this plugin.
	 * @param array $options Telefication options from WP database
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
				'error_occurred'     => __( 'An error occurred', 'telefication' ),
				'test_message'       => __( 'This is a test message from Telefication', 'telefication' ),
				'ajax_url'           => admin_url( 'admin-ajax.php' ),
				'bot_token_is_empty' => __( 'Your bot token is not set!', 'telefication' )
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
			__( 'Telefication Chat ID', 'telefication' ),
			array( $this, 'chat_id_callback' ),
			'telefication-setting',
			'general_setting_section'
		);

		add_settings_field(
			'notify_for',
			__( 'Notify Me For:', 'telefication' ),
			array( $this, 'notify_for_callback' ),
			'telefication-setting',
			'general_setting_section'
		);


		// TELEFICATION OWN BOT SETTINGS
		add_settings_section(
			'own_bot_setting_section',
			__( 'My Own Bot Setting', 'telefication' ),
			array( $this, 'own_bot_setting_section_callback' ),
			'telefication-own-bot-setting'
		);

		add_settings_field(
			'bot_token', // ID
			__( 'Your Bot Token', 'telefication' ),
			array( $this, 'bot_token_callback' ),
			'telefication-own-bot-setting',
			'own_bot_setting_section'
		);

		// TELEFICATION CHANNEL SETTINGS
		add_settings_section(
			'channel_setting_section',
			__( 'Channel Setting', 'telefication' ),
			array( $this, 'channel_setting_section_callback' ),
			'telefication-channel-setting'
		);

		add_settings_field(
			'channel_username', // ID
			__( 'Your Channel Username', 'telefication' ),
			array( $this, 'telefication_chanel_username_callback' ),
			'telefication-channel-setting',
			'channel_setting_section'
		);


		add_settings_field(
			'channel_notification_template', // ID
			__( 'Notification Template', 'telefication' ),
			array( $this, 'telefication_channel_notification_template_callback' ),
			'telefication-channel-setting',
			'channel_setting_section'
		);

		add_settings_field(
			'channel_featured_image_enable', // ID
			__( 'Featured Image', 'telefication' ),
			array( $this, 'telefication_channel_featured_image_enable_callback' ),
			'telefication-channel-setting',
			'channel_setting_section'
		);

		add_settings_field(
			'channel_post_type', // ID
			__( 'Post Types', 'telefication' ),
			array( $this, 'telefication_channel_post_type_callback' ),
			'telefication-channel-setting',
			'channel_setting_section'
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

		if ( isset( $input['bot_token'] ) ) {
			$input['bot_token'] = sanitize_text_field( $input['bot_token'] );
		}

		return $input;
	}

	/**
	 * General setting Section callback to print information.
	 *
	 * @since 1.0.0
	 */
	public function general_setting_section_callback() {

		printf( '<p>' . __( 'You can use <b>your own Telegram Bot</b> or join to %s at Telegram to receive notifications.', 'telefication' ) . '</p>',
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
			'<p class="description">' . __( 'Please enter your Telegram chat id. You can get it from @teleficationbot', 'telefication' ) . '</p>',

			isset( $this->options['chat_id'] ) ? esc_attr( $this->options['chat_id'] ) : ''
		);

		$description = __( 'If you use your own bot, you cat get your chat id by pressing this button. ', 'telefication' );
		$disable     = 'disable';

		if ( isset( $this->options['bot_token'] ) && ! empty( $this->options['bot_token'] ) ) {

			$disable     = '';
			$description = __( 'Start your bot or send a message to it, then press this button to get your ID', 'telefication' );
		}

		echo "<div class='$disable'>";
		echo '<br><a href="#" id="get_chat_id" class="button">' . __( 'Get Your Chat ID From Your Own Bot', 'telefication' ) . '</a>';
		echo '<p class="description">' . $description . '</p>';
		echo '</div>';
	}


	/**
	 * Generate send email body option checkbox field
	 *
	 * @since 1.2.0
	 */
	public function notify_for_callback() {


		if ( isset( $this->options['email_notification'] ) ) {
			$email_notification_checked = checked( 1, $this->options['email_notification'], false );
		}
		if ( isset( $this->options['send_email_body'] ) ) {
			$send_email_body_checked = checked( 1, $this->options['send_email_body'], false );
		}
		if ( isset( $this->options['display_recipient_email'] ) ) {
			$display_recipient_email_checked = checked( 1, $this->options['display_recipient_email'], false );
		}
		if ( isset( $this->options['is_woocommerce_only'] ) ) {
			$is_woocommerce_only_checked = checked( 1, $this->options['is_woocommerce_only'], false );
		}
		if ( isset( $this->options['new_comment_notification'] ) ) {
			$new_comment_notification_checked = checked( 1, $this->options['new_comment_notification'], false );
		}
		if ( isset( $this->options['new_post_notification'] ) ) {
			$new_post_notification_checked = checked( 1, $this->options['new_post_notification'], false );
		}
		if ( isset( $this->options['new_user_notification'] ) ) {
			$new_user_notification_checked = checked( 1, $this->options['new_user_notification'], false );
		}
		/*
		 * Notify for emails
		 */
		printf(
			'<div class="field-set"><input class="has-sub" type="checkbox" id="email_notification" name="telefication[email_notification]" value="1" %s/>' .
			'<label for="email_notification"><b>' . __( 'E-mails:', 'telefication' ) . '</b> ' . __( 'notify me for WP emails', 'telefication' ) . '</label><br> ',
			isset( $email_notification_checked ) ? $email_notification_checked : ''
		);

		//Send email body?
		printf(
			'<div style="display:%s;" class="setting-fields-group"><input type="checkbox" id="send_email_body" name="telefication[send_email_body]" value="1" %s/>' .
			'<label for="send_email_body"><b>' . __( 'Send Email Body:', 'telefication' ) . '</b> ' . __( 'If enabled, you will receive email body too.', 'telefication' ) . '</label><br>',
			isset( $email_notification_checked ) ? 'block' : 'none',
			isset( $send_email_body_checked ) ? $send_email_body_checked : ''
		);

		//add recipient email to notification?
		printf(
			'<input type="checkbox" id="display_recipient_email" name="telefication[display_recipient_email]" value="1" %s/>' .
			'<label for="display_recipient_email"><b>' . __( 'Display Recipient Email:', 'telefication' ) . '</b> ' . __( 'If enabled, the recipient email will be added to notifications.', 'telefication' ) . '</label><br>',
			isset( $display_recipient_email_checked ) ? $display_recipient_email_checked : ''
		);

		//Filter recipients
		printf(
			'<br><b>' . __( 'Email(s):', 'telefication' ) . '</b><br><input type="text" id="match_emails" name="telefication[match_emails]" value="%s" /> ' .
			'<p class="description">' . __( 'Notify me only of the emails that are sent to this list. (Comma separated.) <br> Leave it empty if you want to get all notifications.', 'telefication' ) . '</p></div></div>',

			isset( $this->options['match_emails'] ) ? esc_attr( $this->options['match_emails'] ) : ''
		);
		// END Of Field Set


		// is woocommerce active
		if ( ! defined( 'WC_VERSION' ) ) {
			$woocommerce_is_active = '<p>' . __( '⚠ Woocommerce is not active!', 'telefication' ) . '</p>';
		}
		//Notify for new orders
		printf(
			'<input class="%s" type="checkbox" id="is_woocommerce_only" name="telefication[is_woocommerce_only]" value="1" %s/>' .
			'<label class="%s" for="is_woocommerce_only">' . __( '<b>New Order:</b> Enable this to get notified for new woocommerce orders. (on woocommerce thank you page)', 'telefication' ) . '</label><br>',
			isset( $woocommerce_is_active ) ? 'disable' : '',
			isset( $is_woocommerce_only_checked ) ? $is_woocommerce_only_checked : '',
			isset( $woocommerce_is_active ) ? 'disable' : ''
		);

		// Notify for new comments
		printf(
			'<input type="checkbox" id="new_comment_notification" name="telefication[new_comment_notification]" value="1" %s/>' .
			'<label for="new_comment_notification"><b>' . __( 'New Comment:', 'telefication' ) . '</b> ' . __( 'Enable this to get notified for new comments', 'telefication' ) . '</label><br> ',
			isset( $new_comment_notification_checked ) ? $new_comment_notification_checked : ''
		);

		// Notify for new posts
		printf(
			'<input type="checkbox" id="new_post_notification" name="telefication[new_post_notification]" value="1" %s/>' .
			'<label for="new_post_notification"><b>' . __( 'New Post:', 'telefication' ) . '</b> ' . __( 'Enable this to get notified for new post', 'telefication' ) . '</label><br> ',
			isset( $new_post_notification_checked ) ? $new_post_notification_checked : ''
		);

		// Notify for new posts
		printf(
			'<input type="checkbox" id="new_user_notification" name="telefication[new_user_notification]" value="1" %s/>' .
			'<label for="new_user_notification"><b>' . __( 'New User:', 'telefication' ) . '</b> ' . __( 'Enable this to get notified for new user registration', 'telefication' ) . '</label><br> ',
			isset( $new_user_notification_checked ) ? $new_user_notification_checked : ''
		);

	}


	// Own Bot Setting Page

	/**
	 * Own bot setting Section callback to print information.
	 *
	 * @since 1.3.0
	 */
	public function own_bot_setting_section_callback() {

		echo "<p>" . __( 'If you insert your own bot token, Telefication will send notifications to your bot directly!', 'telefication' ) . "<br>";
	}

	/**
	 * Generate bot_token field display
	 *
	 * @since 1.3.0
	 */
	public function bot_token_callback() {

		printf(
			'<input type="text" id="bot_token" name="telefication[bot_token]" value="%s" /> ' .
			'<p class="description">' . __( 'Please enter your bot token .', 'telefication' ) . '</p>',

			isset( $this->options['bot_token'] ) ? esc_attr( $this->options['bot_token'] ) : ''
		);

	}

	// Channel Setting Page


	public function channel_setting_section_callback() {

		echo "<p>" . __( 'Sending new posts notification to your channel!', 'telefication' ) . "<br>";
	}

	public function telefication_chanel_username_callback() {

		printf(
			'<input type="text" id="chanel_username" class="half" name="telefication[chanel_username]" value="%s" /> ' .
			'<p class="description">' . __( 'Please enter your channel username .', 'telefication' ) . '</p>',

			isset( $this->options['chanel_username'] ) ? esc_attr( $this->options['chanel_username'] ) : ''
		);

	}


	public function telefication_channel_notification_template_callback() {

		printf(
			'<textarea id="channel_notification_template" name="telefication[channel_notification_template]" >%s</textarea> ' .
			'<p class="description">' . __( 'Please write your channel notification template .', 'telefication' ) . '<br>' .
			__( 'You can use this variables:', 'telefication' ) . ' {title}, {content}, {expert}, {post_link}, {post_primary_category}' . '</p>',

			isset( $this->options['channel_notification_template'] ) ? esc_attr( $this->options['channel_notification_template'] ) : ''
		);


	}


	public function telefication_channel_featured_image_enable_callback() {


		if ( isset( $this->options['channel_featured_image_enable'] ) ) {
			$channel_featured_image_enable_checked = checked( 1, $this->options['channel_featured_image_enable'], false );
		}
		/*
		 * Notify for emails
		 */
		printf(
			'<input class="has-sub" type="checkbox" id="channel_featured_image_enable" name="telefication[channel_featured_image_enable]" value="1" %s/>' .
			'<label for="channel_featured_image_enable">' . __( 'If enabled notifications sent as image post (if you enable this, your notification template length should not be more than 200 characters)', 'telefication' ) . '</label> ',
			isset( $channel_featured_image_enable_checked ) ? $channel_featured_image_enable_checked : ''
		);


	}


	public function telefication_channel_post_type_callback() {

		$telefication_channel_post_type = [];

		if ( isset( $this->options['telefication_channel_post_type'] ) ) {
			$telefication_channel_post_type = $this->options['telefication_channel_post_type'];
		}


		foreach ( get_post_types( '', 'names' ) as $post_type ) {
			$checked = '';
			if ( array_key_exists( $post_type, $telefication_channel_post_type ) && $telefication_channel_post_type[ $post_type ] === "1" ) {
				$checked = 'checked';
			}

			printf( '<div class="one-third">
			<input class="has-sub" type="checkbox" name="telefication[telefication_channel_post_type][%s]" value="1" %s>%s</div>',
				$post_type, $checked, $post_type

			);
		}

	}

}
