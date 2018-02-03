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
	protected $chat_id;

	/**
	 * Telefication API url for sending notification.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $url Telefication api url.
	 */
	protected $url;

	/**
	 * Initialize options.
	 *
	 * @since    1.0.0
	 *
	 * @param array $options Telefication options from WP database
	 */
	public function __construct( $options ) {

		$this->chat_id = $options['chat_id'];
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
			$this->url = "https://telefication.ir/api/sendNotification?chat_id=" . $this->chat_id . "&message=" . urlencode( $message );

			return true;
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
				CURLOPT_URL            => $this->url,
				CURLOPT_RETURNTRANSFER => true,
			);
			curl_setopt_array( $ch, $option_array );
			$result = curl_exec( $ch );
			curl_close( $ch );
			if ( 'ok' === $result ) {
				return true;
			} else {
				return $result;
			}
		}

		return false;
	}

}