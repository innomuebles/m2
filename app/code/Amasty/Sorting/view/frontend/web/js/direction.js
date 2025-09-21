define([
    'jquery'
], function ($) {
    $.widget('mage.amSortingDirection', {
        options: {
            permanentDirectionMethods: []
        },
        selectors: {
            directionSwitcher: '[data-role="direction-switcher"]'
        },

        _create: function () {
            this.toggleDirectionSwitcher(this.element.val());
            this.observeSortOrder();
        },

        observeSortOrder: function () {
            var self = this;

            self.element.on('change', function () {
                self.toggleDirectionSwitcher(self.element.val());
            });
        },

        toggleDirectionSwitcher: function (sortMethod) {
            var directionSwitcher = $(this.selectors.directionSwitcher);

            if (this.options.permanentDirectionMethods.indexOf(sortMethod) !== -1) {
                directionSwitcher.fadeTo(0, 0);
                directionSwitcher.css('pointer-events', 'none');
            } else {
                directionSwitcher.fadeTo(0, 1);
                directionSwitcher.css('pointer-events', '');
            }
        }
    });

    return $.mage.amSortingDirection;
});
