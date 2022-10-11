<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
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
    protected $selectedOptions;

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
            if (is_array($defaultValue)) {
                $options = $this->getOptions();
                foreach ($options as $id => $label) {
                    if (in_array($id, $defaultValue)) {
                        $this->selectedOptions[$id] = $id;
                    }
                }
            }
        }
        return isset($this->selectedOptions[$optionId]);
    }
}
