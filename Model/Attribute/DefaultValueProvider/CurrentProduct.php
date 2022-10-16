<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\WidgetForms\Model\Attribute\DefaultValueProvider;

use Alekseon\AlekseonEav\Model\Attribute;
use Alekseon\AlekseonEav\Model\Attribute\DefaultValueProvider\AbstractProvider;

/**
 * Class CurrentProduct
 * @package Alekseon\WidgetForms\Model\Attribute\DefaultValueProvider
 */
class CurrentProduct extends AbstractProvider
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * CurrentProduct constructor.
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        array $data = []
    )
    {
        $this->registry = $registry;
        parent::__construct($data);
    }

    /**
     * @param Attribute $attribute
     * @return bool|void
     */
    public function canBeUsedForAttribute(Attribute $attribute)
    {
        if (parent::canBeUsedForAttribute($attribute)) {
            if ($attribute->getForm()
                && $attribute->getForm()->getCanUseForWidget()
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return mixed|null
     */
    protected function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * @return string
     */
    public function getValue()
    {
        $product = $this->getCurrentProduct();
        if ($product) {
            return $product->getSku();
        }

        return '';
    }
}
