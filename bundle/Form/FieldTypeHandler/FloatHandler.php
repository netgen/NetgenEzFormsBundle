<?php

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\Float as FloatValue;
use eZ\Publish\Core\Helper\FieldHelper;
use eZ\Publish\SPI\FieldType\Value;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class FloatHandler extends FieldTypeHandler
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
     * @return float
     */
    public function convertFieldValueToForm(Value $value, FieldDefinition $fieldDefinition = null)
    {
        return $value->value;
    }

    /**
     * {@inheritdoc}
     *
     * @return FloatValue\Value
     */
    public function convertFieldValueFromForm($data)
    {
        if (!is_numeric($data)) {
            $data = null;
        }

        return new FloatValue\Value($data);
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

        if (!empty($fieldDefinition->getValidatorConfiguration()['FloatValueValidator'])) {
            $rangeConstraints = array();

            $min = $fieldDefinition->getValidatorConfiguration()['FloatValueValidator']['minFloatValue'];
            $max = $fieldDefinition->getValidatorConfiguration()['FloatValueValidator']['maxFloatValue'];

            if ($min !== false) {
                $rangeConstraints['min'] = $min;
            }

            if ($max !== false) {
                $rangeConstraints['max'] = $max;
            }

            if (!empty($rangeConstraints)) {
                $options['constraints'][] = new Assert\Range($rangeConstraints);
            }
        }

        if ($fieldDefinition->defaultValue instanceof FloatValue\Value) {
            if (!$content instanceof Content) {
                $options['data'] = (float) $fieldDefinition->defaultValue->value;
            }
        }

        $formBuilder->add($fieldDefinition->identifier, NumberType::class, $options);
    }
}
