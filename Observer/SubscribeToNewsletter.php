<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\WidgetForms\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Newsletter\Model\SubscriptionManagerInterface;
use Magento\Store\Model\StoreManager;

/**
 * Class SubscribeToNewsletter
 * @package Alekseon\WidgetForms\Observer
 */
class SubscribeToNewsletter implements ObserverInterface
{
    /**
     * @var SubscriptionManagerInterface
     */
    protected $subscriptionManager;
    /**
     * @var StoreManager
     */
    protected $storeManager;

    /**
     * SubscribeToNewsletter constructor.
     * @param SubscriptionManagerInterface $subscriptionManager
     */
    public function __construct(
        SubscriptionManagerInterface $subscriptionManager,
        StoreManager $storeManager
    )
    {
        $this->subscriptionManager = $subscriptionManager;
        $this->storeManager = $storeManager;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $formRecord = $observer->getEvent()->getFormRecord();
        $form = $formRecord->getForm();

        if ($form->getSubscribeToNewsletter()) {
            $emailField = $form->getNewsletterEmail();
            $email = $formRecord->getData($emailField);
            if ($email) {
                $subscriber = $this->subscriptionManager->subscribe($email, $this->storeManager->getStore()->getId());
            }
        }
    }
}
