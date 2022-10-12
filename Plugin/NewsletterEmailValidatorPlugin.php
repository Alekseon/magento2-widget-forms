<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace Alekseon\WidgetForms\Plugin;

use Alekseon\AlekseonEav\Model\Attribute\InputValidator\Email;
use Alekseon\AlekseonEav\Model\Attribute\InputValidator\EmailFactory;
use Alekseon\CustomFormsBuilder\Model\FormRecord\Attribute;
use Alekseon\CustomFormsBuilder\Model\FormRepository;

/**
 * Class NewsletterEmailValidatorPlugin
 * @package Alekseon\WidgetForms\Plugin
 */
class NewsletterEmailValidatorPlugin
{
    /**
     * @var FormRepository
     */
    protected $formRepository;
    /**
     * @var EmailFactory
     */
    protected $emailValidatorFactory;

    /**
     * NewsletterEmailValidatorPlugin constructor.
     * @param FormRepository $formRepository
     */
    public function __construct(
        FormRepository $formRepository,
        EmailFactory $emailValidatorFactory
    )
    {
        $this->formRepository = $formRepository;
        $this->emailValidatorFactory = $emailValidatorFactory;
    }

    /**
     * @param Attribute $attribute
     * @param $validator
     */
    public function afterGetInputValidators(Attribute $attribute, $validators)
    {
        $formId = $attribute->getFormId();
        $form = $this->formRepository->getById($formId);

        if ($form->getSubscribeToNewsletter() && $form->getNewsletterEmail() == $attribute->getAttributeCode()) {
            $emailValidator = $this->emailValidatorFactory->create();
            if (!isset($validators[$emailValidator->getCode()])) {
                $validators[] = $emailValidator;
            }
        }

        return $validators;
    }
}
