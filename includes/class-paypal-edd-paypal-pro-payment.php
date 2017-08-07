<?php

ob_start();

class Paypal_EDD_PayPal_Pro_Payment {

    public function __construct() {

        global $edd_options;

        $this->id = 'paypal_edd_paypal_pro';
        $this->edd_options = $edd_options;
        $this->api_version = '120';

        /**
         * EDD Defualt Page Link
         */
        $this->purchase_page = isset($this->edd_options['purchase_page']) ? $this->edd_options['purchase_page'] : '';
        $this->success_page = isset($this->edd_options['success_page']) ? $this->edd_options['success_page'] : '';
        $this->failure_page = isset($this->edd_options['failure_page']) ? $this->edd_options['failure_page'] : '';

        /**
         * EDD PayPal Pro Back-end Settings.
         */
        $this->testmode = isset($this->edd_options['paypal_edd_pro_testmode']) ? TRUE : FALSE;
        $this->debug = isset($this->edd_options['paypal_edd_pro_debug']) ? TRUE : FALSE;
        $this->invoice_prifix = isset($this->edd_options['paypal_edd_pro_prefix']) ? $this->edd_options['paypal_edd_pro_prefix'] : '';

        $this->parsed_response = '';
        $this->parsed_response_personal_details = '';
        $this->paypal_edd_notifyurl = site_url('?Paypal_Edd&action=ipn_handler');


        $this->URL = "https://api-3t.paypal.com/nvp";
        $this->username = isset($this->edd_options['paypal_edd_pro_live_username']) ? trim($this->edd_options['paypal_edd_pro_live_username']) : '';
        $this->password = isset($this->edd_options['paypal_edd_pro_live_password']) ? trim($this->edd_options['paypal_edd_pro_live_password']) : '';
        $this->signature = isset($this->edd_options['paypal_edd_pro_live_signature']) ? trim($this->edd_options['paypal_edd_pro_live_signature']) : '';

        if ($this->testmode) {
            $this->URL = "https://api-3t.sandbox.paypal.com/nvp";
            $this->username = isset($this->edd_options['paypal_edd_pro_sandbox_username']) ? trim($this->edd_options['paypal_edd_pro_sandbox_username']) : '';
            $this->password = isset($this->edd_options['paypal_edd_pro_sandbox_password']) ? trim($this->edd_options['paypal_edd_pro_sandbox_password']) : '';
            $this->signature = isset($this->edd_options['paypal_edd_pro_sandbox_signature']) ? trim($this->edd_options['paypal_edd_pro_sandbox_signature']) : '';
        }
    }

    public function Paypal_EDD_PayPal_Pro_Process_Payment($posted) {
        try {
            edd_clear_errors();

            if (is_array($posted) && count($posted) > 0) {

                $edd_card_info = $this->Paypal_EDD_PayPal_Pro_Card_Info($posted['card_info']);
                if ($edd_card_info) {
                    $edd_posted_data = $this->Paypal_EDD_PayPal_Pro_Get_Post_Array($posted);
                    $edd_responce = $this->Paypal_EDD_PayPal_Pro_Request($edd_posted_data);
                    if (is_wp_error($edd_responce)) {
                        edd_set_error('paypal_edd_paypal_pro_request_responce', __($edd_responce->get_error_message(), 'paypal-edd'));
                        $this->Paypal_EDD_PayPal_Pro_Write_Log('paypal_edd_paypal_pro', 'ERRORS', $edd_responce->get_error_message());
                        wp_redirect(get_permalink($this->purchase_page));
                        exit;
                    }
                    if (isset($edd_responce['body']) && !empty($edd_responce['body'])) {
                        parse_str($edd_responce['body'], $this->parsed_response);
                    }
                    if (isset($this->parsed_response['ACK']) && ($this->parsed_response['ACK'] == 'Success' || $this->parsed_response['ACK'] == 'successwithwarning' )) {
                        $this->Paypal_EDD_PayPal_Pro_Write_Log('paypal_edd_paypal_pro', 'REQUEST', $this->parsed_response);
                        $this->Paypal_EDD_PayPal_Pro_Insert_Payment($posted);
                        exit;
                    } else {
                        $this->Paypal_EDD_PayPal_Pro_Write_Log('paypal_edd_paypal_pro', 'request', $this->parsed_response);
                        edd_set_error('paypal_edd_paypal_pro_request_responce', __($this->parsed_response['L_LONGMESSAGE0'], 'paypal-edd'));
                        wp_redirect(get_permalink($this->purchase_page));

                        exit;
                    }
                } else {
                    edd_set_error('paypal_edd_paypal_pro_card_empty', __('EDD PayPal Pro Please fill the card details.', 'paypal-edd'));
                    wp_redirect(get_permalink($this->purchase_page));
                    exit;
                }
            }
        } catch (Exception $ex) {
            
        }
    }

    public function Paypal_EDD_PayPal_Pro_Card_Info($card_info) {
        try {

            $result = TRUE;
            $card_key = array('card_name' => 'card_name', 'card_number' => 'card_number', 'card_cvc' => 'card_cvc', 'card_exp_month' => 'card_exp_month', 'card_exp_year' => 'card_exp_year');
            foreach ($card_info as $key => $value) {
                if (array_key_exists($key, $card_key)) {
                    if (isset($value) && empty($value)) {
                        $result = FALSE;
                        break;
                    }
                }
            }
            return $result;
        } catch (Exception $ex) {
            
        }
    }

    public function Paypal_EDD_PayPal_Pro_Get_Post_Array($posted) {
        try {

            $edd_posted_data = $posted['post_data'];
            $edd_card_data = $this->Paypal_EDD_PayPal_Pro_Card_Details($posted['card_info']);

            $result = array(
                'VERSION' => '121',
                'SIGNATURE' => $this->signature,
                'USER' => $this->username,
                'PWD' => $this->password,
                'METHOD' => 'DoDirectPayment',
                'PAYMENTACTION' => 'sale',
                'IPADDRESS' => $this->Paypal_EDD_PayPal_Pro_User_IP(),
                'AMT' => number_format(( $posted['price']), 2, '.', ''),
                'INVNUM' => $this->invoice_prifix . '' . substr(microtime(), -5),
                'CURRENCYCODE' => edd_get_currency(),
                'ACCT' => $edd_card_data['card_number'],
                'EXPDATE' => sprintf('%02d', $edd_card_data['card_exp_month']) . '' . $edd_card_data['card_exp_year'],
                'STARTDATE' => '', //$is_card_info['card_exp_year'],
                'CVV2' => $edd_card_data['card_cvc'],
                'EMAIL' => $edd_posted_data['edd_email'],
                'FIRSTNAME' => $edd_posted_data['edd_first'],
                'DESC' => '',
                'NOTIFYURL' => $this->paypal_edd_notifyurl,
                'BUTTONSOURCE' => ''
            );


            return $result;
        } catch (Exception $ex) {
            
        }
    }

    public function Paypal_EDD_PayPal_Pro_Card_Details($card_info) {
        try {

            $result = array();

            foreach ($card_info as $key => $value) {
                $result[$key] = $value;
            }

            return $result;
        } catch (Exception $ex) {
            
        }
    }
    
    public function Paypal_EDD_PayPal_Pro_Request($edd_posted_data) {
        try {
            return wp_remote_post($this->URL, array('method' => 'POST', 'headers' => array('PAYPAL-NVP' => 'Y'), 'body' => $edd_posted_data, 'timeout' => 70, 'user-agent' => 'credit card', 'httpversion' => '1.1'));
        } catch (Exception $ex) {
            
        }
    }    

    public function Paypal_EDD_PayPal_Pro_Cart_Details($cart_details) {
        try {

            $result = array();

            $item_qty = 0;
            $item_total = 0;

            foreach ($cart_details as $key => $value) {
                $item_total = $item_total + ( $value['item_price'] * $value['quantity'] );
            }
            $cart_details['TOTAL_AMT'] = $item_total;

            return $cart_details;
        } catch (Exception $ex) {
            
        }
    }

    public function Paypal_EDD_PayPal_Pro_Post_Details($post_data) {
        try {

            $result = array();

            foreach ($post_data as $key => $value) {
                $result[$key] = $value;
            }

            return $result;
        } catch (Exception $ex) {
            
        }
    }

    public function Paypal_EDD_PayPal_Pro_Transaction_Details() {

        try {
            $result = array(
                'VERSION' => $this->api_version,
                'SIGNATURE' => $this->signature,
                'USER' => $this->username,
                'PWD' => $this->password,
                'METHOD' => 'GetTransactionDetails',
                'TRANSACTIONID' => $this->parsed_response['TRANSACTIONID']
            );
            $edd_responce = wp_remote_post($this->URL, array('method' => 'POST', 'headers' => array('PAYPAL-NVP' => 'Y'), 'body' => $result, 'timeout' => 70, 'user-agent' => 'credit card', 'httpversion' => '1.1'));

            if (is_wp_error($edd_responce)) {
                edd_set_error('paypal_edd_paypal_pro_request_responce', __($edd_responce->get_error_message(), 'paypal-edd'));
                wp_redirect(get_permalink($this->purchase_page));
                exit;
            }

            if (isset($edd_responce['body']) && !empty($edd_responce['body'])) {
                parse_str($edd_responce['body'], $this->parsed_response_personal_details);
            }

            if (isset($this->parsed_response_personal_details['ACK']) && (strtolower($this->parsed_response_personal_details['ACK']) == 'success' || strtolower($this->parsed_response_personal_details['ACK']) == 'successwithwarning')) {
                $this->Paypal_EDD_PayPal_Pro_Write_Log('paypal_edd_paypal_pro', 'TRANSACTION', $this->parsed_response_personal_details);
                return $this->parsed_response_personal_details;
            } else {
                $this->Paypal_EDD_PayPal_Pro_Write_Log('paypal_edd_paypal_pro', 'TRANSACTIONID', $this->parsed_response_personal_details);
                return false;
            }
        } catch (Exception $Ex) {
            
        }
    }

    public function Paypal_EDD_PayPal_Pro_Insert_Payment($purchase_data) {

        try {

            $is_transaction_info = $this->Paypal_EDD_PayPal_Pro_Transaction_Details();

            $payment_data = array(
                'price' => $purchase_data['price'],
                'date' => $purchase_data['date'],
                'user_email' => $purchase_data['user_email'],
                'purchase_key' => $purchase_data['purchase_key'],
                'currency' => edd_get_currency(),
                'downloads' => $purchase_data['downloads'],
                'user_info' => $purchase_data['user_info'],
                'cart_details' => $purchase_data['cart_details'],
                'status' => 'pending'
            );
            $payment = edd_insert_payment($payment_data);

            if ($payment) {
                edd_update_payment_status($payment, $is_transaction_info['PAYMENTSTATUS'] == 'Completed' ? 'publish' : 'pending');
                wp_update_post(array('ID' => $payment, 'post_title' => $this->parsed_response['TRANSACTIONID']));
                edd_empty_cart();
                edd_send_to_success_page();
            } else {
                edd_record_gateway_error(__('Payment Error', 'easy-digital-downloads'), sprintf(__('Payment creation failed while processing a manual (free or test) purchase. Payment data: %s', 'easy-digital-downloads'), json_encode($payment_data)), $payment);
                edd_send_back_to_checkout('?payment-mode=' . $purchase_data['post_data']['edd-gateway']);
            }
        } catch (Exception $Ex) {
            
        }
    }

    public function Paypal_EDD_PayPal_Pro_User_IP() {
        return !empty($_SERVER['HTTP_X_FORWARD_FOR']) ? $_SERVER['HTTP_X_FORWARD_FOR'] : $_SERVER['REMOTE_ADDR'];
    }

    public function Paypal_EDD_PayPal_Pro_Write_Log($handle, $response_name, $result_array) {

        if ($this->debug == false) {
            return;
        }
        $log = new PayPal_EDD_Logger();
        $log->add($handle, $response_name . '=>' . print_r($result_array, true));
        return;
    }

}

ob_flush();
