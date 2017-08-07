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

class PayPal_EDD_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
            self::create_log_files();
	}
        private static function create_log_files() {
        $upload_dir = wp_upload_dir();
        $files = array(
            array(
                'base' => PAYPAL_EDD_LOG_DIR,
                'file' => '.htaccess',
                'content' => 'deny from all'
            ),
            array(
                'base' => PAYPAL_EDD_LOG_DIR,
                'file' => 'index.html',
                'content' => ''
            )
        );
        foreach ($files as $file) {
            if (wp_mkdir_p($file['base']) && !file_exists(trailingslashit($file['base']) . $file['file'])) {
                if ($file_handle = @fopen(trailingslashit($file['base']) . $file['file'], 'w')) {
                    fwrite($file_handle, $file['content']);
                    fclose($file_handle);
                }
            }
        }
    }

}
