<?php

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\SPI\FieldType\Value;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\Type\FieldType\UserCreateType;
use Netgen\Bundle\EzFormsBundle\Form\Type\FieldType\UserUpdateType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class User.
 */
class User extends FieldTypeHandler
{
    /**
     * {@inheritdoc}
     */
    public function convertFieldValueToForm(Value $value, FieldDefinition $fieldDefinition = null)
    {
        // Returning null here because user data is mapped in mapper as an exceptional case
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function buildFieldCreateForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        $languageCode
    ) {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode);

        $formBuilder->add($fieldDefinition->identifier, UserCreateType::class, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function buildFieldUpdateForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        Content $content,
        $languageCode
    ) {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode, $content);

        $formBuilder->add($fieldDefinition->identifier, UserUpdateType::class, $options);
    }
}
