<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\WidgetForms\Model\Attribute\DefaultValueProvider;

use Alekseon\AlekseonEav\Model\Attribute\DefaultValueProvider\AbstractProvider;

/**
 * Class CurrentCmsPage
 * @package Alekseon\WidgetForms\Model\Attribute\DefaultValueProvider
 */
class CurrentCmsPage extends AbstractProvider
{
    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $cmsPage;

    /**
     * CurrentProduct constructor.
     * @param array $data
     */
    public function __construct(
        \Magento\Cms\Model\Page $cmsPage,
        array $data = []
    )
    {
        $this->cmsPage = $cmsPage;
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
        if ($this->cmsPage) {
            return $this->cmsPage->getIdentifier();
        }

        return '';
    }
}
