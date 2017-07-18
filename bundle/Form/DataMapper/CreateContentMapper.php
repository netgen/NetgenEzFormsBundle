<?php

namespace Netgen\Bundle\EzFormsBundle\Form\DataMapper;

use Netgen\Bundle\EzFormsBundle\Form\DataMapper;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use RuntimeException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

/**
 * Class CreateContentMapper.
 *
 * A data mapper using property paths to read/write data.
 */
class CreateContentMapper extends DataMapper
{
    /**
     * {@inheritdoc}
     */
    protected function mapToForm(FormInterface $form, DataWrapper $data, PropertyPathInterface $propertyPath)
    {
        /** @var $contentCreateStruct \eZ\Publish\Core\Repository\Values\Content\ContentCreateStruct */
        $contentCreateStruct = $data->payload;
        $contentType = $contentCreateStruct->contentType;

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
                $contentType->getFieldDefinition($fieldDefinitionIdentifier)->defaultValue,
                $fieldDefinition
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function mapFromForm(FormInterface $form, DataWrapper $data, PropertyPathInterface $propertyPath)
    {
        /** @var $contentCreateStruct \eZ\Publish\Core\Repository\Values\Content\ContentCreateStruct */
        $contentCreateStruct = $data->payload;
        $contentType = $contentCreateStruct->contentType;

        $fieldDefinitionIdentifier = (string) $propertyPath;
        $fieldDefinition = $contentType->getFieldDefinition($fieldDefinitionIdentifier);

        if (null === $fieldDefinition) {
            throw new RuntimeException(
                "Data payload does not contain expected FieldDefinition '{$fieldDefinitionIdentifier}'"
            );
        }

        $fieldTypeIdentifier = $fieldDefinition->fieldTypeIdentifier;

        $handler = $this->fieldTypeHandlerRegistry->get($fieldTypeIdentifier);
        $contentCreateStruct->setField(
            $fieldDefinitionIdentifier,
            $handler->convertFieldValueFromForm($form->getData())
        );
    }
}
