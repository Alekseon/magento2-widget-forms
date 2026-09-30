<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
declare(strict_types=1);

namespace Alekseon\WidgetForms\Plugin;

use \Alekseon\AlekseonEav\Api\Data\AttributeInterface;

class FromAttributeGroupCodesPlugin
{
    /**
     * @var \Alekseon\CustomFormsBuilder\Model\FormRepository
     */
    private $formRepository;

    /**
     * FromAttributeGroupCodesPlugin constructor.
     * @param \Alekseon\CustomFormsBuilder\Model\FormRepository $formRepository
     */
    public function __construct(
        \Alekseon\CustomFormsBuilder\Model\FormRepository $formRepository
    ) {
        $this->formRepository = $formRepository;
    }

    /**
     * @param AttributeInterface $attribute
     * @param bool $result
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetCanUseGroup($attribute, $result)
    {
        $form = $this->formRepository->getById($attribute->getFormId());
        return $form->getCanUseForWidget() || $result;
    }

    /**
     * @param AttributeInterface $attribute
     * @param bool $result
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
