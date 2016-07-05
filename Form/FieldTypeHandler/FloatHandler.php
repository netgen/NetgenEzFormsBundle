<?php

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use eZ\Publish\SPI\FieldType\Value;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\FormBuilderInterface;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\FieldType\Float as FloatValue;
use Symfony\Component\Validator\Constraints as Assert;
use eZ\Publish\Core\Helper\FieldHelper;


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
        parent::__construct();

        $this->fieldHelper = $fieldHelper;
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
            if ($content instanceof Content) {
                if ($this->fieldHelper->isFieldEmpty($content, $fieldDefinition->identifier, $languageCode)) {
                    $options['data'] = (float)$fieldDefinition->defaultValue->value;
                }
            } else {
                $options['data'] = (float)$fieldDefinition->defaultValue->value;
            }
        }

        $formBuilder->add($fieldDefinition->identifier, 'number', $options);
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
}
