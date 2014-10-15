<?php

namespace Netgen\Bundle\EzFormsBundle\Form\Type;

use eZ\Publish\API\Repository\Values\User\UserCreateStruct;
use Symfony\Component\Form\FormBuilderInterface;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use RuntimeException;

/**
 * Class EzPublishCreateUserType
 *
 * @package Netgen\EzFormsBundle\Form\Type
 */
class CreateUserType extends AbstractContentType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return "ezforms_create_user";
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        /** @var $dataWrapper \Netgen\Bundle\EzFormsBundle\Form\DataWrapper */
        $dataWrapper = $options["data"];

        if ( !$dataWrapper instanceof DataWrapper )
        {
            throw new RuntimeException(
                "Data must be an instance of Netgen\\EzFormsBundle\\Form\\DataWrapper"
            );
        }

        $userCreateStruct = $dataWrapper->payload;

        if ( !$userCreateStruct instanceof UserCreateStruct )
        {
            throw new RuntimeException(
                "Data payload must be an instance of eZ\\Publish\\API\\Repository\\Values\\User\\UserCreateStruct"
            );
        }

        $builder->setDataMapper( $this->dataMapper );

        foreach ( $userCreateStruct->contentType->getFieldDefinitions() as $fieldDefinition )
        {
            $handler = $this->fieldTypeHandlerRegistry->get( $fieldDefinition->fieldTypeIdentifier );
            $handler->buildFieldCreateForm( $builder, $fieldDefinition, $userCreateStruct->mainLanguageCode );
        }

        // Intentionally omitting submit buttons, set them manually as needed
    }
}
