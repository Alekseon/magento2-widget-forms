<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\WidgetForms\Plugin;

/**
 * Class AddWidgetFormsToAdminMenuPlugin
 * @package Alekseon\WidgetForms\Plugin
 */
class AddWidgetFormsToAdminMenuPlugin
{
    /**
     * @var \Magento\Backend\Model\Menu\Item\Factory
     */
    protected $menuItemFactory;
    /**
     * @var \Alekseon\WidgetForms\Model\ResourceModel\Form\CollectionFactory
     */
    protected $formCollectionFactory;

    /**
     * AddWidgetFormsToAdminMenuPlugin constructor.
     * @param \Magento\Backend\Model\Menu\Item\Factory $menuItemFactory
     * @param \Alekseon\CustomFormsBuilder\Model\ResourceModel\Form\CollectionFactory $formCollectionFactory
     */
    public function __construct(
        \Magento\Backend\Model\Menu\Item\Factory $menuItemFactory,
        \Alekseon\CustomFormsBuilder\Model\ResourceModel\Form\CollectionFactory $formCollectionFactory
    )
    {
        $this->menuItemFactory = $menuItemFactory;
        $this->formCollectionFactory = $formCollectionFactory;
    }

    /**
     * @param $builder
     * @param $menu
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterGetResult($builder, $menu)
    {
        $formCollection = $this->formCollectionFactory->create();
        $formCollection->addAttributeToSelect('title');
        $formCollection->addAttributeToFilter('can_use_for_widget', true);

        foreach ($formCollection as $form) {
            $defaultTitle = __('Form #%1', $form->getId());

            $title = $form->getTitle();
            if (strlen($title) < 3) {
                $title = $defaultTitle;
            }
            if (strlen($title) > 50) {
                $title = substr($title, 0, 50);
            }

            $params = [
                'id' => 'widget_forms_answer_' . $form->getId(),
                'title' => $title,
                'resource' => 'Alekseon_WidgetForms::widget_forms_answers',
                'action' => 'alekseon_customFormsBuilder/formRecord/index/id/' . $form->getId(),
            ];

            try {
                $item = $this->menuItemFactory->create($params);
            } catch (\InvalidArgumentException $e) {
                $params['title'] = (string) $defaultTitle;
                $item = false;
            }

            try {
                if (!$item) {
                    $item = $this->menuItemFactory->create($params);
                }
                $contentElements = $menu->get('Alekseon_WidgetForms::widget_forms_answers');
                $contentElements->getChildren()->add($item, null, 1);
            } catch (\Exception $e) {

            }
        }

        return $menu;
    }
}
