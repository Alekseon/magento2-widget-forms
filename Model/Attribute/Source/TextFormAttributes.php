<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\WidgetForms\Model\Attribute\Source;

use Alekseon\CustomFormsBuilder\Model\Form;

/**
 * Class TextFormAttributes
 * @package Alekseon\WidgetForms\Model\Attribute\Source
 */
class TextFormAttributes extends \Alekseon\AlekseonEav\Model\Attribute\Source\AbstractSource
{
    /**
     * TextFormAttributes constructor.
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry
    )
    {
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        $options = [];

        /** @var Form $form */
        $form = $this->coreRegistry->registry('current_form');

        $fields = $form->getFieldsCollection();
        foreach ($fields as $field) {
            if ($field->getFrontendInput() == 'text') {
                $options[$field->getAttributeCode()] = $field->getFrontendLabel();
            }
        }

        return $options;
    }
}
