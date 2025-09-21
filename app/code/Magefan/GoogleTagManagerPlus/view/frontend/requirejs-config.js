/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

var config = {
    map: {
        '*': {
            mfGtmCustomerDataLayer: 'Magefan_GoogleTagManagerPlus/js/customer-data-layer'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/action/select-shipping-method': {
                'Magefan_GoogleTagManagerPlus/js/action/select-shipping-method-mixin': true
            },
            'Magento_Checkout/js/action/select-payment-method': {
                'Magefan_GoogleTagManagerPlus/js/action/select-payment-method-mixin': true
            }
        }
    }
};
