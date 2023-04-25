<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\WidgetForms\Block\Form\Field;

use Alekseon\AlekseonEav\Model\Attribute\InputType\AbstractInputType;
use Alekseon\AlekseonEav\Model\Attribute\Source\Boolean;

/**
 * Class Checkbox
 * @package Alekseon\WidgetForms\Block\Form\Field
 */
class Checkbox extends \Alekseon\WidgetForms\Block\Form\Field\Select
{
    protected $_template = "Alekseon_WidgetForms::form/field/checkbox.phtml";

    /**
     * @return bool
     */
    public function displayLabel()
    {
        /** @var AbstractInputType $field */
        $field = $this->getField();

        if ($field->getFrontendInput() == 'boolean') {
            return false;
        }
        return true;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        $options = parent::getOptions();

        /** @var AbstractInputType $field */
        $field = $this->getField();

        if ($field->getFrontendInput() == 'boolean') {
            unset($options[Boolean::VALUE_NO]);
            $options[Boolean::VALUE_YES] = $this->getLabel();
        }

        return $options;
    }
}
