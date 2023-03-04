<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\WidgetForms\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class AddFrontendInputBlockOptions
 * @package Alekseon\WidgetForms\Observer
 */
class AddFrontendInputBlockOptions implements ObserverInterface
{
    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $formFieldId = $observer->getEvent()->getFormFieldId();
        $fieldset = $observer->getEvent()->getFieldset();
        $fieldSettings =  $observer->getEvent()->getFieldSettings();

        if (!isset($fieldSettings['attribute'])) {
            return;
        }

        $attribute = $fieldSettings['attribute'];
        $attribute->setFrontendInputBlock($attribute->getAttributeExtraParam('frontend_input_block'));

        $frontendInputTypeConfig = $attribute->getFrontendInputTypeConfig();
        if ($frontendInputTypeConfig) {
            $frontendBlocks = $frontendInputTypeConfig->getFrontendBlocks();
        } else {
            $frontendBlocks = [];
        }

        if (is_array($frontendBlocks) && count($frontendBlocks) > 1 ) {

            $options = [];
            foreach ($frontendBlocks as $code => $data) {
                $options[] = [
                    'value' => $code,
                    'label' => $data['label'],
                ];
            }

            $fieldset->addField('form_field_' . $formFieldId . '_frontend_input_block', 'select',
                [
                    'label' => __('Input Block Type'),
                    'name' => 'form_fields[' . $formFieldId . '][frontend_input_block]',
                    'values' => $options,
                ]
            )->addCustomAttribute("data-fieldcode", "frontend_input_block");
        }
    }
}
