<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\FieldTypeHandler;

use eZ\Publish\Core\FieldType\MapLocation\Value as MapLocationValue;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\MapLocation;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilder;

final class MapLocationTest extends TestCase
{
    public function testAssertInstanceOfFieldTypeHandler(): void
    {
        $map = new MapLocation();

        self::assertInstanceOf(FieldTypeHandler::class, $map);
    }

    public function testConvertFieldValueToForm(): void
    {
        $map = new MapLocation();
        $data = ['latitude' => 34, 'longitude' => 123, 'address' => null];
        $mapValue = new MapLocationValue($data);

        $returnedValue = $map->convertFieldValueToForm($mapValue);

        self::assertSame($data, $returnedValue);
    }

    public function testConvertFieldValueFromForm(): void
    {
        $map = new MapLocation();
        $data = ['latitude' => 34, 'longitude' => 123, 'address' => null];
        $mapValue = new MapLocationValue($data);

        $returnedValue = $map->convertFieldValueFromForm($data);

        self::assertSame($mapValue->latitude, $returnedValue->latitude);
        self::assertSame($mapValue->longitude, $returnedValue->longitude);
        self::assertSame($mapValue->address, $returnedValue->address);
    }

    public function testConvertFieldValueFromFormWhenDataIsNotArray(): void
    {
        $map = new MapLocation();

        $returnedValue = $map->convertFieldValueFromForm(null);

        self::assertNull($returnedValue);
    }

    public function testBuildFieldCreateForm(): void
    {
        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['add'])
            ->getMock();

        $formBuilder->expects(self::once())
            ->method('add');

        $fieldDefinition = new FieldDefinition(
            [
                'id' => 'id',
                'identifier' => 'identifier',
                'isRequired' => true,
                'descriptions' => ['fre-FR' => 'fre-FR'],
                'names' => ['fre-FR' => 'fre-FR'],
            ]
        );

        $languageCode = 'eng-GB';

        $map = new MapLocation();
        $map->buildFieldCreateForm($formBuilder, $fieldDefinition, $languageCode);
    }
}
