define([
    'jquery',
    'mage/utils/wrapper',
    'mage/apply/main'
], function ($, wrapper, mage) {
    'use strict';

    return function(targetModule){

        var displayRegularPriceBlock = targetModule.prototype._displayRegularPriceBlock,
            fillSelect = targetModule.prototype._fillSelect;

        targetModule.prototype._displayRegularPriceBlock = wrapper.wrap(displayRegularPriceBlock, function(original, optionId) {
            var shouldBeShown = true,
                $elm = $(this.options.slyOldPriceSelector).parents('.price-box').find('.normal-price'),
                $product = this.element.parents(this.options.selectorProduct),
                $productPrice = $product.find(this.options.selectorProductPrice),
                product_list_info = this.element.parents('.product-item-info'),
                product_view_info = this.element.parents('.catalog-product-view'),
                discount_elm = product_list_info.find('.discount-percent'),
                discount_view_elm = product_view_info.find('.product.media .discount-percent');

            _.each(this.options.settings, function (element) {
                if (element.value === '') {
                    shouldBeShown = false;
                }
            });

            if (shouldBeShown &&
                this.options.spConfig.optionPrices[optionId].oldPrice.amount !==
                this.options.spConfig.optionPrices[optionId].finalPrice.amount
            ) {
                if ($elm.length) {
                    $elm.addClass('special-price');
                }

                $productPrice.find(this.options.slyOldPriceSelector).show();

                var oldPrice = this.options.spConfig.optionPrices[optionId].oldPrice.amount,
                    finalPrice = this.options.spConfig.optionPrices[optionId].finalPrice.amount,
                    discount_percent = (finalPrice - oldPrice) * 100 / oldPrice,
                    discount_text = discount_percent.toFixed(0) + '%';

                if (product_list_info.length) {
                    if (discount_elm.length) {
                        discount_elm.show();
                        discount_elm.text(discount_text);
                    } else {
                        product_list_info.find('.product-item-photo').append('<span class="discount-percent">'+discount_text+'</span>');
                    }
                }

                if (product_view_info.length) {
                    if (discount_view_elm.length) {
                        discount_view_elm.show();
                        discount_view_elm.text(discount_text);
                    } else {
                        product_view_info.find('.product.media').append('<span class="discount-percent">'+discount_text+'</span>');
                    }
                }
            } else {
                if ($elm.length) {
                    $elm.removeClass('special-price');
                }

                $productPrice.find(this.options.slyOldPriceSelector).hide();

                if (discount_elm.length) {
                    discount_elm.hide();
                }

                if (discount_view_elm.length) {
                    discount_view_elm.hide();
                }
            }

            $(document).trigger('updateMsrpPriceBlock',
                [
                    optionId,
                    this.options.spConfig.optionPrices
                ]
            );
        });

        return targetModule;
    };
});
