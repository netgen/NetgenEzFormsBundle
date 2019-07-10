<?php

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\SPI\FieldType\Value;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\Type\FieldType\UserCreateType;
use Netgen\Bundle\EzFormsBundle\Form\Type\FieldType\UserUpdateType;
use Symfony\Component\Form\FormBuilderInterface;

class User extends FieldTypeHandler
{
    public function convertFieldValueToForm(Value $value, ?FieldDefinition $fieldDefinition = null): void
    {
        // Returning nothing here because user data is mapped in mapper as an exceptional case
    }

    public function buildFieldCreateForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        string $languageCode
    ): void {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode);

        $formBuilder->add($fieldDefinition->identifier, UserCreateType::class, $options);
    }

    public function buildFieldUpdateForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        Content $content,
        string $languageCode
    ): void {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode, $content);

        $formBuilder->add($fieldDefinition->identifier, UserUpdateType::class, $options);
    }
}
