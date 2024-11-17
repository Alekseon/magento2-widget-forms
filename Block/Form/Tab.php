<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 *
 */
declare(strict_types=1);

namespace Alekseon\WidgetForms\Block\Form;

use Alekseon\CustomFormsBuilder\Model\Form;
use Alekseon\CustomFormsBuilder\Model\FormTab;

/**
 * @method Tab setForm(Form $formTab)
 * @method Form getForm()
 * @method Tab setTab(FormTab $formTab)
 * @method FormTab getTab()
 */
class Tab extends \Magento\Framework\View\Element\Template
{
    protected $_template = "Alekseon_WidgetForms::form/tab.phtml";

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSubmitButtonHtml()
    {
        return $this->getLayout()->createBlock(
            \Alekseon\WidgetForms\Block\Form\Action::class,
            'form_' . $this->getForm()->getId() . '_action_' . $this->getTab()->getId(),
            [
                'data' => [
                    'submit_button_label' => $this->getSubmitButtonLabel()
                ],
            ]
        )->toHtml();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    private function getSubmitButtonLabel()
    {
        if (!$this->getTab()->getIsLastTab()) {
            return __('Next');
        }

        $form = $this->getForm();
        if ($form && $form->getSubmitButtonLabel()) {
            return $form->getSubmitButtonLabel();
        }

        return __('Submit');
    }

    /**
     * @return \Alekseon\WidgetForms\Block\WidgetForm
     */
    public function getWidgetFormBlock()
    {
        return $this->getParentBlock()->getParentBlock();
    }
}
