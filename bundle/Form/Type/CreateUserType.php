<?php

namespace Netgen\Bundle\EzFormsBundle\Form\Type;

use eZ\Publish\API\Repository\Values\User\UserCreateStruct;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use RuntimeException;
use Symfony\Component\Form\FormBuilderInterface;

class CreateUserType extends AbstractContentType
{
    public function getBlockPrefix(): string
    {
        return 'ezforms_create_user';
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

        $userCreateStruct = $dataWrapper->payload;

        if (!$userCreateStruct instanceof UserCreateStruct) {
            throw new RuntimeException(
                'Data payload must be an instance of eZ\\Publish\\API\\Repository\\Values\\User\\UserCreateStruct'
            );
        }

        $builder->setDataMapper($this->dataMapper);

        foreach ($userCreateStruct->contentType->getFieldDefinitions() as $fieldDefinition) {
            $handler = $this->fieldTypeHandlerRegistry->get($fieldDefinition->fieldTypeIdentifier);
            $handler->buildFieldCreateForm($builder, $fieldDefinition, $userCreateStruct->mainLanguageCode);
        }

        // Intentionally omitting submit buttons, set them manually as needed
    }
}
