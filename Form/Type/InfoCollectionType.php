<?php

namespace Netgen\Bundle\EzFormsBundle\Form\Type;

use eZ\Publish\API\Repository\Values\Content\ContentCreateStruct;
use Symfony\Component\Form\FormBuilderInterface;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use RuntimeException;

class InfoCollectionType extends AbstractContentType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return "ezforms_info_collection";
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

        $contentCreateStruct = $dataWrapper->payload;

        if ( !$contentCreateStruct instanceof ContentCreateStruct )
        {
            throw new RuntimeException(
                "Data payload must be an instance of eZ\\Publish\\API\\Repository\\Values\\Content\\ContentCreateStruct"
            );
        }

        $builder->setDataMapper( $this->dataMapper );

        foreach ( $contentCreateStruct->contentType->getFieldDefinitions() as $fieldDefinition )
        {
            // Only add field that has info collector checked
            if ( !$fieldDefinition->isInfoCollector )
            {
                continue;
            }

            // Users can't be created through Content, if ezuser field is found we simply skip it
            if ( $fieldDefinition->fieldTypeIdentifier === "ezuser" )
            {
                continue;
            }

            $handler = $this->fieldTypeHandlerRegistry->get( $fieldDefinition->fieldTypeIdentifier );
            $handler->buildFieldCreateForm( $builder, $fieldDefinition, $contentCreateStruct->mainLanguageCode );
        }
    }
}

