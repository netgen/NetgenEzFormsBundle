<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\MapLocation as MapLocationValue;
use eZ\Publish\SPI\FieldType\Value;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\Type\MapType;
use Symfony\Component\Form\FormBuilderInterface;
use function is_array;

final class MapLocation extends FieldTypeHandler
{
    public function convertFieldValueToForm(Value $value, ?FieldDefinition $fieldDefinition = null): array
    {
        return [
            'latitude' => empty($value->latitude) ? null : $value->latitude,
            'longitude' => empty($value->longitude) ? null : $value->longitude,
            'address' => empty($value->address) ? null : $value->address,
        ];
    }

    public function convertFieldValueFromForm($data): ?MapLocationValue\Value
    {
        if (!is_array($data)) {
            return null;
        }

        return new MapLocationValue\Value(
            [
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'address' => $data['address'],
            ]
        );
    }

    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        string $languageCode,
        ?Content $content = null
    ): void {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode, $content);

        $options['block_name'] = 'ezforms_map';

        $formBuilder->add($fieldDefinition->identifier, MapType::class, $options);
    }
}
