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

            const formData = new FormData(form);
            formData.append("form_key", $.mage.cookies.get("form_key"))

            $.ajax({
                url: this.formSubmitUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                showLoader: true
            }).done(function (response) {
                alert({
                    title: $.mage.__('Success'),
                    content: response.message
                });
                form.reset();
            }).fail(function (error) {
                var message = $.mage.__('Unexpected error');
                if (error.responseJSON) {
                    message = error.responseJSON.message;
                }

                alert({
                    title: $.mage.__('Error'),
                    content: message
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
