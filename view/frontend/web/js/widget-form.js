/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
define([
    'jquery',
    'Magento_Ui/js/modal/alert'
], function ($, alert) {
    'use strict';

    $.widget('mage.alekseonWidgetForm', {

        options: {
            currentTab: 1,
            tabs: [],
            form: null,
            formSubmitUrl: 'formSubmitUrl',
            formId: ''
        },

        _create: function () {
            let self = this;
            this.options.form = $('#' + this.options.formId)[0];
            $(this.options.form).submit(function (event) {
                self.submitFormAction();
                event.preventDefault(event);
            });
        },

        openTab: function (form, tabIndex) {
            $(this.options.form).find('#form-tab-fieldset-' + this.options.currentTab).slideUp();
            $(this.options.form).find('#form-tab-actions-' + this.options.currentTab).hide();

            setTimeout(() => {
                this.options.currentTab = tabIndex;

                $(this.options.form).find('#form-tab-fieldset-' + this.options.currentTab).slideDown();
                $(this.options.form).find('#form-tab-actions-' + this.options.currentTab).show();
            }, "100");
        },

        submitFormAction: function () {
            if ($(this.options.form).validation && !$(this.options.form).validation('isValid')) {
                return false;
            }

            if (this.options.tabs[this.options.currentTab + 1] !== undefined) {
                this.openTab(this.options, this.options.currentTab + 1);
                return;
            }

            let self = this;
            const formData = new FormData(this.options.form);

            $.ajax({
                url: this.options.formSubmitUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                showLoader: true
            }).done(function (response) {
                if (response.errors) {
                    self.onError(response);
                } else {
                    self.onSuccess(response);
                }
            }).fail(function (error) {
                self.onError(error.responseJSON);
            }).always(function() {
                self.onComplete();
            });
        },

        onComplete: function() {
        },

        onError: function(response) {
            alert({
                title: $.mage.__('Error'),
                content: response.message
            });
        },

        onSuccess: function(response) {
            if(response.html_content) {
                this.options.form.parentElement.innerHTML = response.html_content;
            } else {
                alert({
                    title: response.title,
                    content: response.message
                });
            }
            this.options.form.reset();
            if (this.options.currentTab !== 1) {
                this.openTab(this.options.form, 1);
            }
        }
    });

    return $.mage.alekseonWidgetForm;
});
