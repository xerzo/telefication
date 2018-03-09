<?php

/**
 * The plugin bootstrap file
 *
 * @link              https://telefication.ir/wordpress-plugin
 * @since             1.0.0
 * @package           Telefication
 *
 * @wordpress-plugin
 * Plugin Name:       Telefication
 * Plugin URI:        https://telefication.ir/wordpress-plugin
 * Description:       Send notifications to users via @teleficationbot (a telegram robot). you can start it here <a href="https://t.me/teleficationbot">Telefication Bot</a>
 * Version:           1.3.0
 * Author:            Foad Tahmasebi
 * Author URI:        http://daskhat.ir/
 * Text Domain:       telefication
 * Domain Path:       /languages
 * License:           GPLv2 or later
 *
 * Telefication is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Telefication is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Telefication. If not, see Wordpress root.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'TELEFICATION_VERSION', '1.3.0' );

/**
 * Plugin basename.
 */
define( 'TELEFICATION_BASENAME', plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 */
function activate_telefication() {

}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_telefication() {

}

register_activation_hook( __FILE__, 'activate_telefication' );
register_deactivation_hook( __FILE__, 'deactivate_telefication' );

/**
 * The core plugin class that is used to define internationalization, admin-specific hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-telefication.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_telefication() {

	$plugin = new Telefication();
	$plugin->run();

}

run_telefication();

// Since 1.3.0
// Ajax test message
if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'telefication_test_message' ) {
	do_action( 'wp_ajax_send_test_message' );
}
