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
}
