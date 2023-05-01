<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\WidgetForms\Block\Form\Field;

/**
 * Class Select
 * @package Alekseon\WidgetForms\Block\Form\Field
 */
class Select extends \Alekseon\WidgetForms\Block\Form\Field\AbstractField
{
    protected $_template = "Alekseon_WidgetForms::form/field/select.phtml";
    /**
     * @var
     */
    private $selectedOptions;

    /**
     * @return array
     */
    public function getOptions()
    {
        $field = $this->getField();
        $sourceModel = $field->getSourceModel();
        if ($sourceModel) {
            return $sourceModel->getOptions();
        }
        return [];
    }

    /**
     * @return bool
     */
    public function isSelected($optionId)
    {
        if ($this->selectedOptions === null) {
            $this->selectedOptions = [];
            $defaultValue = $this->getField()->getDefaultValue();

            if (!is_array($defaultValue)) {
                if ($defaultValue !== false) {
                    $defaultValue = [(string) $defaultValue];
                } else {
                    $defaultValue = [];
                }
            }

            $options = $this->getOptions();
            foreach (array_keys($options) as $optId) {
                if (in_array($optId, $defaultValue)) {
                    $this->selectedOptions[$optId] = $optId;
                }
            }
        }
        return isset($this->selectedOptions[$optionId]);
    }

    /**
     * @return false
     */
    public function hasEmptyOption()
    {
        return $this->getField()->getInputTypeModel()->hasEmptyOption();
    }
}
