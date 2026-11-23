/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 *
 * "Frontend View" fieldset (Widget Settings tab): drag & drop field order + field width.
 *
 * - Order is written into the existing "Sort Order" inputs of the "Form Fields" tab
 *   (#form_field_{id}_sort_order), so this table never posts sort_order by itself.
 * - Width select posts form_fields[{id}][frontend_width].
 * - config.data comes from FrontendView::getFieldsJson().
 */
define([
    'jquery',
    'jquery-ui-modules/sortable'
], function ($) {
    'use strict';

    return function (config, element) {
        var table = $(element),
            step = parseInt(config.sortOrderStep, 10) || 10,
            data = config.data || {},
            useGroups = !!data.multipleSteps && (data.tabs || []).length > 1,
            groups = {};

        /**
         * Input from the "Form Fields" tab for given field id and field code.
         */
        function fieldsTabInput(fieldId, code) {
            return $('#form_field_' + fieldId + '_' + code);
        }

        function tabLabel(code) {
            return $('input[name="form_tabs[' + code + '][label]"]').val() || code;
        }

        function createGroup(code, label) {
            var tbody = $('<tbody class="frontend-view-group"/>').attr('data-group-code', code);

            if (useGroups) {
                tbody.append(
                    $('<tr class="frontend-view-group-header"/>').append(
                        $('<th colspan="4"/>').text(label)
                    )
                );
            }
            table.append(tbody);
            groups[code] = tbody;
            makeSortable(tbody);

            return tbody;
        }

        function getGroup(code) {
            if (!useGroups) {
                return groups[''];
            }

            // tab may be created in "Form Fields" but not saved yet
            return groups[code] || createGroup(code, tabLabel(code));
        }

        function createWidthSelect(field) {
            var select = $('<select class="admin__control-select"/>')
                .attr('name', 'form_fields[' + field.id + '][frontend_width]');

            $.each(data.widthOptions || [], function () {
                select.append(
                    $('<option/>').val(this.value).text(this.label).prop('selected', this.value === field.width)
                );
            });

            return select;
        }

        function createRow(field) {
            return $('<tr class="frontend-view-row"/>')
                .attr('data-field-id', field.id)
                .attr('data-group-code', field.group_code)
                .append(
                    $('<td class="col-draggable"><span class="draggable-handle"/></td>'),
                    $('<td class="col-label"/>').text(field.label),
                    $('<td class="col-type"/>').text(field.type || '—'),
                    $('<td class="col-width"/>').append(createWidthSelect(field))
                );
        }

        /**
         * Assign sequential sort orders to all rows (in table order) and push them to "Form Fields".
         */
        function renumber() {
            table.find('tr.frontend-view-row:visible').each(function (index) {
                fieldsTabInput($(this).data('field-id'), 'sort_order').val((index + 1) * step).trigger('change');
            });
        }

        function makeSortable(tbody) {
            tbody.sortable({
                items: '> tr.frontend-view-row',
                axis: 'y',
                distance: 8,
                tolerance: 'pointer',
                cancel: 'input, select, button',
                placeholder: 'frontend-view-placeholder',
                helper: function (event, row) {
                    // keep cell widths while dragging
                    row.children().each(function () {
                        $(this).width($(this).width());
                    });

                    return row;
                },
                stop: function (event, ui) {
                    ui.item.children().css('width', '');
                },
                update: renumber
            });
        }

        function render() {
            if (useGroups) {
                $.each(data.tabs, function () {
                    createGroup(this.code, this.label);
                });
            } else {
                createGroup('', '');
            }

            $.each(data.fields || [], function () {
                getGroup(this.group_code).append(createRow(this));
            });
        }

        /**
         * Re-read data edited in "Form Fields" (label, tab, sort order)
         * and reorder rows accordingly.
         */
        function refreshFromFieldsTab() {
            var rows = table.find('tr.frontend-view-row').get();

            $.each(rows, function (index) {
                var row = $(this),
                    id = row.data('field-id'),
                    sortOrderInput = fieldsTabInput(id, 'sort_order'),
                    labelInput = fieldsTabInput(id, 'frontend_label'),
                    groupInput = fieldsTabInput(id, 'group_code'),
                    removed = !sortOrderInput.length; // field removed in "Form Fields" (fieldset deleted from DOM)

                row.data('index', index);
                row.toggle(!removed);
                if (removed) {
                    return;
                }
                if (labelInput.length) {
                    row.find('.col-label').text(labelInput.val());
                }
                if (groupInput.length) {
                    row.attr('data-group-code', groupInput.val());
                }
            });

            rows.sort(function (a, b) {
                var diff = (parseInt(fieldsTabInput($(a).data('field-id'), 'sort_order').val(), 10) || 0) -
                    (parseInt(fieldsTabInput($(b).data('field-id'), 'sort_order').val(), 10) || 0);

                return diff || $(a).data('index') - $(b).data('index');
            });

            $.each(rows, function () {
                getGroup($(this).attr('data-group-code')).append(this);
            });

            $.each(groups, function (code, tbody) {
                if (useGroups) {
                    tbody.find('.frontend-view-group-header th').text(tabLabel(code));
                }
                tbody.toggle(tbody.find('tr.frontend-view-row:visible').length > 0);
            });
        }

        render();

        // "Form Fields" may have been edited since page load - sync when the tab is opened
        $(document).on('click', config.tabLinkSelector, refreshFromFieldsTab);
    };
});
