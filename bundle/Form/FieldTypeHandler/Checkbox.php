<?php

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\Checkbox as CheckboxValue;
use eZ\Publish\Core\Helper\FieldHelper;
use eZ\Publish\SPI\FieldType\Value;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class Checkbox extends FieldTypeHandler
{
    /**
     * @var FieldHelper
     */
    protected $fieldHelper;

    /**
     * Integer constructor.
     *
     * @param FieldHelper $fieldHelper
     */
    public function __construct(FieldHelper $fieldHelper)
    {
        $this->fieldHelper = $fieldHelper;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function convertFieldValueToForm(Value $value, FieldDefinition $fieldDefinition = null)
    {
        return $value->bool;
    }

    /**
     * {@inheritdoc}
     *
     * @return CheckboxValue\Value
     */
    public function convertFieldValueFromForm($data)
    {
        return new CheckboxValue\Value($data);
    }

    /**
     * {@inheritdoc}
     */
    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        $languageCode,
        Content $content = null
    ) {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode, $content);

        if ($fieldDefinition->defaultValue instanceof CheckboxValue\Value) {
            if (!$content instanceof Content) {
                $options['data'] = $fieldDefinition->defaultValue->bool;
            }
        }

        $formBuilder->add($fieldDefinition->identifier, CheckboxType::class, $options);
    }
}
