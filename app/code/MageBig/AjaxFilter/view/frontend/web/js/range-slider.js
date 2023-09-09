/*jshint browser:true jquery:true*/
/*global alert*/

define([
    'jquery',
    'Magento_Catalog/js/price-utils',
    'jquery/ui-modules/widgets/slider'
], function ($, priceUtil) {
    "use strict";

    !function (o) {
        if (o.support.touch = "ontouchend" in document, o.support.touch) {
            var t, e = o.ui.mouse.prototype, u = e._mouseInit, n = e._mouseDestroy;

            function c(o, t)
            {
                if (!(o.originalEvent.touches.length > 1)) {
                    var e = o.originalEvent.changedTouches[0], u = document.createEvent("MouseEvents");
                    u.initMouseEvent(t, !0, !0, window, 1, e.screenX, e.screenY, e.clientX, e.clientY, !1, !1, !1, !1, 0, null), o.target.dispatchEvent(u)
                }
            }

            e._touchStart = function (o) {
                !t && this._mouseCapture(o.originalEvent.changedTouches[0]) && (t = !0, this._touchMoved = !1, c(o, "mouseover"), c(o, "mousemove"), c(o, "mousedown"))
            }, e._touchMove = function (o) {
                t && (this._touchMoved = !0, c(o, "mousemove"))
            }, e._touchEnd = function (o) {
                t && (c(o, "mouseup"), c(o, "mouseout"), this._touchMoved || c(o, "click"), t = !1)
            }, e._mouseInit = function () {
                var t = this;
                t.element.bind({
                    touchstart: o.proxy(t, "_touchStart"),
                    touchmove: o.proxy(t, "_touchMove"),
                    touchend: o.proxy(t, "_touchEnd")
                }), u.call(t)
            }, e._mouseDestroy = function () {
                var t = this;
                t.element.unbind({
                    touchstart: o.proxy(t, "_touchStart"),
                    touchmove: o.proxy(t, "_touchMove"),
                    touchend: o.proxy(t, "_touchEnd")
                }), n.call(t)
            }
        }
    }($);

    $.widget('magebig.rangeSlider', {

        options: {
            fromLabel: '[data-role=from-label]',
            toLabel: '[data-role=to-label]',
            sliderBar: '[data-role=slider-bar]',
            applyButton: '[data-role=apply-range]',
            rate: 1.0000,
            maxLabelOffset: 0.01
        },

        _create: function () {
            this._initSliderValues();
            this._createSlider();
            this._refreshDisplay();
        },

        _initSliderValues: function () {
            this.rate = parseFloat(this.options.rate);
            this.from = Math.floor(this.options.currentValue.from * this.rate);
            this.to = Math.round(this.options.currentValue.to * this.rate);
            this.minValue = Math.floor(this.options.minValue * this.rate);
            this.maxValue = Math.round(this.options.maxValue * this.rate);
        },

        _createSlider: function () {
            this.element.find(this.options.sliderBar).slider({
                range: true,
                min: this.minValue,
                max: this.maxValue,
                values: [this.from, this.to],
                slide: this._onSliderChange.bind(this),
                step: this.options.step
            });
        },

        _onSliderChange: function (ev, ui) {
            this.from = ui.values[0];
            this.to = ui.values[1];
            this._refreshDisplay();
        },

        _refreshDisplay: function () {
            if (this.element.find('[data-role=from-label]')) {
                this.element.find('[data-role=from-label]').html(this._formatLabel(this.from));
            }

            if (this.element.find('[data-role=to-label]')) {
                this.element.find('[data-role=to-label]').html(this._formatLabel(this.to - this.options.maxLabelOffset));
            }

            this._applyRange();
        },

        _applyRange: function () {
            var from = this.from * (1 / this.rate),
                to = this.to * (1 / this.rate),
                url = this.options.actionUrl,
                code = this.options.code;

            url += (url.search(/\?/) != -1) ? '&' : '?';
            url += code + '=' + from + '-' + to;
            this.element.find(this.options.applyButton).attr('href', url);
        },

        _formatLabel: function (value) {
            var formattedValue = value;

            if (this.options.fieldFormat) {
                formattedValue = priceUtil.formatPrice(value, this.options.fieldFormat);
            }

            return formattedValue;
        }
    });

    return $.magebig.rangeSlider;
});
