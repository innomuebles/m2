/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

define([
    'mage/utils/wrapper'
], function (wrapper) {
    'use strict';

    /**
     * ------------------------------------------------------------------------
     * Constants
     * ------------------------------------------------------------------------
     */
    const BEGIN_CHECKOUT_EVENT = 'begin_checkout';
    const ADD_SHIPPING_INFO_EVENT = 'add_shipping_info';

    /**
     * Push data to GTM datalayer
     */
    return function (selectShippingMethodAction) {
        let lastCarrierTitle;

        return wrapper.wrap(selectShippingMethodAction, function (originalSelectPaymentMethodAction, shippingMethod) {
            originalSelectPaymentMethodAction(shippingMethod);

            if (!window.dataLayer || shippingMethod === null || !shippingMethod.carrier_title) {
                return;
            }

            let data;

            for (let i = 0; i < dataLayer.length; i++) {
                if (dataLayer[i].event === BEGIN_CHECKOUT_EVENT) {
                    data = JSON.stringify(dataLayer[i]);
                    break;
                }
            }

            if (data && lastCarrierTitle !== shippingMethod.carrier_title) {
                lastCarrierTitle = shippingMethod.carrier_title;

                data = JSON.parse(data);
                data.event = ADD_SHIPPING_INFO_EVENT;
                data.ecommerce.shipping_tier = shippingMethod.carrier_title;

                window.dataLayer = window.dataLayer || [];
                window.dataLayer.push(data);
            }
        });
    };
});

