<?php

namespace Netgen\Bundle\EzFormsBundle\Form\DataMapper;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyPathInterface;
use Netgen\Bundle\EzFormsBundle\Form\DataMapper;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use Netgen\Bundle\EzFormsBundle\Model\InformationCollectionInterface;
use RuntimeException;

/**
 * Class InfoCollectionMapper
 * @package Netgen\Bundle\EzFormsBundle\Form\DataMapper
 */
class InfoCollectionMapper extends DataMapper
{
    /**
     * {@inheritdoc}
     */
    protected function mapToForm( FormInterface $form, DataWrapper $data, PropertyPathInterface $propertyPath )
    {
        /** @var ContentType $contentType */
        $contentType = $data->definition;

        $fieldDefinitionIdentifier = (string)$propertyPath;
        $fieldDefinition = $contentType->getFieldDefinition( $fieldDefinitionIdentifier );

        if ( null === $fieldDefinition )
        {
            throw new RuntimeException(
                "Data definition does not contain expected FieldDefinition '{$fieldDefinitionIdentifier}'"
            );
        }

        $fieldTypeIdentifier = $fieldDefinition->fieldTypeIdentifier;

        $handler = $this->fieldTypeHandlerRegistry->get( $fieldTypeIdentifier );
        $form->setData(
            $handler->convertFieldValueToForm(
                $contentType->getFieldDefinition( $fieldDefinitionIdentifier )->defaultValue
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function mapFromForm( FormInterface $form, DataWrapper $data, PropertyPathInterface $propertyPath )
    {
        /** @var InformationCollectionInterface $model */
        $model = $data->payload;
        /** @var ContentType $contentType */
        $contentType = $data->definition;

        $fieldDefinitionIdentifier = (string)$propertyPath;
        $fieldDefinition = $contentType->getFieldDefinition( $fieldDefinitionIdentifier );

        if ( null === $fieldDefinition )
        {
            throw new RuntimeException(
                "Data definition does not contain expected FieldDefinition '{$fieldDefinitionIdentifier}'"
            );
        }

        $fieldTypeIdentifier = $fieldDefinition->fieldTypeIdentifier;

        $handler = $this->fieldTypeHandlerRegistry->get( $fieldTypeIdentifier );
        $model->setField(
            $fieldDefinitionIdentifier,
            $handler->convertFieldValueFromForm( $form->getData() )
        );
    }
}
