<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\WidgetForms\Plugin;

class RemoveWidgetGroupAttributesFromGeneralTabPlugin
{
    /**
     * @param \Alekseon\CustomFormsBuilder\Block\Adminhtml\Form\Edit\Tab\General $generalTabBlock
     * @param \Magento\Framework\Data\Form\Element\Fieldset $generalFieldset
     * @param \Alekseon\AlekseonEav\Api\Data\EntityInterface $formObject
     * @param array $groups
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeAddAllAttributeFields($generalTabBlock, $generalFieldset, $formObject, $groups = [])
    {
        $groups['excluded'][] = 'widget_form_attribute';
        return [$generalFieldset, $formObject, $groups];
    }
}
