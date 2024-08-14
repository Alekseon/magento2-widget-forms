<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\WidgetForms\Plugin;

use Alekseon\CustomFormsBuilder\Block\Adminhtml\Form\Edit\Tab\Fields\Form;

/**
 * Class AddFormFieldsetWarningPlugin
 * @package Alekseon\WidgetForms\Plugin
 */
class AddFormFieldsetWarningPlugin
{
    /**
     * @param Form $subject
     * @param $formFieldSettings
     * @param $attribute
     * @return array
     */
    public function afterGetFieldSettings(Form $subject, $formFieldSettings, $attribute)
    {
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
