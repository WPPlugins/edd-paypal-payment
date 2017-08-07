jQuery(function ($) {
    /**
     * Paypal Pro Payment Gatways
     */
    jQuery('input[name="edd_settings[paypal_edd_pro_testmode]"]').change(function () {
        
        var sandbox = jQuery('input[name="edd_settings[paypal_edd_pro_sandbox_username]"], input[name="edd_settings[paypal_edd_pro_sandbox_password]"], input[name="edd_settings[paypal_edd_pro_sandbox_signature]"]').closest('tr'),
                live = jQuery('input[name="edd_settings[paypal_edd_pro_live_username]"], input[name="edd_settings[paypal_edd_pro_live_password]"], input[name="edd_settings[paypal_edd_pro_live_signature]"]').closest('tr');
        if (jQuery(this).is(':checked')) {
            sandbox.show();
            live.hide();
        } else {
            sandbox.hide();
            live.show();
        }
    }).change();      
});