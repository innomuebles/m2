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
    const ADD_PAYMENT_INFO_EVENT = 'add_payment_info';

    /**
     * Push data to GTM datalayer
     */
    return function (selectPaymentMethodAction) {
        let lastTitle;

        return wrapper.wrap(selectPaymentMethodAction, function (originalSelectPaymentMethodAction, paymentMethod) {
            originalSelectPaymentMethodAction(paymentMethod);

            if (!window.dataLayer || paymentMethod === null || !paymentMethod.method) {
                return;
            }

            let data;

            for (let i = 0; i < dataLayer.length; i++) {
                if (dataLayer[i].event === BEGIN_CHECKOUT_EVENT) {
                    data = JSON.stringify(dataLayer[i]);
                    break;
                }
            }

            if (data && lastTitle !== paymentMethod.method) {
                lastTitle = paymentMethod.method;

                data = JSON.parse(data);
                data.event = ADD_PAYMENT_INFO_EVENT;
                data.ecommerce.payment_type = paymentMethod.method;

                window.dataLayer = window.dataLayer || [];
                window.dataLayer.push(data);
            }
        });
    }
});
