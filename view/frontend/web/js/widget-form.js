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
            let form = {};

            form.currentTab = 1;
            form.tabs = config.tabs;
            form.form = $('#' + config.formId)[0];
            form.formSubmitUrl = config.formSubmitUrl;
            form.successMessage = config.successMessage;

            let self = this;

            $(form.form).submit(function (event) {
                self.submitFormAction(form);
                event.preventDefault(event);
            });
        },

        openTab: function (form, tabIndex) {
            var tab = $(form.form).find('.form-tab-' + form.currentTab);
            tab.slideUp();

            setTimeout(() => {
                form.currentTab = tabIndex;

                $(form.form).find('.form-tab-' + form.currentTab).slideDown();
            }, "100");
        },

        submitFormAction: function (form) {
            if ($(form.form).validation && !$(form.form).validation('isValid')) {
                 return false;
            }

            if (form.tabs[form.currentTab + 1] !== undefined) {
                this.openTab(form, form.currentTab + 1);
                return;
            }

            let self = this;

            $.ajax({
                url: form.formSubmitUrl,
                    type: 'POST',
                    data: $(form.form).serializeArray(),
                    dataType: 'json',
                    showLoader: true
                }).done(function (response) {
                    if (response.errors) {
                        self.onError(form, response);
                    } else {
                        self.onSuccess(form);
                    }
            }).fail(function (error) {
                self.onError(form, error.responseJSON);
            }).complete(function() {
                self.onComplete(form);
            });
        },

        onComplete: function(form) {
        },

        onError: function(form, response) {
            alert({
                title: $.mage.__('Error'),
                content: response.message
            });
        },

        onSuccess: function(form) {
            alert({
                title: $.mage.__('Success'),
                content: form.successMessage
            });
            form.form.reset();
            if (this.currentTab !== 1) {
                this.openTab(form, 1);
            }
        }
    };

    return function (config) {
        $(document).ready(function () {
            form.init(config);
        });
    };
});
