<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\WidgetForms\Block\Adminhtml\Form\Edit\Tab\WidgetSettings;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * "Frontend View" fieldset in the "Widget Settings" tab: field layout (order + width) on the frontend.
 * Rendered as "form_after" child of the Widget Settings form (see layout alekseon_customformsbuilder_form_edit.xml).
 */
class FrontendView extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Alekseon_WidgetForms::form/edit/tab/widget_settings/frontend_view.phtml';

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var Json
     */
    private $json;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param Json $json
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Json $json,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->json = $json;
        parent::__construct($context, $data);
    }

    /**
     * Data for the Frontend View table (JS: Alekseon_WidgetForms/js/form/edit/frontend-view).
     *
     * Format:
     * {
     *   "multipleSteps": bool,                                  // form "enable_multiple_steps"
     *   "tabs": [{"code": "1", "label": "General"}],            // form tabs (steps)
     *   "fields": [{
     *       "id": "1",                                          // field (record attribute) id
     *       "label": "First name",
     *       "type": "Text Field",                               // frontend input label
     *       "group_code": "1",                                  // tab code the field belongs to
     *       "sort_order": 10,
     *       "width": 100                                        // 25 | 50 | 75 | 100
     *   }],                                                     // sorted by sort_order
     *   "widthOptions": [{"value": 25, "label": "25%"}, ...]
     * }
     *
     * Values marked "// MOCK" are hardcoded test data - TODO backend: replace them.
     * All other values are final (real form data).
     *
     * @return string
     */
    public function getFieldsJson(): string
    {
        /** @var \Alekseon\CustomFormsBuilder\Model\Form|null $form */
        $form = $this->registry->registry('current_form');
        if (!$form || !$form->getId()) {
            return $this->json->serialize(['multipleSteps' => false, 'tabs' => [], 'fields' => [], 'widthOptions' => []]);
        }

        $tabs = [];
        foreach ($form->getFormTabs() as $tab) {
            $tabs[] = ['code' => (string) $tab->getId(), 'label' => (string) $tab->getLabel()];
        }

        $fields = [];
        foreach ($form->getFieldsCollection() as $field) {
            $typeConfig = $field->getFrontendInputTypeConfig();
            $fields[] = [
                'id' => (string) $field->getId(),
                'label' => $this->getFieldLabel($field),
                'type' => $typeConfig ? (string) __($typeConfig->getLabel()) : (string) $field->getFrontendInput(),
                'group_code' => (string) $field->getGroupCode(),
                'sort_order' => (int) $field->getSortOrder(),
                'width' => 100, // MOCK - saved getAttributeExtraParam('frontend_width'), validated, default 100
            ];
        }

        return $this->json->serialize([
            'multipleSteps' => (bool) $form->getEnableMultipleSteps(),
            'tabs' => $tabs,
            'fields' => $fields,
            'widthOptions' => [
                ['value' => 25, 'label' => '25%'],
                ['value' => 50, 'label' => '50%'],
                ['value' => 75, 'label' => '75%'],
                ['value' => 100, 'label' => '100%'],
            ],
        ]);
    }

    /**
     * Field label; static fields (Static Text, Static Heading, CMS Block) have no label - show their content instead.
     *
     * @param \Alekseon\CustomFormsBuilder\Model\FormRecord\Attribute $field
     * @return string
     */
    private function getFieldLabel($field): string
    {
        $label = (string) $field->getFrontendLabel();
        if ($label === '') {
            $content = $field->getInputParam('content') ?: $field->getInputParam('cms_block_id');
            $label = mb_strimwidth(trim(strip_tags((string) $content)), 0, 50, '…');
        }

        return $label;
    }
}
