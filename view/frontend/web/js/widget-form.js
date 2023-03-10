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
            currentTab: 0
        },

        openTab: function (form, tabIndex) {
            var tab = $(form).find('.form-tab-' + this.currentTab);
            tab.slideUp();

            setTimeout(() => {
                this.currentTab = tabIndex;
                $(form).find('.form-tab-' + this.currentTab).slideDown();
            }, "100");
        },

        submitForm: function (form) {
            if (!this.validateForm(form)) {
                return;
            }

            if (this.tabs[this.currentTab + 1] !== undefined) {
               this.openTab(form, this.currentTab + 1);
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
                    title: response.title,
                    content: response.message
                });
                form.reset();
                if (self.currentTab != 0) {
                    self.openTab(form, 0);
                }
                self.onSuccess();
            }).fail(function (error) {
                var message = $.mage.__('Unexpected error.');
                if (error.status) {
                    message += ' ' + $.mage.__('Error Code') + ': ' + error.status;
                }
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

        onSuccess: function() {
        },

        onElementRender: function (element) {
            $(element).trigger('contentUpdated');
        },

        validateForm: function (form) {
            return $(form).validation() && $(form).validation('isValid');
        }
    });
});
