<?php

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\Url as UrlValue;
use eZ\Publish\SPI\FieldType\Value;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;

class Url extends FieldTypeHandler
{
    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function convertFieldValueToForm(Value $value, FieldDefinition $fieldDefinition = null)
    {
        return array('url' => $value->link, 'text' => $value->text);
    }

    /**
     * {@inheritdoc}
     *
     * @return UrlValue\Value
     */
    public function convertFieldValueFromForm($data)
    {
        if (!is_array($data)) {
            $data = array();
            $data['url'] = null;
            $data['text'] = null;
        }

        return new UrlValue\Value($data['url'], $data['text']);
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

        $formBuilder->add($fieldDefinition->identifier, UrlType::class, $options);
    }
}
