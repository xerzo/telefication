<?php

/**
 * Communicate with Telefication service.
 *
 * @link       https://telefication.ir/wordpress-plugin
 * @since      1.0.0
 *
 * @package    Telefication
 * @subpackage Telefication/includes
 */

/**
 * Send Notification Functionality.
 *
 * Send a message through Telefication service to Telegram Bot.
 *
 * @since      1.0.0
 * @package    Telefication
 * @subpackage Telefication/includes
 * @author     Foad Tahmasebi <tahmasebi.f@gmail.com>
 */
class Telefication_Service {

	/**
	 * The chat_id of user in Telefication bot.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $chat_id The chat_id of user in Telefication bot.
	 */
	public $chat_id;

	/**
	 * Telefication or Telegram API url for sending notification.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $url Telefication api url.
	 */
	protected $url;

	/**
	 * URL parameters for sending to api.
	 *
	 * @since    1.3.0
	 * @access   protected
	 * @var      array $data parameters.
	 */
	protected $data;

	/**
	 * User own bot token
	 *
	 * @since    1.3.0
	 * @access   protected
	 * @var bool
	 */
	protected $telegram_bot_token;

	/**
	 * Initialize options.
	 *
	 * @since    1.0.0
	 *
	 * @param array $options Telefication options from WP database
	 */
	public function __construct( $options ) {

		$this->chat_id = $options['chat_id'];

		// Since 1.3.0
		if ( ! empty( $this->chat_id ) && isset( $options['bot_token'] ) && ! empty( $options['bot_token'] ) ) {
			$this->telegram_bot_token = $options['bot_token'];
		} else {
			$this->telegram_bot_token = false;
		}
	}

	/**
	 * Create Telefication API url to send notification .
	 *
	 * @since    1.0.0
	 *
	 * @param string $message Notification Message
	 *
	 * @return bool
	 */
	public function create_url( $message = '' ) {

		if ( ! empty( $this->chat_id ) ) {
			// send to user bot if bot-token is exist
			if ( $this->telegram_bot_token ) {

				$this->url  = 'https://api.telegram.org/bot' . $this->telegram_bot_token . '/sendMessage';
				$this->data = array(
					'parse_mode' => 'html',
					'chat_id'    => $this->chat_id,
					'text'       => $message
				);

				return true;

			} else {

				$this->url  = "https://telefication.ir/api/sendNotification";
				$this->data = array(
					'chat_id' => $this->chat_id,
					'message' => $message
				);

				return true;

			}
		}

		return false;
	}

	/**
	 * Send notification.
	 *
	 * Call Telefication API url by curl.
	 *
	 * @since    1.0.0
	 *
	 * @return bool|mixed
	 */
	function send_notification() {

		if ( ! empty( $this->url ) ) {
			$ch           = curl_init();
			$option_array = array(
				CURLOPT_URL            => $this->url . '?' . http_build_query( $this->data ),
				CURLOPT_RETURNTRANSFER => true,
			);
			curl_setopt_array( $ch, $option_array );
			$result = curl_exec( $ch );
			curl_close( $ch );

			//if telegram bot token exist, we should parse response in jason mode
			if ( $this->telegram_bot_token ) {

				$result = json_decode( $result, true );
				if ( 'true' == $result['ok'] ) {
					return __( 'Test message sent successfully (To Your Bot)', 'telefication' );
				} else {

					if ( isset( $result['description'] ) ) {
						return $result['description'];
					}

					return false;
				}

			} else {

				if ( 'ok' === $result ) {
					return __( 'Test message sent successfully (To Telefication Bot)', 'telefication' );
				} else {
					return $result;
				}

			}
		}


		return false;
	}

}