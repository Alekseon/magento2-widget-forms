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
     * @var EavDataSetupFactory
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
            $this->addNewsletterAttributes($setup);
        }
    }


    /**
     *
     */
    protected function addNewsletterAttributes()
    {
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
}
