<?php

namespace Netgen\Bundle\EzFormsBundle\Form\Type;

use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\User\User;
use eZ\Publish\API\Repository\Values\User\UserUpdateStruct;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use RuntimeException;
use Symfony\Component\Form\FormBuilderInterface;

class UpdateUserType extends AbstractContentType
{
    public function getBlockPrefix(): string
    {
        return 'ezforms_update_user';
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var \Netgen\Bundle\EzFormsBundle\Form\DataWrapper $dataWrapper */
        $dataWrapper = $options['data'];

        if (!$dataWrapper instanceof DataWrapper) {
            throw new RuntimeException(
                'Data must be an instance of Netgen\\EzFormsBundle\\Form\\DataWrapper'
            );
        }

        $user = $dataWrapper->target;

        if (!$user instanceof User) {
            throw new RuntimeException(
                'Data payload must be an instance of eZ\\Publish\\API\\Repository\\Values\\User\\User'
            );
        }

        $userUpdateStruct = $dataWrapper->payload;

        if (!$userUpdateStruct instanceof UserUpdateStruct) {
            throw new RuntimeException(
                'Data payload must be an instance of eZ\\Publish\\API\\Repository\\Values\\User\\UserUpdateStruct'
            );
        }

        $contentType = $dataWrapper->definition;

        if (!$contentType instanceof ContentType) {
            throw new RuntimeException(
                'Data definition must be an instance of eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentType'
            );
        }

        if ($user->contentInfo->contentTypeId !== $contentType->id) {
            throw new RuntimeException(
                'Data definition (ContentType) does not correspond to the data target (Content)'
            );
        }

        $builder->setDataMapper($this->dataMapper);

        foreach ($contentType->getFieldDefinitions() as $fieldDefinition) {
            $languageCode = $userUpdateStruct->contentUpdateStruct->initialLanguageCode;
            $handler = $this->fieldTypeHandlerRegistry->get($fieldDefinition->fieldTypeIdentifier);

            $handler->buildFieldUpdateForm($builder, $fieldDefinition, $user, $languageCode);
        }

        // Intentionally omitting submit buttons, set them manually as needed
    }
}
