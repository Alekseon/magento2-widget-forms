<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\WidgetForms\Model\Attribute\DefaultValueProvider;

use Alekseon\AlekseonEav\Model\Attribute\DefaultValueProvider\AbstractProvider;

/**
 * Class CurrentProduct
 * @package Alekseon\WidgetForms\Model\Attribute\DefaultValueProvider
 */
class CurrentProduct extends AbstractProvider
{
    /**
     * @var \Magento\Catalog\Helper\Data
     */
    protected $catalogHelper;

    /**
     * @param \Magento\Catalog\Helper\Data $catalogHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Helper\Data $catalogHelper,
        array $data = []
    )
    {
        $this->catalogHelper = $catalogHelper;
        parent::__construct($data);
    }

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
     * @return string
     */
    public function getValue()
    {
        $product = $this->catalogHelper->getProduct();
        if ($product) {
            return $product->getSku();
        }

        return '';
    }
}
