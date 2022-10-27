<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\WidgetForms\Observer;

use Magento\Customer\Api\AccountManagementInterface;
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
    protected $subscriptionManager;
    /**
     * @var StoreManager
     */
    protected $storeManager;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    /**
     * @var AccountManagementInterface
     */
    protected $customerAccountManagement;

    /**
     * SubscribeToNewsletter constructor.
     * @param SubscriptionManagerInterface $subscriptionManager
     */
    public function __construct(
        SubscriptionManagerInterface $subscriptionManager,
        StoreManager $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        AccountManagementInterface $customerAccountManagement
    )
    {
        $this->subscriptionManager = $subscriptionManager;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->customerAccountManagement = $customerAccountManagement;
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
            $storeId = $this->storeManager->getStore()->getId();
            if ($email) {
                $this->validateEmailAvailable($email);
                $customerId = $this->getCurrentCustomerId();
                if ($customerId) {
                    $this->subscriptionManager->subscribeCustomer($customerId, $storeId);
                } else {
                    $this->subscriptionManager->subscribe($email, $storeId);
                }
            }
        }
    }

    /**
     * @return false | int
     */
    protected function getCurrentCustomerId()
    {
        if ($this->customerSession->isLoggedIn()) {
            return $this->customerSession->getCustomerDataObject()->getId();
        }

        return false;
    }

    /**
     * @param $email
     */
    protected function validateEmailAvailable($email)
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
