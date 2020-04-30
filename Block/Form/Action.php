<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\WidgetForms\Block\Form;

/**
 * Class Action
 * @package Alekseon\WidgetForms\Block\Form
 */
class Action extends \Magento\Framework\View\Element\Template
{
    protected $_template = "Alekseon_WidgetForms::form/action.phtml";

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getSubmitButtonLabel()
    {
        $parentBlock = $this->getParentBlock();
        if ($parentBlock) {
            $form = $parentBlock->getForm();
            if ($form && $form->getSubmitButtonLabel()) {
                return $form->getSubmitButtonLabel();
            }
        }

        return __('Submit');
    }
}
