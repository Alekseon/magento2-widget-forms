<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\WidgetForms\Block;

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
class WidgetForm extends \Magento\Framework\View\Element\Template
    implements \Magento\Widget\Block\BlockInterface, \Magento\Framework\DataObject\IdentityInterface
{
    /**
     * @var string
     */
    protected $_template = 'Alekseon_WidgetForms::widget_form.phtml';
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
     * @inheritDoc
     */
    protected function _toHtml()
    {
        $form = $this->getForm();
        if ($form) {
            $this->addTabs();
            $this->addFields();
            $this->eventManager->dispatch(
                'alekseon_widget_form_prepare_layout',
                [
                    'widget_block' => $this,
                    'form' => $this->getForm(),
                ]
            );
        }
        return parent::_toHtml();
    }

    /**
     * @param string $alias
     * @return BlocksContainer
     */
    public function getBlocksContainer(string $alias)
    {
        $containerAlias = 'form_' . $this->getForm()->getId() . '_' . $alias;
        /** @var BlocksContainer $blocksContainer */
        $blocksContainer = $this->getChildBlock($containerAlias);
        if (!$blocksContainer) {
            $blocksContainer = $this->addChild(
                $containerAlias,
                BlocksContainer::class
            );
        }
        return $blocksContainer;
    }

    /**
     * @return void
     */
    private function addTabs()
    {
        if ($this->tabBlocks == null) {
            $this->tabBlocks = [];
            $tabs = [];

            if ($this->getForm()->getEnableMultipleSteps()) {
                $formTabs = $this->getForm()->getFormTabs();
                /** @var FormTab $tab */
                foreach ($formTabs as $tab) {
                    $tabs[$tab->getId()] = $tab;
                }
            }

            if (empty($tabs)) {
                // backward compatible, to be sure there is always at least one tab
                $tab = $this->getForm()->addFormTab();
                $tab->setId(1);
                $tabs[$tab->getId()] = $tab;
            }

            $firstTab = reset($tabs);
            $firstTab->setIsFirstTab(true);

            $lastTab = end($tabs);
            $lastTab->setIsLastTab(true);
            $tabsContainer = $this->getBlocksContainer('tabs_container');

            $tabsCounter = 0;
            /** @var FormTab $tab */
            foreach ($tabs as $tabCode => $tab) {
                $tabsCounter ++;
                $fieldBlockAlias = 'form_' . $this->getForm()->getId() . '_tab_' . $tabCode;
                $tab->setTabSequenceNumber($tabsCounter);
                $this->tabBlocks[$tabCode] = $tabsContainer->addChild(
                    $fieldBlockAlias,
                    Tab::class,
                    [
                        'form' => $this->getForm(),
                        'tab' => $tab,
                    ]
                );
                $this->tabSequence[$tabsCounter] = $tabCode;
            }
        }
    }

    /**
     * @return void
     */
    private function addFields()
    {
        $fields = $this->getFormFieldsCollection();
        /** @var Attribute $attribute */
        foreach ($fields as $attribute) {
            $tabCode = $attribute ? $attribute->getGroupCode() : '';
            /** @var Tab $tabBlock */
            $tabBlock = $this->tabBlocks[$tabCode] ?? reset($this->tabBlocks);
            $fieldBlockAlias = 'form_field_' . $attribute->getAttributeCode();
            $tabBlock->addChild(
                $fieldBlockAlias,
                \Alekseon\CustomFormsFrontend\Block\Form\Field::class,
                [
                    'attribute' => $attribute
                ]
            );
        }
    }

    /**
     * @return \Alekseon\CustomFormsBuilder\Model\ResourceModel\FormRecord\Attribute\Collection
     */
    private function getFormFieldsCollection()
    {
        if ($this->formFieldsCollection === null) {
            $form = $this->getForm();
            $this->formFieldsCollection = $form->getFieldsCollection();
        }
        return $this->formFieldsCollection;
    }

    /**
     * @param $tabSequenceNumber
     * @return false|mixed
     */
    public function getTabBlock($tabSequenceNumber)
    {
        $tabCode = $this->tabSequence[$tabSequenceNumber] ?? false;
        return $tabCode ? $this->tabBlocks[$tabCode] : false;
    }

    /**
     * @return int|null
     */
    public function getTabsCounter()
    {
        return count($this->tabSequence);
    }

    /**
     * @return false|string
     */
    public function getTabsJson()
    {
        return $this->jsonHexTag->serialize($this->tabSequence);
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
                    } catch (\Exception $e) {}
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
     * @return false|string
     */
    public function getFormTitle()
    {
        if ($this->getHideTitle()) {
            return false;
        }

        return $this->getForm()->getTitle();
    }

    /**
     * @return false|string
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
    public function getCacheLifetime(): int
    {
        return 86400;
    }

    /**
     * @return array
     */
    public function getCacheKeyInfo(): array
    {
        $cacheKeyInfo = parent::getCacheKeyInfo();
        if ($this->getForm()) {
            $cacheKeyInfo['widget_data'] =  $this->serialize();
        }
        return $cacheKeyInfo;
    }

    /**
     * @return string[]
     */
    public function getIdentities(): array
    {
        return $this->getForm() ? $this->getForm()->getIdentities() : [];
    }

    /**
     * @return string
     */
    public function getSubmitFormUrl()
    {
        return $this->getUrl('Alekseon_WidgetForms/form/submit', [
            'form_id' => $this->getForm()->getId(),
        ]);
    }

    /**
     * @return string
     */
    public function getFormWrapperClass(): string
    {
        return $this->getForm()->getIdentifier()
            ? 'alekseon-widget-' . $this->getForm()->getIdentifier() . '-form--wrapper'
            : '';
    }
}
