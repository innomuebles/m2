/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

define([
    'jquery',
    'Magento_Customer/js/customer-data',
    'Magento_Customer/js/section-config',
    'domReady!'
], function($, customerData, sectionConfig) {
    'use strict';

    /**
     * Push data to GTM datalayer
     */
    return function () {
        function dataLayerPush(data, sectionName) {
            if (!data.mf_datalayer) return;
            let eventFired, i, k;
            for (i = 0; i < data.mf_datalayer.length; i++) {
                window.dataLayer = window.dataLayer || [];
                eventFired = false;
                for (k = 0; k < window.dataLayer.length; k++) {
                    if (data.mf_datalayer[i].magefanUniqueEventId
                        && data.mf_datalayer[i].magefanUniqueEventId == window.dataLayer[k].magefanUniqueEventId
                    ) {
                        eventFired = true;
                        break;
                    }
                }
                if (!eventFired) {
                    /*
                    if (!data.mf_datalayer[i].customer_identifier
                        || (data.mf_datalayer[i].customer_identifier.indexOf('getMfGtmCustomerIdentifier') != -1)
                    ) {
                        data.mf_datalayer[i].customer_identifier = getMfGtmCustomerIdentifier();
                    }
                    */
                    var ept = data.mf_datalayer[i].ecomm_pagetype;
                    if ('other' === ept) {
                        ept = mfGtmGetEcommPageType();
                        data.mf_datalayer[i].ecomm_pagetype = ept;
                        if (data.mf_datalayer[i].google_tag_params) {
                            data.mf_datalayer[i].google_tag_params.ecomm_pagetype = ept;
                        }
                    };

                    window.dataLayer.push(data.mf_datalayer[i]);
                }
            }

            delete data.mf_datalayer;
            customerData.set(sectionName, data);
        }

        let sectionNames = [];
        if (sectionConfig.getSectionNames) {
            sectionNames = sectionConfig.getSectionNames();
        }
        if (!sectionNames || !sectionNames.length) {
            sectionNames = ['customer','cart','wishlist'];
        }

        let sectionName, section;
        for (let i = 0; i < sectionNames.length; i++) {
            sectionName = sectionNames[i];
            section = customerData.get(sectionName);

            (function(section, sectionName) {
                section.subscribe(function (data) {
                    dataLayerPush(data, sectionName);
                });
            })(section, sectionName);

            dataLayerPush(section(), sectionName);
        }
    }
});
