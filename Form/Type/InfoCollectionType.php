<?php

namespace Netgen\Bundle\EzFormsBundle\Form\Type;

use Netgen\Bundle\EzFormsBundle\Model\InformationCollectionInterface;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use Symfony\Component\Form\FormBuilderInterface;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use RuntimeException;

/**
 * Class InfoCollectionType
 * @package Netgen\Bundle\EzFormsBundle\Form\Type
 */
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

        /** @var InformationCollectionInterface $model */
        $model = $dataWrapper->payload;

        if ( !$model instanceof InformationCollectionInterface )
        {
            throw new RuntimeException(
                "Data payload must be an instance of Netgen\\EzFormsBundle\\Model\\InformationCollectionInterface"
            );
        }

        /** @var ContentType $contentType */
        $contentType = $dataWrapper->definition;

        if ( !$contentType instanceof ContentType )
        {
            throw new RuntimeException(
                "Data definition must be an instance of eZ\\Publish\\API\\Repository\\Values\\ContentType"
            );
        }

        $builder->setDataMapper( $this->dataMapper );

        foreach ( $contentType->getFieldDefinitions() as $fieldDefinition )
        {
            // Users can't be used as Information collectors
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

        // Intentionally omitting submit buttons, set them manually as needed
    }
}
