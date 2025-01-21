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
use Alekseon\WidgetForms\Block\Form\BlocksContainer;
use Alekseon\WidgetForms\Block\Form\Tab;
use Magento\Framework\Serialize\Serializer\JsonHexTag;
use Magento\Framework\EntityManager\EventManager;
use Magento\Framework\Data\Form\FormKey;

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
     * @var
     */
    private $formFieldsCollection;
    /**
     * @var FormKey
     */
    private $formKey;
    /**
     * @var EventManager
     */
    private $eventManager;
    /**
     * @var array|null
     */
    private $tabBlocks;
    /**
     * @var array|null
     */
    private $tabSequence;
    /**
     * @var JsonHexTag
     */
    private $jsonHexTag;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param FormRepository $formRepository
     * @param FormKey $formKey
     * @param EventManager $eventManager
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        FormRepository $formRepository,
        FormKey $formKey,
        EventManager $eventManager,
        JsonHexTag $jsonHexTag,
        array $data = []
    ) {
        $this->formRepository = $formRepository;
        $this->formKey = $formKey;
        $this->eventManager = $eventManager;
        $this->jsonHexTag = $jsonHexTag;
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

    /**
     * @return false|string
     */
    public function getFormTitle()
    {
        if ($this->getHideTitle()) {
            return false;
        }

        return $this->getForm()->getTitle();
    }
}
