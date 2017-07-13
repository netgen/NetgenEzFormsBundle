<?php

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use eZ\Publish\SPI\FieldType\Value;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\FormBuilderInterface;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\FieldType\Integer as IntegerValue;
use Symfony\Component\Validator\Constraints as Assert;
use eZ\Publish\Core\Helper\FieldHelper;

class IntegerHandler extends FieldTypeHandler
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
     */
    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        $languageCode,
        Content $content = null
    ) {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode, $content);

        if ($fieldDefinition->defaultValue instanceof IntegerValue\Value) {
            if (!$content instanceof Content) {
                $options['data'] = (int)$fieldDefinition->defaultValue->value;
            }
        }

        if (!empty($fieldDefinition->getValidatorConfiguration()['IntegerValueValidator'])) {
            $rangeConstraints = array();

            $min = $fieldDefinition->getValidatorConfiguration()['IntegerValueValidator']['minIntegerValue'];
            $max = $fieldDefinition->getValidatorConfiguration()['IntegerValueValidator']['maxIntegerValue'];

            if ($min !== null) {
                $rangeConstraints['min'] = $min;
            }

            if ($max !== null) {
                $rangeConstraints['max'] = $max;
            }

            if (!empty($rangeConstraints)) {
                $options['constraints'][] = new Assert\Range($rangeConstraints);
            }
        }

        $formBuilder->add($fieldDefinition->identifier, 'integer', $options);
    }

    /**
     * {@inheritdoc}
     *
     * @return int
     */
    public function convertFieldValueToForm(Value $value, FieldDefinition $fieldDefinition = null)
    {
        return (int)$value->value;
    }

    /**
     * {@inheritdoc}
     *
     * @return IntegerValue\Value
     */
    public function convertFieldValueFromForm($data)
    {
        if (!is_int($data)) {
            $data = null;
        }

        return new IntegerValue\Value($data);
    }
}
