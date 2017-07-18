<?php

namespace Netgen\Bundle\EzFormsBundle\Form\DataMapper;

use Netgen\Bundle\EzFormsBundle\Form\DataMapper;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use RuntimeException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

/**
 * Class UpdateUserMapper.
 *
 * A data mapper using property paths to read/write data.
 */
class UpdateUserMapper extends DataMapper
{
    /**
     * {@inheritdoc}
     */
    protected function mapToForm(FormInterface $form, DataWrapper $data, PropertyPathInterface $propertyPath)
    {
        /** @var $userUpdateStruct \eZ\Publish\API\Repository\Values\User\UserUpdateStruct */
        $userUpdateStruct = $data->payload;
        /** @var $user \eZ\Publish\API\Repository\Values\User\User */
        $user = $data->target;
        /** @var $contentType \eZ\Publish\API\Repository\Values\ContentType\ContentType */
        $contentType = $data->definition;

        $fieldDefinitionIdentifier = (string) $propertyPath;
        $fieldDefinition = $contentType->getFieldDefinition($fieldDefinitionIdentifier);

        if (null === $fieldDefinition) {
            throw new RuntimeException(
                "Data payload does not contain expected FieldDefinition '{$fieldDefinitionIdentifier}'"
            );
        }

        $fieldTypeIdentifier = $fieldDefinition->fieldTypeIdentifier;

        // For user we can update only email and password.
        // Only email is set as it doesn't make sense to set the password to the form.
        if ($fieldTypeIdentifier === 'ezuser') {
            $form->setData(
                array(
                    'email' => $user->email,
                )
            );
        } else {
            $handler = $this->fieldTypeHandlerRegistry->get($fieldTypeIdentifier);
            $form->setData(
                $handler->convertFieldValueToForm(
                    $user->getFieldValue(
                        $fieldDefinitionIdentifier,
                        $userUpdateStruct->contentUpdateStruct->initialLanguageCode
                    ),
                    $fieldDefinition
                )
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function mapFromForm(FormInterface $form, DataWrapper $data, PropertyPathInterface $propertyPath)
    {
        /** @var $userUpdateStruct \eZ\Publish\API\Repository\Values\User\UserUpdateStruct */
        $userUpdateStruct = $data->payload;
        /** @var $contentType \eZ\Publish\API\Repository\Values\ContentType\ContentType */
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
        $formData = $form->getData();

        // For ezuser we need to map form data to the properties in the UserUpdateStruct
        if ($fieldTypeIdentifier === 'ezuser') {
            $convertedData = $handler->convertFieldValueFromForm($formData);

            $userUpdateStruct->email = $convertedData['email'];
            $userUpdateStruct->password = $convertedData['password'];

            // Creating users through Content context is not allowed,
            // so we map dummy data to make it non-empty
            // This will be ignored during actual user update
            // @todo this should be improved on eZ side
            $userUpdateStruct->contentUpdateStruct->setField(
                $fieldDefinitionIdentifier,
                array(
                    'login' => 'dummy',
                )
            );
        }
        // Else set field to struct, but only if it is not empty or it has not been marked
        // to skip update if empty
        elseif (!$this->shouldSkipForEmptyUpdate($form, $formData, $fieldDefinitionIdentifier)) {
            $convertedData = $handler->convertFieldValueFromForm($formData);
            $userUpdateStruct->contentUpdateStruct->setField($fieldDefinitionIdentifier, $convertedData);
        }
    }
}
