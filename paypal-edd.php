<?php

/**
 * @link              http://designncoding.com/
 * @since             1.0.0
 * @package           PayPal_EDD
 *
 * @wordpress-plugin
 * Plugin Name:       Easy Digital Downloads PayPal Payment
 * Plugin URI:        paypal-edd
 * Description:       Easy Digital Downloads PayPal Payment
 * Version:           1.0.0
 * Author:            wpdesigncoding
 * Author URI:        http://designncoding.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       paypal-edd
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/***
 * Requier variable Initialize hear.
 */


if (!defined('PAYPAL_EDD_LOG_DIR')) {
    $upload_dir = wp_upload_dir();
    define('PAYPAL_EDD_LOG_DIR', $upload_dir['basedir'] . '/paypal-edd-logs/');
}

if (!defined('PAYPAL_EDD_BASE_NAME')) {
    define('PAYPAL_EDD_BASE_NAME', plugin_basename(__FILE__));
}


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-paypal-edd-activator.php
 */
function activate_paypal_edd() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-paypal-edd-activator.php';
	PayPal_EDD_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-paypal-edd-deactivator.php
 */
function deactivate_paypal_edd() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-paypal-edd-deactivator.php';
	PayPal_EDD_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_paypal_edd' );
register_deactivation_hook( __FILE__, 'deactivate_paypal_edd' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-paypal-edd.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_paypal_edd() {

	$plugin = new PayPal_EDD();
	$plugin->run();

}
run_paypal_edd();
