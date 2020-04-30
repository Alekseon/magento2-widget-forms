/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
define([
    'jquery',
    'Magento_Ui/js/modal/alert'
], function ($, alert) {
    'use strict';

    var form = {

        init: function (config) {
            var form = {};
            form.form = $('#' + config.formId)[0];
            form.formSubmitUrl = config.formSubmitUrl;
            form.successMessage = config.successMessage;
            this.addSubmitButtonEvent(form);
        },

        addSubmitButtonEvent: function (form) {
            var self = this;
            var submitButton = $(form.form).find(':submit');
            submitButton.click(function () {
                self.submitFormAction(form);
                return false;
            });
        },

        submitFormAction: function (form) {
            if ($(form.form).validation && !$(form.form).validation('isValid')) {
                return false;
            }

            $.ajax({
                url: form.formSubmitUrl,
                type: 'POST',
                data: $(form.form).serializeArray(),
                dataType: 'json',
                showLoader: true
            }).done(function (response) {
                alert({
                    title: $.mage.__('Success'),
                    content: form.successMessage
                });
                form.form.reset();
            }).fail(function (error) {
                alert({
                    title: $.mage.__('Error'),
                    content: error.responseJSON.message
                });
            });
        }
    };

    return function (config) {
        $(document).ready(function () {
            form.init(config);
        });
    };
});
