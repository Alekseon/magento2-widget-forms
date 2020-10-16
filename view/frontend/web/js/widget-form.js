/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
define([
    'uiComponent',
    'jquery',
    'Magento_Ui/js/modal/alert'
], function (Component, $, alert) {
    'use strict';

    return Component.extend({

        defaults: {
            template: 'Alekseon_WidgetForms/widget-form',
        },

        submitForm: function (form) {
            if (!this.validateForm(form)) {
                return;
            }

            var self = this;

            $.ajax({
                url: this.formSubmitUrl,
                type: 'POST',
                data: $(form).serializeArray(),
                dataType: 'json',
                showLoader: true
            }).done(function (response) {
                alert({
                    title: $.mage.__('Success'),
                    content: response.message
                });
                form.reset();
            }).fail(function (error) {
                alert({
                    title: $.mage.__('Error'),
                    content: error.responseJSON.message
                });
            }).complete(function() {
                self.onComplete();
            });
        },

        onComplete: function() {
        },

        validateForm: function (form) {
            return $(form).validation() && $(form).validation('isValid');
        }
    });
});
