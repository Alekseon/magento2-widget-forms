<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\WidgetForms\Controller\Form;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Widget\Model\Widget;

/**
 * Class Success
 * @package Alekseon\WidgetForms\Controller
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Success implements HttpGetActionInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;
    /**
     * @var \Alekseon\CustomFormsBuilder\Model\FormRepository
     */
    private $formRepository;
    /**
     * @var \Magento\Widget\Model\Template\FilterEmulate
     */
    private $templateFilter;
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;
    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * Submit constructor.
     * @param Context $context
     */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Alekseon\CustomFormsBuilder\Model\FormRepository $formRepository,
        \Magento\Widget\Model\Template\FilterEmulate $templateFilter,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    ) {
        $this->request = $context->getRequest();
        $this->resultPageFactory = $resultPageFactory;
        $this->formRepository = $formRepository;
        $this->templateFilter = $templateFilter;
        $this->customerSession = $customerSession;
        $this->resultForwardFactory = $resultForwardFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $customerFormId = $this->customerSession->getWidgetFormsSubmitFormId();
        $form = $this->getForm();
        if (!$form || !$customerFormId) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }

        if ($form->getId() != $customerFormId) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }
        $this->customerSession->setWidgetFormsSubmitFormId(null);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->initLayout();
        $successBlock = $resultPage->getLayout()->getBlock(
            'widgetforms_success'
        );

        if ($successBlock) {
            $successBlock->setData('form_id', $form->getId());
            $successBlock->setData('success_message', $this->getSuccessMessage($form));
        }
        $resultPage->getConfig()->getTitle()->append($form->getTitle());
        $resultPage->getConfig()->getTitle()->set($this->getSuccessTitle($form));
        return $resultPage;
    }

    /**
     * @param $form
     * @return string
     */
    public function getSuccessMessage($form)
    {
        $successMessage = $form->getFormSubmitSuccessMessage();
        if ($successMessage) {
            $successMessage = $this->templateFilter->filter($successMessage);
        } else {
            $successMessage = __('Thank You!');
        }
        return (string) $successMessage;
    }

    /**
     * @param $form
     * @return string
     */
    public function getSuccessTitle($form)
    {
        $successTitle = $form->getFormSubmitSuccessTitle();
        if (!$successTitle) {
            $successTitle = __('Success');
        }
        return (string) $successTitle;
    }

    /**
     * @return \Alekseon\CustomFormsBuilder\Model\Form|false
     * @throws NoSuchEntityException
     */
    public function getForm()
    {
        $formId = $this->getRequest()->getParam('form_id');
        $form = false;
        if ($formId) {
            try {
                $form = $this->formRepository->getByIdentifier($formId, null, true);
            } catch (\Exception $e) {}
        } else {
            try {
                $form = $this->formRepository->getById($formId, null, true);
            } catch (\Exception $e) {}
        }

        if ($form && $form->getCanUseForWidget()) {
            return $form;
        } else {
            return false;
        }
    }

    /**
     * @return \Magento\Framework\App\RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }
}
