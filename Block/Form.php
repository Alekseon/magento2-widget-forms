<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\WidgetForms\Block;

use Magento\Framework\View\Element\Template;

/**
 * Class Form
 * @package Alekseon\WidgetForms\Block
 */
class Form extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'Alekseon_WidgetForms::form.phtml';
    /**
     * @var
     */
    protected $form;
    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $formKey;

    /**
     * Form constructor.
     * @param Template\Context $context
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Framework\Data\Form\FormKey $formKey,
        array $data = []
    ) {
        $this->formKey = $formKey;
        parent::__construct($context, $data);
    }

    /**
     * @return Template
     */
    protected function _prepareLayout()
    {
        $form = $this->getForm();
        $fields = $form->getFieldsCollection();

        foreach ($fields as $field) {
            $frontendInputTypeConfig = $field->getFrontendInputTypeConfig();
            $frontendBlocks = $frontendInputTypeConfig->getFrontendBlocks();

            $frontendBlock = false;
            if (isset($frontendBlocks['default'])) {
                $frontendBlock = $frontendBlocks['default'];
            }

            if ($frontendBlock) {
                $this->addChild(
                    'form_'. $form->getId() . '_field_' . $field->getAttributeCode(),
                    $frontendBlock,
                    [
                        'field' => $field,
                        'form' => $form
                    ]
                );
            }
        }

        $this->addChild(
            'form_'. $form->getId() . '_action',
            \Alekseon\WidgetForms\Block\Form\Action::class,
            []
        );

        return parent::_prepareLayout();
    }

    /**
     * @param $form
     */
    public function setForm($form)
    {
        $this->form = $form;
    }

    /**
     * @return mixed
     */
    public function getForm()
    {
        if ($this->form == null) {
            $form = $this->getData('form');
            $this->setForm($form);
        }

        return $this->form;
    }

    /**
     * @return bool
     */
    public function getTitle()
    {
        $parentBlock = $this->getParentBlock();
        if ($parentBlock && $parentBlock->getHideTitle()) {
            return false;
        }

        return $this->getForm()->getTitle();
    }

    /**
     *
     */
    public function getDescription()
    {
        $parentBlock = $this->getParentBlock();
        if ($parentBlock && $parentBlock->getHideDescription()) {
            return false;
        }

        return $this->getForm()->getFrontendFormDescription();
    }

    /**
     *
     */
    public function getSuccessMessage()
    {
        if ($this->getForm()->getFormSubmitSuccessMessage()) {
            return $this->getForm()->getFormSubmitSuccessMessage();
        }

        return __('Form has been successfully submitted.');
    }

    /**
     * @return string
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }
}