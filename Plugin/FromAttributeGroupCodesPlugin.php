<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\WidgetForms\Plugin;

/**
 * Class FromAttributeGroupCodesPlugin
 * @package Alekseon\WidgetForms\Plugin
 */
class FromAttributeGroupCodesPlugin
{
    /**
     * @var \Alekseon\CustomFormsBuilder\Model\FormRepository
     */
    protected $formRepository;

    /**
     * FromAttributeGroupCodesPlugin constructor.
     * @param \Alekseon\CustomFormsBuilder\Model\FormRepository $formRepository
     */
    public function __construct(
        \Alekseon\CustomFormsBuilder\Model\FormRepository $formRepository
    )
    {
        $this->formRepository = $formRepository;
    }

    /**
     * @param $attribute
     * @param $result
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetCanUseGroup($attribute, $result)
    {
        $form = $this->formRepository->getById($attribute->getFormId());
        return $form->getCanUseForWidget() || $result;
    }

    /**
     * @param $attribute
     * @param $result
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetIsGroupEditable($attribute, $result)
    {
        $form = $this->formRepository->getById($attribute->getFormId());
        if ($form->getCanUseForWidget()) {
            return false;
        }
        return $result;
    }
}
