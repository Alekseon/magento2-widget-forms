<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\WidgetForms\Model\Attribute\DefaultValueProvider;

use Alekseon\AlekseonEav\Model\Attribute\DefaultValueProvider\AbstractProvider;

/**
 * Class CurrentCmsPage
 * @package Alekseon\WidgetForms\Model\Attribute\DefaultValueProvider
 */
class CurrentCmsPage extends AbstractProvider
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
     * @return mixed|null
     */
    protected function getCurrentCmsPage()
    {
        return $this->registry->registry('current_cms_page_for_widget_forms');
    }

    /**
     * @return string
     */
    public function getValue()
    {
        $cmsPage = $this->getCurrentCmsPage();
        if ($cmsPage) {
            return $cmsPage->getIdentifier();
        }

        return '';
    }
}
