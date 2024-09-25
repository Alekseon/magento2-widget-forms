<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\WidgetForms\Observer;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\EntityManager\EventManager;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
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
    private $subscriptionManager;
    /**
     * @var StoreManager
     */
    private $storeManager;
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;
    /**
     * @var AccountManagementInterface
     */
    private $customerAccountManagement;
    /**
     * @var EventManager
     */
    private $eventManager;

    /**
     * SubscribeToNewsletter constructor.
     *
     * @param SubscriptionManagerInterface $subscriptionManager
     * @param StoreManager $storeManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param AccountManagementInterface $customerAccountManagement
     * @param EventManager $eventManager
     */
    public function __construct(
        SubscriptionManagerInterface $subscriptionManager,
        StoreManager $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        AccountManagementInterface $customerAccountManagement,
        EventManager $eventManager
    ) {
        $this->subscriptionManager = $subscriptionManager;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->eventManager = $eventManager;
    }

    /**
     * Execute
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $formRecord = $observer->getEvent()->getFormRecord();
        $form = $formRecord->getForm();

        if ($form->getSubscribeToNewsletter()) {
            $emailField = $form->getNewsletterEmail();
            $email = $formRecord->getData($emailField);
            $storeId = (int) $this->storeManager->getStore()->getId();
            if ($email) {
                $this->validateEmailAvailable($email);
                $customerId = $this->getCurrentCustomerId();
                if ($customerId) {
                    $subscriber = $this->subscriptionManager->subscribeCustomer($customerId, $storeId);
                } else {
                    $subscriber = $this->subscriptionManager->subscribe($email, $storeId);
                }
                $this->eventManager->dispatch(
                    'alekseon_widget_form_after_subscribe',
                    [
                        'form_record' => $formRecord,
                        'subscriber' => $subscriber,
                    ]
                );
            }
        }
    }

    /**
     * @return false | int
     */
    private function getCurrentCustomerId()
    {
        if ($this->customerSession->isLoggedIn()) {
            return $this->customerSession->getCustomerDataObject()->getId();
        }

        return false;
    }

    /**
     * @param $email
     */
    private function validateEmailAvailable($email)
    {
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        if ($this->customerSession->isLoggedIn()
            && ($this->customerSession->getCustomerDataObject()->getEmail() !== $email
                && !$this->customerAccountManagement->isEmailAvailable($email, $websiteId))
        ) {
            throw new LocalizedException(
                __('This email address is already assigned to another user.')
            );
        }
    }
}
