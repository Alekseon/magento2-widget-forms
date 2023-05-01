<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\WidgetForms\Setup\Patch\Data;

use Alekseon\AlekseonEav\Model\Adminhtml\System\Config\Source\Scopes;
use Alekseon\CustomFormsBuilder\Model\FormFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

class CreateWidgetFormsAttributesPatch implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @var \Alekseon\AlekseonEav\Setup\EavDataSetupFactory
     */
    private $eavSetupFactory;
    /**
     * @var \Alekseon\CustomFormsBuilder\Model\Form\AttributeRepository
     */
    private $formAttributeRepository;
    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param \Alekseon\AlekseonEav\Setup\EavDataSetupFactory $eavSetupFactory
     * @param \Alekseon\CustomFormsBuilder\Model\Form\AttributeRepository $formAttributeRepository
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        \Alekseon\AlekseonEav\Setup\EavDataSetupFactory $eavSetupFactory,
        \Alekseon\CustomFormsBuilder\Model\Form\AttributeRepository $formAttributeRepository,
        FormFactory $formFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->formAttributeRepository = $formAttributeRepository;
        $this->formFactory = $formFactory;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->setAttributeRepository($this->formAttributeRepository);

        /**
         * remove attributes used in old versions of module
         */
        $eavSetup->deleteAttribute('form_submit_failure_message');

        $this->createWidgetFormAttributes($eavSetup);
        $this->createNewsletterAttributes($eavSetup);
        $this->createMultistepAttribites($eavSetup);

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @param $eavSetup
     * @return void
     */
    private function createWidgetFormAttributes($eavSetup)
    {
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

        $eavSetup->createOrUpdateAttribute(
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

    private function createNewsletterAttributes($eavSetup)
    {
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
     * @param $eavSetup
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function createMultistepAttribites($eavSetup)
    {
        $enableMultipleStepsAttribute = $eavSetup->createOrUpdateAttribute(
            'enable_multiple_steps',
            [
                'frontend_input' => 'boolean',
                'frontend_label' => 'Enable Multiple Steps',
                'visible_in_grid' => false,
                'is_required' => true,
                'sort_order' => 50,
                'group_code' => 'widget_form_attribute',
                'scope' => Scopes::SCOPE_GLOBAL,
                'note' => 'If Yes, display tabs as separated steps',
            ]
        );

        // this attribute "enable_multiple_steps" was created with typo in code in old version of module
        $wrongAttribute = $this->formAttributeRepository->getByAttributeCode('enable_multpiple_steps', true);
        if ($wrongAttribute && $wrongAttribute->getId()) {
            $formsWithEnabledSteps = $this->formFactory->create()->getCollection()
                ->addAttributeToFilter('enable_multpiple_steps', 1);

            foreach ($formsWithEnabledSteps as $form) {
                $form->setEnableMultipleSteps(1);
                $form->saveAttributeValue($form, $enableMultipleStepsAttribute);
            }

            $eavSetup->deleteAttribute('enable_multpiple_steps');
        }
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function revert()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->setAttributeRepository($this->formAttributeRepository);

        $eavSetup->deleteAttribute('can_use_for_widget');
        $eavSetup->deleteAttribute('frontend_form_description');
        $eavSetup->deleteAttribute('submit_button_label');
        $eavSetup->deleteAttribute('form_submit_success_title');
        $eavSetup->deleteAttribute('form_submit_success_message');
        $eavSetup->deleteAttribute('subscribe_to_newsletter');
        $eavSetup->deleteAttribute('newsletter_email');
        $eavSetup->deleteAttribute('enable_multiple_steps');

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }
}
