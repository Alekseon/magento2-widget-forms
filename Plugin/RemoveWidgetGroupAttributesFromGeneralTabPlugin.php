<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\WidgetForms\Plugin;

/**
 * Class RemoveWidgetGroupAttributesFromGeneralTabPlugin
 * @package Alekseon\WidgetForms\Plugin
 */
class RemoveWidgetGroupAttributesFromGeneralTabPlugin
{
    /**
     * @param $generalTabBlock
     * @param $generalFieldset
     * @param $formObject
     * @param array $groups
     * @return array
     */
    public function beforeAddAllAttributeFields($generalTabBlock, $generalFieldset, $formObject, $groups = [])
    {
        $groups['excluded'][] = 'widget_form_attribute';
        return [$generalFieldset, $formObject, $groups];
    }
}
