<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\WidgetForms\Block;

use Magento\Framework\DataObject;
use Magento\Framework\Serialize\Serializer\JsonHexTag;

/**
 * Class WidgetForm
 * @package Alekseon\WidgetForms\Block
 */
class WidgetForm extends \Magento\Framework\View\Element\Template
    implements \Magento\Widget\Block\BlockInterface, \Magento\Framework\DataObject\IdentityInterface
{
    /**
     * @var string
     */
    protected $_template = 'Alekseon_WidgetForms::widget_form.phtml';
    /**
     * @var \Alekseon\CustomFormsBuilder\Model\FormRepository
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
     * @var \Magento\Framework\Data\Form\FormKey
     */
    private $formKey;
    /**
     * @var \Magento\Framework\EntityManager\EventManager
     */
    private $eventManager;
    /**
     * @var JsonHexTag
     */
    private $jsonHexTag;
    /**
     * @var array
     */
    private $formFields = [];
    /**
     * @var
     */
    private $tabs;

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
     * @return string
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
            if (!$frontendInputTypeConfig) {
                continue;
            }
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
                $frontendBlock['is_required'] = $field->getIsRequired();
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
                'tab_code' => array_key_last($this->getTabs())
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

        $tabs = $this->getTabs();
        foreach ($tabs as $tabCode => $tab) {
            $this->addChild(
                'form_' . $form->getId() . '_action_' . $tabCode,
                \Alekseon\WidgetForms\Block\Form\Action::class,
            )->setSubmitButtonLabel($this->getSubmitButtonLabel($tab));
        }

        return parent::_toHtml();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getSubmitButtonLabel($tab)
    {
        if (!$tab->getIsLastTab()) {
            return __('Next');
        }

        $form = $this->getForm();
        if ($form && $form->getSubmitButtonLabel()) {
            return $form->getSubmitButtonLabel();
        }

        return __('Submit');
    }

    /**
     * @param $alias
     * @param $block
     * @param array $data
     * @return $this|bool
     */
    public function addFormField($fieldBlockAlias, $block, $data = [])
    {
        $tabCode = $data['tab_code'] ?? '';
        $fieldClass = '';
        $isRequired = $data['is_required'] ?? false;
        if ($isRequired) {
            $fieldClass = 'required';
        }

        $tabs = $this->getTabs();
        if (!isset($tabs[$tabCode])) {
            $tabCode = array_key_first($tabs);
        }
        if (!isset($this->formFields[$tabCode][$fieldBlockAlias])) {
            $this->addChild($fieldBlockAlias, $block, $data);
            $this->formFields[$tabCode][$fieldBlockAlias] = [
                'block' => $fieldBlockAlias,
                'field_class' => $fieldClass,
            ];
        }
        return $this->getChildBlock($this->formFields[$tabCode][$fieldBlockAlias]['block']);
    }

    /**
     * @return |null
     */
    public function getFormFieldsCollection()
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
    public function getTabs()
    {
        if ($this->tabs === null) {
            $this->tabs = [];

            if ($this->getForm()->getEnableMultipleSteps()) {
                $formTabs = $this->getForm()->getFormTabs();
                foreach ($formTabs as $tab) {
                    $this->tabs[$tab->getId()] = $tab;
                }
            }

            if (empty($this->tabs)) {
                // backward compatible, to be sure there is alwyas at least one tab
                $tab = new DataObject();
                $tab->setId(1);
                $tab->setIsLastTab(true);
                $this->tabs[1] = $tab;
            }
        }
        return $this->tabs;
    }

    /**
     * @return bool|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFormTabsHtmlJson()
    {
        $tabs = $this->getTabs();
        $formTabsHtml = [];
        $tabsCounter = 0;
        $lastTabCode = false;

        foreach ($tabs as $tabCode => $tab) {
            $formFields = $this->formFields[$tabCode] ?? [];
            if (!isset($formTabsHtml[$tabCode])) {
                $formTabsHtml[$tabCode]['is_last'] = 0;
                $formTabsHtml[$tabCode]['fields'] = [];
                $formTabsHtml[$tabCode]['code'] = $tabCode;
                $formTabsHtml[$tabCode]['index'] = $tabsCounter;
                $formTabsHtml[$tabCode]['visible'] = $tabsCounter ? false : true;
            }
            foreach ($formFields as $field) {
                $fieldHtml = $this->getChildHtml($field['block']);
                if ($fieldHtml) {
                    $formTabsHtml[$tabCode]['fields'][] = [
                        'html' => $this->getChildHtml($field['block']),
                        'field_class' => $field['field_class'],
                    ];
                }
            }

            $formTabsHtml[$tabCode]['actionHtml'] = $this->getActionToolbarHtml($tab);
            $lastTabCode = $tabCode;
            $tabsCounter ++;
        }

        if ($lastTabCode) {
            $formTabsHtml[$lastTabCode]['is_last'] = 1;
        }

        return $this->jsonHexTag->serialize(array_values($formTabsHtml));
    }

    /**
     * @return string
     */
    public function getActionToolbarHtml($tab)
    {
        $form = $this->getForm();
        return $this->getChildHtml('form_'. $form->getId() . '_action_' . $tab->getId());
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
     * @return string|bool
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
     * @return bool|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getUiComponentChildrenJson()
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

        return $this->jsonHexTag->serialize($dataObject->getUiComponentChildren());
    }

    /**
     * @return int
     */
    public function getCacheLifetime()
    {
        return 86400;
    }

    /**
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $cacheKeyInfo = parent::getCacheKeyInfo();
        if ($this->getForm()) {
            $cacheKeyInfo['widget_data'] =  $this->serialize();
        }
        return $cacheKeyInfo;
    }

    /**
     * @return string[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getIdentities()
    {
        if ($this->getForm()) {
            return $this->getForm()->getIdentities();
        }
        return  [];
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSubmitFormUrl()
    {
        return $this->getUrl('Alekseon_WidgetForms/form/submit', [
            'form_id' => $this->getForm()->getId()
        ]);
    }
}
