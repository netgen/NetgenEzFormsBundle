<?php

namespace Netgen\Bundle\EzFormsBundle\Form\DataMapper;

use Netgen\Bundle\EzFormsBundle\Form\DataMapper;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use RuntimeException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

/**
 * Class CreateUserMapper.
 *
 * A data mapper using property paths to read/write data.
 */
class CreateUserMapper extends DataMapper
{
    /**
     * {@inheritdoc}
     */
    protected function mapToForm(FormInterface $form, DataWrapper $data, PropertyPathInterface $propertyPath)
    {
        /** @var $userCreateStruct \eZ\Publish\Core\Repository\Values\User\UserCreateStruct */
        $userCreateStruct = $data->payload;
        $contentType = $userCreateStruct->contentType;

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
        /** @var $userCreateStruct \eZ\Publish\API\Repository\Values\User\UserCreateStruct */
        $userCreateStruct = $data->payload;
        $contentType = $userCreateStruct->contentType;

        $fieldDefinitionIdentifier = (string) $propertyPath;
        $fieldDefinition = $contentType->getFieldDefinition($fieldDefinitionIdentifier);

        if (null === $fieldDefinition) {
            throw new RuntimeException(
                "Data payload does not contain expected FieldDefinition '{$fieldDefinitionIdentifier}'"
            );
        }

        $fieldTypeIdentifier = $fieldDefinition->fieldTypeIdentifier;

        $handler = $this->fieldTypeHandlerRegistry->get($fieldTypeIdentifier);
        $convertedData = $handler->convertFieldValueFromForm($form->getData());

        // 'ezuser' is an exceptional case, here we need to map form data to the properties
        // in the UserCreateStruct
        if ($fieldTypeIdentifier === 'ezuser') {
            $userCreateStruct->login = $convertedData['username'];
            $userCreateStruct->email = $convertedData['email'];
            $userCreateStruct->password = isset($convertedData['password']) ? $convertedData['password'] : null;
        } else {
            $userCreateStruct->setField($fieldDefinitionIdentifier, $convertedData);
        }
    }
}
