<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\WidgetForms\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class FormRecordAttributeSaveAfter
 * @package Alekseon\WidgetForms\Observer
 */
class FormRecordAttributeSaveAfter implements ObserverInterface
{
    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        if ($frontendInputBlock = $attribute->getFrontendInputBlock()) {
            $attribute->setAttributeExtraParam('frontend_input_block', $frontendInputBlock);
        }
    }
}
