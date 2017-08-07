<?php

if (!defined('ABSPATH')) exit;

function PayPal_EDD_Registration($gateways) {
    $gateways['paypal_edd_paypal_pro'] = array(
        'admin_label' => __('EDD PayPal Pro', 'paypal-edd'),
        'checkout_label' => __('EDD PayPal Pro', 'paypal-edd')
    );
    return $gateways;
}

function PayPal_EDD_Initialize_PayPal_Pro($sections) {
    $sections['edd_paypal_pro'] = __('EDD PayPal Pro', 'paypal-edd');
    return $sections;
}

function PayPal_EDD_Initialize_Settings_PayPal_Pro($settings) {   
    
    $paypal_edd_pro = array(
        'edd_paypal_pro' => array(
            array(
                'id' => 'paypal_edd_pro_credentials',
                'name' => '<strong>' . __('EDD PayPal Pro', 'paypal-edd') . '</strong>',
                'type' => 'header',
            ),
            array(
                'id' => 'paypal_edd_pro_testmode',
                'name' => __('Enable Testmode ', 'paypal-edd'),
                'type' => 'checkbox',
                'desc' => __('Enable Paypal Pro Test Mode', 'paypal-edd')
            ),
            array(
                'id' => 'paypal_edd_pro_sandbox_username',
                'name' => __('Sandbox API Username ', 'paypal-edd'),
                'desc' => sprintf(__('Create sandbox accounts and obtain API credentials from within your <a href="%s" target="_blank">PayPal developer account</a>.', 'paypal-edd'), 'https://developer.paypal.com/'),
                'type' => 'text'
            ),
            array(
                'id' => 'paypal_edd_pro_sandbox_password',
                'name' => __('Sandbox API Password ', 'paypal-edd'),
                'type' => 'password'
            ),
            array(
                'id' => 'paypal_edd_pro_sandbox_signature',
                'name' => __('Sandbox API Signature ', 'paypal-edd'),
                'type' => 'password'
            ),
            array(
                'id' => 'paypal_edd_pro_live_username',
                'name' => __('Live API Username ', 'paypal-edd'),
                'desc' => __('Get your live account API credentials from your PayPal account profile under the API Access section or by using <a href="https://www.paypal.com/us/cgi-bin/webscr?cmd=_login-api-run" target="_blank">here.</a>', 'paypal-edd'),
                'type' => 'text'
            ),
            array(
                'id' => 'paypal_edd_pro_live_password',
                'name' => __('Live API Password ', 'paypal-edd'),
                'type' => 'password'
            ),
            array(
                'id' => 'paypal_edd_pro_live_signature',
                'name' => __('Live API Signature ', 'paypal-edd'),
                'type' => 'password'
            ),
            array(
                'id' => 'paypal_edd_pro_prefix',
                'name' => __('Invoice ID Prefix', 'paypal-edd'),
                'desc' => __('Add a prefix to the invoice ID sent to PayPal. This can resolve duplicate invoice problems when working with multiple websites on the same PayPal account.', 'paypal-edd'),
                'type' => 'text'
            ),
            array(
                'id' => 'paypal_edd_pro_debug',
                'name' => __('Debug Log', 'paypal-edd'),
                'desc' => sprintf(__('Enable logging <code>%s</code>', 'paypal-edd'), trailingslashit(PAYPAL_EDD_LOG_DIR) . 'paypal_edd_paypal_pro' . '_' . sanitize_file_name(wp_hash('paypal_edd_paypal_pro')) . '.log'),
                'type' => 'checkbox'
            )
        ),
    );
    return array_merge($settings, $paypal_edd_pro);
}

function PayPal_EDD_PayPal_Pro_process_payment($purchase_data) {
    $Paypal_EDD_PayPal_Pro_Payment = new Paypal_EDD_PayPal_Pro_Payment();
    $Paypal_EDD_PayPal_Pro_Payment->Paypal_EDD_PayPal_Pro_Process_Payment($purchase_data);
}

