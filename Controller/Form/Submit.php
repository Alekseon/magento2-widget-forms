<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\WidgetForms\Controller\Form;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Submit
 * @package Alekseon\WidgetForms\Controller
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Submit implements HttpPostActionInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;
    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    private $response;
    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $jsonFactory;
    /**
     * @var \Alekseon\CustomFormsBuilder\Model\FormRepository
     */
    private $formRepository;
    /**
     * @var \Alekseon\CustomFormsBuilder\Model\FormRecordFactory
     */
    private $formRecordFactory;
    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    private $formKeyValidator;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * @var \Magento\Cms\Api\GetBlockByIdentifierInterface
     */
    private $blockByIdentifier;

    /**
     * Submit constructor.
     * @param Context $context
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Alekseon\CustomFormsBuilder\Model\FormRepository $formRepository,
        \Alekseon\CustomFormsBuilder\Model\FormRecordFactory $formRecordFactory,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Cms\Api\GetBlockByIdentifierInterface $blockByIdentifier,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->request = $context->getRequest();
        $this->response = $context->getResponse();
        $this->eventManager = $context->getEventManager();
        $this->formRecordFactory = $formRecordFactory;
        $this->jsonFactory = $jsonFactory;
        $this->blockByIdentifier = $blockByIdentifier;
        $this->formRepository = $formRepository;
        $this->formKeyValidator = $formKeyValidator;
        $this->logger = $logger;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $resultJson = $this->jsonFactory->create();

        try {
            $form = $this->getForm();
            $this->validateData();
            $post = $this->getRequest()->getPost();
            $formRecord = $this->formRecordFactory->create();
            $formRecord->getResource()->setCurrentForm($form);
            $formRecord->setStoreId($form->getStoreId());
            $formRecord->setFormId($form->getId());
            $formFields = $form->getFieldsCollection();
            foreach ($formFields as $field) {
                $fieldCode = $field->getAttributeCode();
                $value = $post[$fieldCode] ?? $field->getDefaultValue();
                $formRecord->setData($fieldCode, $value);
            }

            $formRecord->getResource()->save($formRecord);
            $this->eventManager->dispatch('alekseon_widget_form_after_submit', ['form_record' => $formRecord]);
            $resultJson->setData(
                [
                    'errors' => false,
                    'title' => $this->getSuccessTitle($formRecord),
                    'message' => $this->getSuccessMessage($formRecord),
                    'html_content' => $this->getSuccessHtmlContent((int) $form->getStoreId(), $formRecord)
                ]
            );
        } catch (LocalizedException $e) {
            $resultJson->setData(
                [
                    'errors' => true,
                    'message' => $e->getMessage()
                ]
            );
        } catch (\Exception $e) {
            $this->logger->error('Widget Form Error during submit action: ' . $e->getMessage());
            $resultJson->setData(
                [
                    'errors' => true,
                    'message' => __('We are unable to process your request. Please, try again later.'),
                ]
            );
        }

        return $resultJson;
    }

    /**
     * @param $form
     * @return string
     */
    public function getSuccessMessage($formRecord)
    {
        $successMessage = $formRecord->getForm()->getFormSubmitSuccessMessage();
        if (!$successMessage) {
            $successMessage = __('Thank You!');
        }
        return (string) $successMessage;
    }

    /**
     * @param $form
     * @return string
     */
    public function getSuccessTitle($formRecord)
    {
        $successTitle = $formRecord->getForm()->getFormSubmitSuccessTitle();
        if (!$successTitle) {
            $successTitle = __('Success');
        }
        return (string) $successTitle;
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function validateData()
    {
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            throw new LocalizedException(__('Invalid Form Key. Please refresh the page.'));
        }

        if ($this->getRequest()->getParam('hideit')) {
            throw new LocalizedException(__('Interrupted Data'));
        }
    }

    /**
     *
     */
    public function getForm()
    {
        $formId = $this->getRequest()->getParam('form_id');
        $form = $this->formRepository->getById($formId);
        return $form;
    }

    /**
     * @return \Magento\Framework\App\RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     *
     */
    public function getSuccessHtmlContent($storeid, $formRecord)
    {
        $successDisplayMode = $this->getRequest()->getParam('success_display_mode');
        $successBlockId = $this->getRequest()->getParam('success_block_id');

        if($successDisplayMode === "form") {
            try {
                $successBlock = $this->blockByIdentifier->execute($successBlockId, $storeid);
                return $successBlock->getContent();
            }
            catch(NoSuchEntityException $e) {
                return '<h2>' . $this->getSuccessTitle($formRecord) . '</h2><p>' . $this->getSuccessMessage($formRecord) . '</p>';
            }
        } else {
            return '';
        }
    }
}
