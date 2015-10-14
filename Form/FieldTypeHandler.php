<?php

namespace Netgen\Bundle\EzFormsBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\SPI\FieldType\Value;
use RuntimeException;

/**
 * Class FieldTypeHandler
 *
 * @package Netgen\EzFormsBundle\FieldType
 */
abstract class FieldTypeHandler implements FieldTypeHandlerInterface
{
    protected $fieldTypeRegistry;

    public function __construct()
    {

    }

    /**
     * @inheritdoc
     */
    abstract public function convertFieldValueToForm( Value $value );

    /**
     * @inheritdoc
     */
    public function convertFieldValueFromForm( $data )
    {
        return $data;
    }

    /**
     * In most cases implementations of methods {@link self::buildCreateFieldForm()}
     * and {@link self::buildUpdateFieldForm()} will be the same, therefore default
     * handler implementation of those falls back to this method.
     *
     * Implement as needed.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $formBuilder
     * @param \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition $fieldDefinition
     * @param string $languageCode
     * @param null|\eZ\Publish\API\Repository\Values\Content\Content $content
     * @param boolean $readOnly
     *
     * @return void
     */
    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        $languageCode,
        Content $content = null,
        $readOnly = false
    )
    {
        throw new RuntimeException( "Not implemented." );
    }

    /**
     * Returns default field options, created from given $fieldDefinition and $languageCode
     *
     * @param \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition $fieldDefinition
     * @param string $languageCode
     * @param null|\eZ\Publish\API\Repository\Values\Content\Content $content
     *
     * @return array
     */
    protected function getDefaultFieldOptions(
        FieldDefinition $fieldDefinition,
        $languageCode,
        Content $content = null
    )
    {
        $options = array();

        $options["label"] = $fieldDefinition->getName( $languageCode );
        $options["required"] = $fieldDefinition->isRequired;
        $options["ezforms"]["description"] = $fieldDefinition->getDescription( $languageCode );
        $options["ezforms"]["language_code"] = $languageCode;
        $options["ezforms"]["fielddefinition"] = $fieldDefinition;

        if ( $content !== null )
        {
            $options["ezforms"]["content"] = $content;
        }

        $options["constraints"] = array();
        if ( $fieldDefinition->isRequired )
        {
            $options["constraints"][] = new Constraints\NotBlank();
        }

        return $options;
    }

    /**
     * @inheritdoc
     *
     * In most cases this will be the same as {@link self::buildUpdateFieldForm()}.
     * For this reason default implementation falls back to the internal method
     * {@link self::buildFieldForm()}, which should be implemented as needed.
     */
    public function buildFieldCreateForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        $languageCode
    )
    {
        $this->buildFieldForm( $formBuilder, $fieldDefinition, $languageCode );
    }

    /**
     * @inheritdoc
     *
     * In most cases this will be the same as {@link self::buildCreateFieldForm()}.
     * For this reason default implementation falls back to the internal method
     * {@link self::buildFieldForm()}, which should be implemented as needed.
     */
    public function buildFieldUpdateForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        Content $content,
        $languageCode,
        $readOnly
    )
    {
        $this->buildFieldForm( $formBuilder, $fieldDefinition, $languageCode, $content, $readOnly );
    }

    /**
     * Adds a hidden field to the from, indicating that empty value passed
     * for update should be ignored.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $formBuilder
     * @param string $fieldDefinitionIdentifier
     */
    protected function skipEmptyUpdate( FormBuilderInterface $formBuilder, $fieldDefinitionIdentifier )
    {
        $options = array(
            "mapped" => false,
            "data" => "yes",
        );

        $formBuilder->add(
            "ezforms_skip_empty_update_{$fieldDefinitionIdentifier}",
            "hidden",
            $options
        );
    }
}
