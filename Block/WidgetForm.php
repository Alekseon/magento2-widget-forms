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
     * @var
     */
    private $tabs;
    /**
     * @var
     */
    private $jsonHexTag;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Alekseon\CustomFormsBuilder\Model\FormRepository $formRepository
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     * @param \Magento\Framework\EntityManager\EventManager $eventManager
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

        $this->addTabs();
        $this->addFields();

        $additionalInfoBlock = $this->addChild(
            'form_' . $form->getId() . '_additional.info',
            \Alekseon\WidgetForms\Block\Form\AdditionalInfo::class
        );

        $this->eventManager->dispatch(
            'alekseon_widget_form_prepare_layout',
            [
                'widget_block' => $this,
                'form' => $this->getForm(),
                'additional_info_block' => $additionalInfoBlock,
            ]
        );

        return parent::_toHtml();
    }

    private function addFields()
    {
        $fields = $this->getFormFieldsCollection();
        foreach ($fields as $attribute) {
            $tabCode = $attribute ? $attribute->getGroupCode() : '';
            $tab = $this->tabs[$tabCode] ?? reset($this->tabs);
            $fieldBlockAlias = 'form_' . $this->getForm()->getId() . '_field_' . $attribute->getAttributeCode();
            $tab->getBlock()->addChild(
                $fieldBlockAlias,
                \Alekseon\CustomFormsFrontend\Block\Form\Field::class,
                [
                    'attribute' => $attribute
                ]
            );
        }
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
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function addTabs()
    {
        if ($this->tabs == null) {
            $this->tabs = [];
            $tabs = [];
            $tabsCounter = 1;

            if ($this->getForm()->getEnableMultipleSteps()) {
                $formTabs = $this->getForm()->getFormTabs();
                foreach ($formTabs as $tab) {
                    $tab->setTabSequenceNumber($tabsCounter);
                    $tabs[$tab->getId()] = $tab;
                    $tabsCounter++;
                }
            }

            if (empty($tabs)) {
                // backward compatible, to be sure there is always at least one tab
                $tab = new DataObject();
                $tab->setId(1);
                $tab->setTabSequenceNumber(1);
                $tabs[1] = $tab;
            }

            $firstTab = reset($tabs);
            $firstTab->setIsFirstTab(true);

            $lastTab = reset($tabs);
            $lastTab->setILastTab(true);

            foreach ($tabs as $tabCode => $tab) {
                $fieldBlockAlias = 'form_' . $this->getForm()->getId() . '_tab_' . $tabCode;
                $tabBlock = $this->addChild(
                    $fieldBlockAlias,
                    \Alekseon\WidgetForms\Block\Form\Tab::class,
                    [
                        'form' => $this->getForm(),
                        'tab' => $tab
                    ]
                );
                $tab->setBlock($tabBlock);
                $this->tabs[$tabCode] = $tab;
            }
        }
    }

    /**
     * @return false|string
     */
    public function getTabsJson()
    {
        $tabs = [];
        foreach ($this->tabs as $tab) {
            $tabs[$tab->getTabSequenceNumber()] = [
                'id' => $tab->getId()
            ];
        }
        return $this->jsonHexTag->serialize($tabs);
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

    /**
     * @return string
     */
    public function getFormWrapperClass()
    {
        $identifier = $this->getForm()->getIdentifier();
        if ($identifier) {
            return 'alekseon-widget-' . $identifier . '-form--wrapper';
        }
    }
}
