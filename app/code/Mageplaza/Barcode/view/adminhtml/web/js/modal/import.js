/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Barcode
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
define([
    'jquery',
    "Magento_Ui/js/modal/alert",
    'mage/translate',
    'Mageplaza_Barcode/js/barcode/helper'
], function ($, alert, $t, barcodeHelper) {
    'use strict';

    $.widget('mpBarcode.importBarcode', {
        options: {
            gridUrl: '',
            printUrl: '',
            checkBtn: $('#mp_barcode_check_btn'),
            importForm: $('#mp_barcode_import_modal_form'),
            csv: $('#mp_barcode_import'),
            printBtn: $('#mass_print_barcode_csv'),
            csvResult: $('#mp-barcode-modal-csv-result'),
            importMessage: $('#mp-barcode-import-message'),
            printDiv: $('#mp-barcode-print-btn-div')
        },

        _create: function () {
            var self     = this,
                formData = new FormData();

            this.options.checkBtn.on('click', function () {
                if (self.options.importForm.valid()) {
                    formData.append('csv', self.options.csv.prop('files')[0]);
                    self._validateCsvFile(formData);
                }
            });

            this.options.printBtn.on('click', function () {
                var barcodeData = $('input[name="products"]').val();

                self._massPrint(barcodeData);
            });
        },

        _validateCsvFile: function (formData) {
            var self = this;

            $.ajax({
                url: self.options.importForm.attr('action'),
                type: 'POST',
                dataType: 'json',
                showLoader: true,
                processData: false,
                contentType: false,
                data: formData,
                success: function (response) {
                    if (response.error) {
                        barcodeHelper.alertError($t('Error'), response.message);
                    }
                    if (response.validated) {
                        self.options.importMessage.find('#message-content').text(response.message);
                        self.options.importMessage.show();
                        self._renderGrid(response.gridData);
                        setTimeout(function () {
                            self.options.importMessage.hide();
                        }, 10000);
                    }
                },
                error: function (error) {
                    barcodeHelper.alertError($t('Request Error'), error.status + ' ' + error.statusText);
                }
            });
        },

        _renderGrid: function (gridData) {
            var self = this;

            $.ajax({
                url: self.options.gridUrl,
                showLoader: true,
                data: {
                    products: gridData,
                    firstLoad: 1
                },
                success: function (response) {
                    self.options.csvResult.html(response);
                    self.options.csvResult.trigger('contentUpdated');
                    self.options.printDiv.show();
                },
                error: function (error) {
                    barcodeHelper.alertError($t('Request Error'), 'Can\'t check with edited cvs file, please choose another csv file or reload the page');
                }
            });
        },

        _massPrint: function (barcodeData) {
            var self = this;

            $.ajax({
                type: 'POST',
                url: self.options.printUrl,
                showLoader: true,
                dataType: 'json',
                data: {
                    products: barcodeData
                },
                success: function (response) {
                    if (response.error) {
                        barcodeHelper.alertError('Error', response.message);
                    } else {
                        barcodeHelper.displayPdf(response.data);
                        barcodeHelper.downloadPdf(response.data, 'ImportBarcodeLabel');
                    }
                },
                error: function (error) {
                    barcodeHelper.alertError('Request Error', error.status + ' ' + error.statusText);
                }
            });
        }

    });

    return $.mpBarcode.importBarcode;
});
