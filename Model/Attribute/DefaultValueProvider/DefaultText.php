<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\WidgetForms\Model\Attribute\DefaultValueProvider;

use Alekseon\AlekseonEav\Model\Attribute\DefaultValueProvider\AbstractProvider;

/**
 * Class DefaultText
 * @package Alekseon\WidgetForms\Model\Attribute\DefaultValueProvider
 */
class DefaultText extends AbstractProvider
{
    /**
     * @return bool
     */
    public function canBeUsedForAttribute()
    {
        if (parent::canBeUsedForAttribute()) {
            if ($this->attribute->getForm()
                && $this->attribute->getForm()->getCanUseForWidget()
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return mixed|void
     */
    public function getValue()
    {
        return $this->attribute->getInputParam('defaultText');
    }
}
