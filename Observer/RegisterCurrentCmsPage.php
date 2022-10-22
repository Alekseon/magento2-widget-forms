<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\WidgetForms\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class RegisterCurrentCmsPage
 * @package Alekseon\WidgetForms\Observer
 */
class RegisterCurrentCmsPage implements ObserverInterface
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
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $page = $observer->getEvent()->getPage();
        $this->registry->register('current_cms_page_for_widget_forms', $page);
    }
}
