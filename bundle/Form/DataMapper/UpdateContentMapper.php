<?php

namespace Netgen\Bundle\EzFormsBundle\Form\DataMapper;

use Netgen\Bundle\EzFormsBundle\Form\DataMapper;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use RuntimeException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

/**
 * Class UpdateContentMapper.
 *
 * A data mapper using property paths to read/write data.
 */
class UpdateContentMapper extends DataMapper
{
    /**
     * {@inheritdoc}
     */
    protected function mapToForm(FormInterface $form, DataWrapper $data, PropertyPathInterface $propertyPath)
    {
        /** @var \eZ\Publish\API\Repository\Values\Content\ContentUpdateStruct $contentUpdateStruct */
        $contentUpdateStruct = $data->payload;
        /** @var \eZ\Publish\API\Repository\Values\Content\Content $content */
        $content = $data->target;
        /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType */
        $contentType = $data->definition;

        $fieldDefinitionIdentifier = (string) $propertyPath;
        $fieldDefinition = $contentType->getFieldDefinition($fieldDefinitionIdentifier);

        if (null === $fieldDefinition) {
            throw new RuntimeException(
                "Data payload does not contain expected FieldDefinition '{$fieldDefinitionIdentifier}'"
            );
        }

        $fieldTypeIdentifier = $fieldDefinition->fieldTypeIdentifier;

        $handler = $this->fieldTypeHandlerRegistry->get($fieldTypeIdentifier);
        $form->setData(
            $handler->convertFieldValueToForm(
                $content->getFieldValue(
                    $fieldDefinitionIdentifier,
                    $contentUpdateStruct->initialLanguageCode
                ),
                $fieldDefinition
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function mapFromForm(FormInterface $form, DataWrapper $data, PropertyPathInterface $propertyPath)
    {
        /** @var \eZ\Publish\API\Repository\Values\Content\ContentUpdateStruct $contentUpdateStruct */
        $contentUpdateStruct = $data->payload;
        /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType */
        $contentType = $data->definition;

        $fieldDefinitionIdentifier = (string) $propertyPath;
        $fieldDefinition = $contentType->getFieldDefinition($fieldDefinitionIdentifier);

        if (null === $fieldDefinition) {
            throw new RuntimeException(
                "Data payload does not contain expected FieldDefinition '{$fieldDefinitionIdentifier}'"
            );
        }

        $fieldTypeIdentifier = $fieldDefinition->fieldTypeIdentifier;
        $formData = $form->getData();

        // Set field to struct only if it is not empty or it has not been marked
        // to skip update if empty
        if (!$this->shouldSkipForEmptyUpdate($form, $formData, $fieldDefinitionIdentifier)) {
            $handler = $this->fieldTypeHandlerRegistry->get($fieldTypeIdentifier);
            $contentUpdateStruct->setField(
                $fieldDefinitionIdentifier,
                $handler->convertFieldValueFromForm($form->getData())
            );
        }
    }
}
