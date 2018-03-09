<?php

/**
 * The file that defines the core plugin class
 *
 * @link       https://telefication.ir/wordpress-plugin
 * @since      1.0.0
 *
 * @package    Telefication
 * @subpackage Telefication/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Telefication
 * @subpackage Telefication/includes
 * @author     Foad Tahmasebi <tahmasebi.f@gmail.com>
 */
class Telefication {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Telefication_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Options of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array $options Options of the plugin from database.
	 */
	protected $options;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		if ( defined( 'TELEFICATION_VERSION' ) ) {
			$this->version = TELEFICATION_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'telefication';
		$this->options     = get_option( 'telefication' );

		$this->load_dependencies();
		$this->set_locale();
		$this->core_functionality();

		if ( is_admin() ) {
			$this->define_admin_hooks();
		}

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Telefication_Loader. Orchestrates the hooks of the plugin.
	 * - Telefication_i18n. Defines internationalization functionality.
	 * - Telefication_Admin. Defines all hooks for the admin area.
	 *
	 * Create an instance of the loader which will be used to register the hooks with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-telefication-loader.php';

		/**
		 * The class responsible for defining internationalization functionality of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-telefication-i18n.php';

		/**
		 * The class responsible for communicate with Telefication server.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-telefication-service.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		if ( is_admin() ) {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-telefication-admin.php';
		}

		$this->loader = new Telefication_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Telefication_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Telefication_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Add hooks according to the settings.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function core_functionality() {

		if ( isset( $this->options['is_woocommerce_only'] ) && '1' == $this->options['is_woocommerce_only'] ) {
			// if woocommerce is active
			$this->loader->add_action( 'woocommerce_thankyou', $this, 'telefication_action_woocommerce_thankyou' );

		} else {
			$this->loader->add_filter( 'wp_mail', $this, 'telefication_filter_wp_mail' );

		}
	}

	/**
	 * Add filter callback function for 'wp_email'
	 *
	 * Get email subject, generate notification body and send notification
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @param array $email_args
	 *
	 * @return array $email_args
	 */
	public function telefication_filter_wp_mail( $email_args ) {

		//check for if notification is not allowed for this recipient.
		if ( isset( $this->options['match_emails'] ) && ! empty( $this->options['match_emails'] ) ) {
			$emails = explode( ',', $this->options['match_emails'] );

			if ( ! in_array( $email_args['to'], $emails ) ) {
				return $email_args;
			}
		}

		// check for adding recipient email
		$to = ( isset( $this->options['display_recipient_email'] ) && '1' == $this->options['display_recipient_email'] ) ? $email_args['to'] : '';

		$message = get_bloginfo( 'name' ) . ": " . $to . "\n\n";
		$message .= $email_args['subject'] . "\n\n";

		// check for adding email body option
		if ( isset( $this->options['send_email_body'] ) && '1' == $this->options['send_email_body'] ) {
			$message .= strip_tags( $email_args['message'], '<b><i><a><code><pre>' ) . "\n\n";
		}

		$message .= site_url();

		$telefication_service = new Telefication_Service( $this->options );

		if ( $telefication_service->create_url( $message ) ) {
			$telefication_service->send_notification();
		}

		return $email_args;
	}

	/**
	 * Add action callback function for 'woocommerce_thankyou'
	 *
	 * Get order data, generate notification body and send notification for woocommerce new order
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @param array $order_id
	 *
	 */
	public function telefication_action_woocommerce_thankyou( $order_id ) {

		// Get an instance of the WC_Order object
		$order      = wc_get_order( $order_id );
		$order_data = $order->get_data();

		$order_details = ""; //order details

		$order_details .= __( 'Name: ', 'telefication' ) . $order_data['billing']['first_name'] . " " . $order_data['billing']['last_name'] . "\n";
		$order_details .= __( 'City: ', 'telefication' ) . $order_data['billing']['city'] . "\n";
		$order_details .= __( 'Phone: ', 'telefication' ) . $order_data['billing']['phone'] . "\n";
		$order_details .= __( 'Total: ', 'telefication' ) . $order_data['total'];


		$items = ""; // items_detail

		foreach ( $order->get_items() as $item_key => $item_values ) {

			$item_data = $item_values->get_data();

			$product_name = $item_data['name'];
			$quantity     = $item_data['quantity'];

			$items .= "$product_name * $quantity \n";
		}


		//notification body
		$message = get_bloginfo( 'name' ) . ":\n\n";
		$message .= __( 'New order: ', 'telefication' ) . "\n-----\n\n";
		$message .= $items . "\n\n";

		$message .= __( 'Billing data: ', 'telefication' ) . "\n-----\n\n";
		$message .= $order_details . "\n\n";

		$message .= site_url();

		$telefication_service = new Telefication_Service( $this->options );

		if ( $telefication_service->create_url( $message ) ) {
			$telefication_service->send_notification();
		}
	}

	/**
	 * Register all of the hooks related to the admin area functionality of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Telefication_Admin( $this->plugin_name, $this->version, $this->options );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_telefication_page' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'init_telefication_page' );

		// Since 1.3.0
		$this->loader->add_action( 'wp_ajax_send_test_message', $this, 'send_test_message' );

		// add link of Telefication setting page in plugins page
		$this->loader->add_filter( 'plugin_action_links_' . TELEFICATION_BASENAME, $plugin_admin, 'add_action_links' );
	}

	/**
	 * Ajax sent test message
	 *
	 * @since    1.3.0
	 */
	public function send_test_message() {

		$message = ( isset( $_REQUEST['message'] ) ) ? $_REQUEST['message'] : __( 'This Is Test', 'telefication' );

		if ( ! isset( $_REQUEST['chat_id'] ) || empty( $_REQUEST['chat_id'] ) ) {
			_e( 'Please enter ID', 'telefication' );
			die;
		}

		$telefication_service          = new Telefication_Service( $this->options );
		$telefication_service->chat_id = $_REQUEST['chat_id'];

		if ( $telefication_service->create_url( $message ) ) {
			echo $telefication_service->send_notification();
		}
		die;
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {

		$this->loader->run();
	}

}
