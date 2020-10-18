<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\WidgetForms\Block\Form\Field;

use Magento\Framework\View\Element\Template;

/**
 * Class AbstractField
 * @package Alekseon\WidgetForms\Block\Form\Field
 */
class AbstractField extends \Magento\Framework\View\Element\Template
{
    /**
     * @return bool
     */
    public function isRequired()
    {
        return (bool) $this->getField()->getIsRequired();
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->getField()->getFrontendLabel();
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->getField()->getAttributeCode();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return 'form_field' . $this->getForm()->getId() . '_' . $this->getField()->getAttributeCode();
    }

    /**
     * @return array
     */
    protected function getDataValidateParams()
    {
        $dataValidate = [];
        if ($this->isRequired()) {
            $dataValidate['required'] = true;
        }

        $inputValidator = $this->getField()->getInputValidator();
        if ($inputValidator) {
            $validateParams = $inputValidator->getDataValidateParams($this->getField());
            foreach ($validateParams as $key => $value) {
                $dataValidate[$key] = $value;
            }
        }

        return $dataValidate;
    }

    /**
     *
     */
    public function getDataValidateJson()
    {
       $dataValidate = $this->getDataValidateParams();
       return json_encode($dataValidate);
    }

    /**
     * @return mixed
     */
    public function getNote()
    {
       return $this->getField()->getNote();
    }

    /**
     *
     */
    public function getPlaceholder()
    {
        return '';
    }
}
