<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\WidgetForms\Setup;

use Alekseon\AlekseonEav\Model\Adminhtml\System\Config\Source\Scopes;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var EavDataSetupFactory
     */
    protected $eavSetupFactory;
    /**
     * @var \Alekseon\WidgetForms\Model\Form\AttributeRepository
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
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->setAttributeRepository($this->formAttributeRepository);

        $eavSetup->createAttribute(
            'can_use_for_widget',
            [
                'frontend_input' => 'boolean',
                'frontend_label' => 'Can use as frontend widget',
                'visible_in_grid' => true,
                'is_required' => true,
                'sort_order' => 50,
                'scope' => Scopes::SCOPE_GLOBAL,
            ]
        );

        $eavSetup->createAttribute(
            'frontend_form_description',
            [
                'frontend_input' => 'textarea',
                'frontend_label' => 'Frontend Form Description',
                'visible_in_grid' => false,
                'is_required' => false,
                'sort_order' => 10,
                'group_code' => 'widget_form_attribute',
                'scope' => Scopes::SCOPE_STORE,
            ]
        );

        $eavSetup->createAttribute(
            'submit_button_label',
            [
                'frontend_input' => 'text',
                'frontend_label' => 'Submit Button Label',
                'visible_in_grid' => false,
                'is_required' => false,
                'sort_order' => 20,
                'group_code' => 'widget_form_attribute',
                'scope' => Scopes::SCOPE_STORE,
            ]
        );

        $eavSetup->createAttribute(
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

        $eavSetup->createAttribute(
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
}
