<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\WidgetForms\Plugin;

/**
 * Class AddFormFieldsetWarningPlugin
 * @package Alekseon\WidgetForms\Plugin
 */
class AddFormFieldsetWarningPlugin
{
    /**
     * @param $attribute
     * @param $formFieldSettings
     * @return mixed
     */
    public function aroundGetFieldSettings($subject, callable $proceed, $attribute)
    {
        $formFieldSettings = $proceed($attribute);
        $form = $subject->getDataObject();

        if ($form->getCanUseForWidget()) {
            $frontendInputTypeConfig = $attribute->getFrontendInputTypeConfig();
            if ($frontendInputTypeConfig) {
                $frontendBlocks = $frontendInputTypeConfig->getFrontendBlocks();

                if (!isset($frontendBlocks['default'])) {
                    $formFieldSettings['warnings'][] = __(
                        'Frontend input %1 is not supported by form widgets.',
                        $frontendInputTypeConfig->getLabel()
                    );
                }
            }
        }

        return $formFieldSettings;
    }
}