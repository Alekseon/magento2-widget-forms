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
     * @var array
     */
    protected $formFields = [];
    /**
     * @var
     */
    protected $tabCodes;

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
                $frontendBlock['tab_code'] = $field->getGroupCode();

                $fieldBlockAlias = 'form_' . $form->getId() . '_field_' . $field->getAttributeCode();
                $this->addFormField(
                    $fieldBlockAlias,
                    $class,
                    $frontendBlock,
                );
            }
        }

        $additionalInfoBlock = $this->addFormField(
            'form_' . $form->getId() . '_additional.info',
            \Alekseon\WidgetForms\Block\Form\AdditionalInfo::class,
            [
                'tab_code' => array_key_last($this->getTabCodes())
            ]
        );

        $this->eventManager->dispatch(
            'alekseon_widget_form_prepare_layout',
            [
                'widget_block' => $this,
                'form' => $this->getForm(),
                'additional_info_block' => $additionalInfoBlock,
            ]
        );

        $this->addChild(
            'form_'. $form->getId() . '_action',
            \Alekseon\WidgetForms\Block\Form\Action::class,
        );

        return parent::_toHtml();
    }

    /**
     * @param $alias
     * @param $block
     * @param array $data
     * @return $this
     */
    public function addFormField($fieldBlockAlias, $block, $data = [])
    {
        $tabCode = $data['tab_code'] ?? '';
        $tabCodes = $this->getTabCodes();
        if (!isset($tabCodes[$tabCode])) {
            $tabCode = array_key_first($tabCodes);
        }
        if (!isset($this->formFields[$tabCode][$fieldBlockAlias])) {
            $this->addChild($fieldBlockAlias, $block, $data);
            $this->formFields[$tabCode][$fieldBlockAlias] = $fieldBlockAlias;
        }
        return $this->getChildBlock($this->formFields[$tabCode][$fieldBlockAlias]);
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
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getTabCodes()
    {
        if ($this->tabCodes === null) {
            $formTabs = $this->getForm()->getFormTabs();
            foreach ($formTabs as $tab) {
                $this->tabCodes[$tab->getId()] = $tab->getId();
            }
            if (empty($this->tabCodes)) {
                $this->tabCodes[1] = 1; // backward compatible, to be sure there is alwyas at least one tab
            }
        }
        return $this->tabCodes;
    }

    /**
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFormTabsHtml()
    {
        $tabCodes = $this->getTabCodes();
        $formTabsHtml = [];
        $tabsCounter = 0;
        foreach ($tabCodes as $tabCode) {
            $formFields = $this->formFields[$tabCode] ?? [];
            if (!isset($formTabsHtml[$tabCode])) {
                $formTabsHtml[$tabCode]['is_last'] = 0;
                $formTabsHtml[$tabCode]['fields'] = [];
                $formTabsHtml[$tabCode]['code'] = $tabCode;
                $formTabsHtml[$tabCode]['index'] = $tabsCounter;
                $formTabsHtml[$tabCode]['visible'] = $tabsCounter ? false : true;
            }
            foreach ($formFields as $field) {
                $formTabsHtml[$tabCode]['fields'][] = [
                    'html' => $this->getChildHtml($field),
                ];
            }
            $tabsCounter ++;
        }

        $formTabsHtml[$tabCode]['is_last'] = 1;
        return array_values($formTabsHtml);
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
