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
     * WidgetForm constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Alekseon\CustomFormsBuilder\Model\FormRepository $formRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Alekseon\CustomFormsBuilder\Model\FormRepository $formRepository,
        array $data = []
    ) {
        $this->formRepository = $formRepository;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Framework\View\Element\Template
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _prepareLayout()
    {
        $this->addChild(
            'form',
            \Alekseon\WidgetForms\Block\Form::class,
            [
                'form' => $this->getForm(),
            ]
        );

        return parent::_prepareLayout();
    }

    /**
     * @return bool|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getForm()
    {
        if ($this->form == null) {
            $formId = (int)$this->getData("form_id");
            $form = $this->formRepository->getById($formId, null, true);
            if ($form->getCanUseForWidget()) {
                $this->form = $form;
            } else {
                $this->form = false;
            }
        }
        return $this->form;
    }
}
