<?php

/**
 * Fired during plugin activation
 *
 * @link       http://designncoding.com/
 * @since      1.0.0
 *
 * @package    Paypal_Edd
 * @subpackage Paypal_Edd/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Paypal_Edd
 * @subpackage Paypal_Edd/includes
 * @author     wpdesigncoding <wpdesigncoding@gmail.com>
 */
class PayPal_EDD_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'paypal-edd',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
