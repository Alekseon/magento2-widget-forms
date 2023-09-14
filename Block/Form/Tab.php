<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\WidgetForms\Block\Form;

/**
 *
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
}
