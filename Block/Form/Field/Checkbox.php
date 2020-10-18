<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\WidgetForms\Block\Form\Field;

use Alekseon\AlekseonEav\Model\Attribute\Source\Boolean;

/**
 * Class Checkbox
 * @package Alekseon\WidgetForms\Block\Form\Field
 */
class Checkbox extends \Alekseon\WidgetForms\Block\Form\Field\AbstractField
{
    protected $_template = "Alekseon_WidgetForms::form/field/checkbox.phtml";

    /**
     * @return int
     */
    public function getValue()
    {
        return Boolean::VALUE_YES;
    }
}
