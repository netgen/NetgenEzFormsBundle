<?php

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\EmailAddress;
use eZ\Publish\SPI\FieldType\Value;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

class Email extends FieldTypeHandler
{
    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function convertFieldValueToForm(Value $value, FieldDefinition $fieldDefinition = null)
    {
        return $value->email;
    }

    /**
     * {@inheritdoc}
     *
     * @return \eZ\Publish\Core\FieldType\EmailAddress\Value
     */
    public function convertFieldValueFromForm($data)
    {
        return new EmailAddress\Value($data);
    }

    /**
     * {@inheritdoc}
     */
    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        $languageCode,
        Content $content = null
    ) {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode, $content);

        if (isset($fieldDefinition->validatorConfiguration['EmailAddressValidator'])) {
            $options['constraints'][] = new Constraints\Email();
        }

        $formBuilder->add($fieldDefinition->identifier, EmailType::class, $options);
    }
}
