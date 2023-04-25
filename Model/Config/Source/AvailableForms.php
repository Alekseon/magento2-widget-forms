<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\WidgetForms\Model\Config\Source;

/**
 * Class AvailableForms
 * @package Alekseon\WidgetForms\Model\Config\Source
 */
class AvailableForms implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var
     */
    protected $options;
    /**
     * @var \Alekseon\WidgetForms\Model\ResourceModel\Form\CollectionFactory
     */
    protected $formCollectionFactory;

    /**
     * AvailableForms constructor.
     * @param \Alekseon\CustomFormsBuilder\Model\ResourceModel\Form\CollectionFactory $formCollectionFactory
     */
    public function __construct(
        \Alekseon\CustomFormsBuilder\Model\ResourceModel\Form\CollectionFactory $formCollectionFactory
    )
    {
        $this->formCollectionFactory = $formCollectionFactory;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function toOptionArray()
    {
        $optionArray = [];
        $options = $this->toArray();
        foreach ($options as $optionId => $optionLabel)
        {
            $optionArray[$optionId] = $optionLabel;
        }
        return $optionArray;
    }

    /**
     * @return array|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function toArray()
    {
        if ($this->options === null) {
            $this->options = [];
            $formCollection = $this->formCollectionFactory->create();
            $formCollection->addAttributeToSelect('title');
            $formCollection->addAttributeToFilter('can_use_for_widget', true);

            foreach ($formCollection as $form) {
                $this->options[$form->getId()] = $form->getTitle();
            }
        }
        return $this->options;
    }
}
