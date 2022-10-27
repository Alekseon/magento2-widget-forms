<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\WidgetForms\Setup;

use Alekseon\AlekseonEav\Model\Adminhtml\System\Config\Source\Scopes;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

/**
 * Class UpgradeData
 * @package Alekseon\WidgetForms\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Alekseon\AlekseonEav\Setup\EavDataSetupFactory
     */
    protected $eavSetupFactory;
    /**
     * @var \Alekseon\CustomFormsBuilder\Model\Form\AttributeRepository
     */
    protected $formAttributeRepository;

    /**
     * InstallData constructor.
     * @param \Alekseon\AlekseonEav\Setup\EavDataSetupFactory $eavSetupFactory
     * @param \Alekseon\CustomFormsBuilder\Model\Form\AttributeRepository $formAttributeRepository
     */
    public function __construct(
        \Alekseon\AlekseonEav\Setup\EavDataSetupFactory $eavSetupFactory,
        \Alekseon\CustomFormsBuilder\Model\Form\AttributeRepository $formAttributeRepository
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->formAttributeRepository = $formAttributeRepository;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->addNewsletterAttributes();
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $this->addFormSubmitSuccessMessage();
        }

        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.3', '<')) {
            $this->removeFormSubmitFailureMessage();
            $this->addFormSubmitSuccessTitle();
        }
    }

    /**
     *
     */
    protected function addNewsletterAttributes()
    {
        /** @var \Alekseon\AlekseonEav\Setup\EavDataSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->setAttributeRepository($this->formAttributeRepository);

        $eavSetup->createAttribute(
            'subscribe_to_newsletter',
            [
                'frontend_input' => 'boolean',
                'frontend_label' => 'Subscribe to newletter',
                'visible_in_grid' => false,
                'is_required' => false,
                'sort_order' => 10,
                'scope' => Scopes::SCOPE_GLOBAL,
                'group_code' => 'newsletter',
            ]
        );

        $eavSetup->createAttribute(
            'newsletter_email',
            [
                'frontend_input' => 'select',
                'frontend_label' => 'Email field',
                'backend_type' => 'varchar',
                'source_model' => 'Alekseon\WidgetForms\Model\Attribute\Source\TextFormAttributes',
                'visible_in_grid' => false,
                'is_required' => false,
                'sort_order' => 20,
                'scope' => Scopes::SCOPE_GLOBAL,
                'group_code' => 'newsletter',
            ]
        );
    }

    /**
     *
     */
    protected function addFormSubmitSuccessMessage()
    {
        /** @var \Alekseon\AlekseonEav\Setup\EavDataSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->setAttributeRepository($this->formAttributeRepository);

        $eavSetup->updateAttribute(
            'form_submit_success_message',
            [
                'frontend_input' => 'textarea',
                'frontend_label' => 'Form Submit Success Message',
                'visible_in_grid' => false,
                'is_required' => false,
                'sort_order' => 30,
                'group_code' => 'widget_form_attribute',
                'scope' => Scopes::SCOPE_STORE,
                'is_wysiwyg_enabled' => true,
            ]
        );
    }

    /**
     *
     */
    protected function addFormSubmitSuccessTitle()
    {
        /** @var \Alekseon\AlekseonEav\Setup\EavDataSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->setAttributeRepository($this->formAttributeRepository);

        $eavSetup->createOrUpdateAttribute(
            'form_submit_success_title',
            [
                'frontend_input' => 'text',
                'frontend_label' => 'Form Submit Success Title',
                'visible_in_grid' => false,
                'is_required' => false,
                'sort_order' => 29,
                'group_code' => 'widget_form_attribute',
                'scope' => Scopes::SCOPE_STORE,
            ]
        );
    }

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function removeFormSubmitFailureMessage()
    {
        /** @var \Alekseon\AlekseonEav\Setup\EavDataSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->setAttributeRepository($this->formAttributeRepository);
        $eavSetup->deleteAttribute('form_submit_failure_message');
    }
}
