<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\WidgetForms\Plugin;

/**
 * Class RemoveAttributesFromGeneralTabPlugin
 * @package Alekseon\WidgetForms\Plugin
 */
class RemoveAttributesFromGeneralTabPlugin
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
        $groups['excluded'][] = 'newsletter';

        return [$generalFieldset, $formObject, $groups];
    }
}
