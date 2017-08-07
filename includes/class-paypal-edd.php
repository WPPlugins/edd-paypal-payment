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
class PayPal_EDD {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      PayPal_EDD_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $PayPal_EDD    The string used to uniquely identify this plugin.
     */
    protected $PayPal_EDD;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {

        $this->PayPal_EDD = 'paypal-edd';
        $this->version = '1.0.0';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        
        $prefix = is_network_admin() ? 'network_admin_' : '';        
        add_filter("{$prefix}plugin_action_links_" . PAYPAL_EDD_BASE_NAME, array($this, 'PayPal_EDD_Plugin_Action_Link'), 10, 4);
        
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - PayPal_EDD_Loader. Orchestrates the hooks of the plugin.
     * - PayPal_EDD_i18n. Defines internationalization functionality.
     * - PayPal_EDD_Admin. Defines all hooks for the admin area.
     * - PayPal_EDD_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-paypal-edd-loader.php';
        /**
         * The class responsible for activity log
         * 
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-paypal-edd-logger.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-paypal-edd-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-paypal-edd-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-paypal-edd-public.php';

        /**
         * The class responsible for PayPal Pro Payment Gatways
         * 
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-paypal-edd-paypal-pro.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-paypal-edd-paypal-pro-payment.php';

        $this->loader = new PayPal_EDD_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the PayPal_EDD_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new PayPal_EDD_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new PayPal_EDD_Admin($this->get_PayPal_EDD(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        
        /**
         * PayPal-EDD Payment Gatway PayPal Pro Back-End
         */
        
        add_filter('edd_payment_gateways', 'PayPal_EDD_Registration');
        add_filter('edd_settings_sections_gateways', 'PayPal_EDD_Initialize_PayPal_Pro');
        add_filter('edd_settings_gateways', 'PayPal_EDD_Initialize_Settings_PayPal_Pro');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {

        $plugin_public = new PayPal_EDD_Public($this->get_PayPal_EDD(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

        /**
         * PayPal EDD Payment Gatway PayPal Pro Front-End
         */
        
        add_action('edd_gateway_paypal_edd_paypal_pro', 'PayPal_EDD_PayPal_Pro_process_payment');        
//        add_action('edd_gateway_paypal_edd_paypal_pro', 'paypal_for_edd_pro_process_payment');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_PayPal_EDD() {
        return $this->PayPal_EDD;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    PayPal_EDD_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }
    
    public function PayPal_EDD_Plugin_Action_Link($actions, $plugin_file, $plugin_data, $context) {
        $custom_actions = array(
            'configure' => sprintf('<a href="%s">%s</a>', admin_url('edit.php?post_type=download&page=edd-settings&tab=gateways'), __('Configure', 'donation-button'))         
        );

        return array_merge($custom_actions, $actions);
    }

}
