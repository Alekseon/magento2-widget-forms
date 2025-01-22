<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\WidgetForms\Block\Form;

use Alekseon\CustomFormsBuilder\Model\Form\Attribute;
use Alekseon\CustomFormsBuilder\Model\FormTab;
use Alekseon\CustomFormsBuilder\Model\FormRepository;

/**
 * Class WidgetForm
 * @package Alekseon\WidgetForms\Block
 *
 * @method bool getHideTitle()
 * @method bool getHideDescription()
 */
class Success extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'Alekseon_WidgetForms::success.phtml';
    /**
     * @var FormRepository
     */
    private $formRepository;
    /**
     * @var
     */
    private $form;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param FormRepository $formRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        FormRepository $formRepository,
        array $data = []
    ) {
        $this->formRepository = $formRepository;
        parent::__construct($context, $data);
    }

    /**
     * @return \Alekseon\CustomFormsBuilder\Model\Form|false
     */
    public function getForm()
    {
        if ($this->form === null) {
            $identifier = $this->getData('form_identifier');
            $form = false;
            if ($identifier) {
                try {
                    $form = $this->formRepository->getByIdentifier($identifier, null, true);
                } catch (\Exception $e) {}
            } else {
                $formId = (int)$this->getData('form_id');
                if ($formId) {
                    try {
                        $form = $this->formRepository->getById($formId, null, true);
                    } catch (\Exception $e) {
                    }
                }
            }
            $this->form = $form;
        }

        return $this->form;
    }
}
