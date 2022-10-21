<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\WidgetForms\Block\Form\Field;

/**
 * Class Textarea
 * @package Alekseon\WidgetForms\Block\Form\Field
 */
class Textarea extends \Alekseon\WidgetForms\Block\Form\Field\AbstractField
{
    protected $_template = "Alekseon_WidgetForms::form/field/textarea.phtml";

    /**
     *
     */
    public function getMaxLength()
    {
        return $this->getField()->getInputParam('maxLength');
    }
}
