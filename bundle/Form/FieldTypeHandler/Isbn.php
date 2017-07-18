<?php

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\ISBN\Value as IsbnValue;
use eZ\Publish\SPI\FieldType\Value;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

/**
 * Class Isbn.
 */
class Isbn extends FieldTypeHandler
{
    /**
     * {@inheritdoc}
     *
     * @param IsbnValue $value
     */
    public function convertFieldValueToForm(Value $value, FieldDefinition $fieldDefinition = null)
    {
        return $value->isbn;
    }

    /**
     * {@inheritdoc}
     */
    public function convertFieldValueFromForm($data)
    {
        if (empty($data)) {
            $data = '';
        }

        return new IsbnValue($data);
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

        if ($fieldDefinition->fieldSettings['isISBN13']) {
            $options['constraints'][] = new Constraints\Isbn(
                array(
                    'type' => 'isbn13',
                )
            );
        } else {
            $options['constraints'][] = new Constraints\Isbn();
        }

        $formBuilder->add($fieldDefinition->identifier, TextType::class, $options);
    }
}
