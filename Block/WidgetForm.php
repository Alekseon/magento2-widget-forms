<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\WidgetForms\Block;
/**
 * Class WidgetForm
 * @package Alekseon\WidgetForms\Block
 */
class WidgetForm extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    /**
     * @var string
     */
    protected $_template = 'Alekseon_WidgetForms::widget_form.phtml';
    /**
     * @var \Alekseon\CustomFormsBuilder\Model\FormRepository
     */
    protected $formRepository;
    /**
     * @var
     */
    protected $form;
    /**
     * @var
     */
    protected $formFieldsCollection;
    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $formKey;
    /**
     * @var \Magento\Framework\EntityManager\EventManager
     */
    protected $eventManager;

    /**
     * WidgetForm constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Alekseon\CustomFormsBuilder\Model\FormRepository $formRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Alekseon\CustomFormsBuilder\Model\FormRepository $formRepository,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Framework\EntityManager\EventManager $eventManager,
        array $data = []
    ) {
        $this->formRepository = $formRepository;
        $this->formKey = $formKey;
        $this->eventManager = $eventManager;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Framework\View\Element\Template
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _toHtml()
    {
        $form = $this->getForm();
        if (!$form) {
            return parent::_toHtml();
        }

        $fields = $this->getFormFieldsCollection();

        foreach ($fields as $field) {
            $frontendInputTypeConfig = $field->getFrontendInputTypeConfig();
            $frontendBlocks = $frontendInputTypeConfig->getFrontendBlocks();
            $frontendBlock = [];
            $frontendInputBlock = $field->getAttributeExtraParam('frontend_input_block');

            if (isset($frontendBlocks['default'])) {
                $frontendBlock = $frontendBlocks['default'];
            }

            if (isset($frontendBlocks[$frontendInputBlock])) {
                $frontendBlock = array_merge($frontendBlock, $frontendBlocks[$frontendInputBlock]);
            }

            if (isset($frontendBlock['class'])) {

                $class = $frontendBlock['class'];
                unset($frontendBlock['class']);
                $frontendBlock['field'] = $field;
                $frontendBlock['form'] = $form;

                $block = $this->addChild(
                    'form_'. $form->getId() . '_field_' . $field->getAttributeCode(),
                    $class,
                    $frontendBlock
                );
            }
        }

        $additinalInfoBlock = $this->addChild(
            'form_' . $form->getId() . '_additional.info',
            \Alekseon\WidgetForms\Block\Form\AdditionalInfo::class
        );

        $this->eventManager->dispatch(
            'alekseon_widget_form_prepare_layout',
            [
                'form' => $this->getForm(),
                'additional_info_block' => $additinalInfoBlock,
            ]
        );

        $this->addChild(
            'form_'. $form->getId() . '_action',
            \Alekseon\WidgetForms\Block\Form\Action::class,
            []
        );

        return parent::_toHtml();
    }

    /**
     * @return |null
     */
    protected function getFormFieldsCollection()
    {
        if ($this->formFieldsCollection === null) {
            $form = $this->getForm();
            $this->formFieldsCollection = $form->getFieldsCollection();
        }
        return $this->formFieldsCollection;
    }

    /**
     * @return string
     */
    public function getFormFieldsHtml()
    {
        $form = $this->getForm();

        $fields = $this->getFormFieldsCollection();
        $html = '';
        foreach ($fields as $field) {
            $html .= $this->getChildHtml('form_'. $form->getId() . '_field_' . $field->getAttributeCode());
        }

        $additionalInfo = $this->getChildHtml('form_'. $form->getId() . '_additional.info');
        $html .= $additionalInfo;

        return $html;
    }

    /**
     * @return string
     */
    public function getActtionToolbarHtml()
    {
        $form = $this->getForm();
        return $this->getChildHtml('form_'. $form->getId() . '_action');
    }

    /**
     * @return false|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getForm()
    {
        if ($this->form === null) {
            $identifier = $this->getData('form_identifier');
            $form = false;
            if ($identifier) {
                $form = $this->formRepository->getByIdentifier($identifier, null);
            } else {
                $formId = (int)$this->getData('form_id');
                if ($formId) {
                    $form = $this->formRepository->getById($formId, null, true);
                }
            }

            if ($form && $form->getCanUseForWidget()) {
                $this->form = $form;
            } else {
                $this->form = false;
            }
        }

        return $this->form;
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFormTitle()
    {
        if ($this->getHideTitle()) {
            return false;
        }

        return $this->getForm()->getTitle();
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFormDescription()
    {
        if ($this->getHideDescription()) {
            return false;
        }

        return $this->getForm()->getFrontendFormDescription();
    }

    /**
     * @return string
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    /**
     * @return array
     */
    public function getUiComponentChildren()
    {
        $dataObject = new \Magento\Framework\DataObject();
        $dataObject->setUiComponentChildren([]);

        $this->eventManager->dispatch(
            'alekseon_widget_form_ui_component_children',
            [
                'widget_block' => $this,
                'form' => $this->getForm(),
                'data_object' => $dataObject,
            ]
        );

        return $dataObject->getUiComponentChildren();
    }
}
