<?php

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\MapLocation as MapLocationValue;
use eZ\Publish\SPI\FieldType\Value;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\Type\MapType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class MapLocation.
 */
class MapLocation extends FieldTypeHandler
{
    /**
     * {@inheritdoc}
     */
    public function convertFieldValueToForm(Value $value, FieldDefinition $fieldDefinition = null)
    {
        return array(
            'latitude' => empty($value->latitude) ? null : $value->latitude,
            'longitude' => empty($value->longitude) ? null : $value->longitude,
            'address' => empty($value->address) ? null : $value->address,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function convertFieldValueFromForm($data)
    {
        if (!is_array($data)) {
            return null;
        }

        return new MapLocationValue\Value(
            array(
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'address' => $data['address'],
            )
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
    ) {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode, $content);

        $options['block_name'] = 'ezforms_map';

        $formBuilder->add($fieldDefinition->identifier, MapType::class, $options);
    }
}
