<?php

namespace Netgen\Bundle\EzFormsBundle\Form;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\SPI\FieldType\Value;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Interface FieldTypeHandlerInterface.
 */
interface FieldTypeHandlerInterface
{
    /**
     * Converts the eZ Publish FieldType value to a format that can be accepted by the form.
     *
     * @see buildFieldCreateForm
     * @see buildFieldUpdateForm
     *
     * @param \eZ\Publish\SPI\FieldType\Value $value
     * @param \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition|null $fieldDefinition
     *
     * @return mixed
     */
    public function convertFieldValueToForm(Value $value, FieldDefinition $fieldDefinition = null);

    /**
     * Converts the form data to a format that can be accepted by eZ Publish FieldType.
     *
     * @see buildFieldCreateForm
     * @see buildFieldUpdateForm
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function convertFieldValueFromForm($data);

    /**
     * Builds the form the given $fieldDefinition and $languageCode for creating.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $formBuilder
     * @param \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition $fieldDefinition
     * @param string $languageCode
     */
    public function buildFieldCreateForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        $languageCode
    );

    /**
     * Builds the form the given $fieldDefinition and $languageCode for updating.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $formBuilder
     * @param \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition $fieldDefinition
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
     * @param string $languageCode
     */
    public function buildFieldUpdateForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        Content $content,
        $languageCode
    );
}
