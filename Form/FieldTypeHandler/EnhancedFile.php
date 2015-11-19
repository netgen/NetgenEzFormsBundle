<?php

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\FormBuilderInterface;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\SPI\FieldType\Value;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class EnhancedFile
 *
 * @package Netgen\EzFormsBundle\FieldType\FormBuilder
 */
class EnhancedFile extends FieldTypeHandler
{

    /**
     * @param \eZ\Publish\Core\MVC\ConfigResolverInterface $configResolver
     */
    public function __construct( ConfigResolverInterface $configResolver )
    {
        $this->configResolver = $configResolver;
    }

    /**
     * {@inheritdoc}
     *
     * @param \eZ\Publish\Core\FieldType\Image\Value $value
     */
    public function convertFieldValueToForm( Value $value )
    {
        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @param UploadedFile $data
     */
    public function convertFieldValueFromForm( $data )
    {
        if ( $data === null )
        {
            return null;
        }

        return array(
            "inputUri" => $data->getFileInfo()->getRealPath(),
            "fileName" => $data->getClientOriginalName(),
            "fileSize" => $data->getSize(),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        $languageCode,
        Content $content = null
    )
    {
        $options = $this->getDefaultFieldOptions( $fieldDefinition, $languageCode, $content );

        $maxFileSize = $fieldDefinition->validatorConfiguration["FileSizeValidator"]["maxFileSize"];
        $allowedExtensions = $fieldDefinition->fieldSettings['allowedTypes'];

        if ( $maxFileSize !== false || !empty( $allowedExtensions ) )
        {
            $constraints = array();

            if ( $maxFileSize !== false )
            {
                $constraints['maxSize'] = $maxFileSize;
            }

            if ( !empty( $allowedExtensions ) )
            {
                $allowedExtensions = explode( '|', $allowedExtensions );

                $allowedMimeTypes = array();

                foreach( $allowedExtensions as $allowedExtension )
                {
                    if ( $this->configResolver->hasParameter( "{$allowedExtension}.Types", 'mime' ) )
                    {
                        $allowedMimeTypes = array_merge( $allowedMimeTypes, $this->configResolver->getParameter( "{$allowedExtension}.Types", 'mime' ) );
                    }
                }
                $constraints['mimeTypes'] = $allowedMimeTypes;
            }

            $options["constraints"][] = new Constraints\File( $constraints );
        }

        // Image should not be erased (updated as empty) if nothing is selected in file input
        $this->skipEmptyUpdate( $formBuilder, $fieldDefinition->identifier );

        //$options["block_name"] = "ezforms_image";

        $formBuilder->add( $fieldDefinition->identifier, "file", $options );
    }
}

