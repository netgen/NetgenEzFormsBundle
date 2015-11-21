<?php

namespace Netgen\Bundle\EzFormsBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use Netgen\Bundle\EzFormsBundle\API\Repository\Values\InformationCollection\InformationCollection;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use RuntimeException;

/**
 * Class InfoCollectionType
 * @package Netgen\Bundle\EzFormsBundle\Form\Type
 */
class InfoCollectionType extends AbstractContentType
{
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return "ezforms_information_collection";
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        /** @var DataWrapper $dataWrapper */
        $dataWrapper = $options["data"];

        if ( !$dataWrapper instanceof DataWrapper )
        {
            throw new RuntimeException(
                "Data must be an instance of Netgen\\EzFormsBundle\\Form\\DataWrapper"
            );
        }

        /** @var InformationCollection $payload */
        $payload = $dataWrapper->payload;

        if ( !$payload instanceof InformationCollection )
        {
            throw new RuntimeException(
                "Data payload must be an instance of Netgen\\Bundle\\EzFormsBundle\\API\\Repository\\Values\\InformationCollection\\InformationCollection"
            );
        }

        /** @var ContentType $contentType */
        $contentType = $dataWrapper->definition;

        if ( !$contentType instanceof ContentType )
        {
            throw new RuntimeException(
                "Data definition must be an instance of eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentType"
            );
        }

        $builder->setDataMapper( $this->dataMapper );

        foreach ( $contentType->getFieldDefinitions() as $fieldDefinition )
        {
            if ( $fieldDefinition->fieldTypeIdentifier === "ezuser" )
            {
                continue;
            }

            if ( !$fieldDefinition->isInfoCollector )
            {
                continue;
            }

            $handler = $this->fieldTypeHandlerRegistry->get( $fieldDefinition->fieldTypeIdentifier );
            $handler->buildFieldCreateForm( $builder, $fieldDefinition, $contentType->mainLanguageCode );
        }
    }
}
