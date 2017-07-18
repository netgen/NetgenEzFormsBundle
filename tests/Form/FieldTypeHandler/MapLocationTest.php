<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\FieldTypeHandler;

use eZ\Publish\Core\FieldType\MapLocation\Value as MapLocationValue;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\MapLocation;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilder;

class MapLocationTest extends TestCase
{
    public function testAssertInstanceOfFieldTypeHandler()
    {
        $map = new MapLocation();

        $this->assertInstanceOf(FieldTypeHandler::class, $map);
    }

    public function testConvertFieldValueToForm()
    {
        $map = new MapLocation();
        $data = array('latitude' => 34, 'longitude' => 123, 'address' => null);
        $mapValue = new MapLocationValue($data);

        $returnedValue = $map->convertFieldValueToForm($mapValue);

        $this->assertEquals($data, $returnedValue);
    }

    public function testConvertFieldValueFromForm()
    {
        $map = new MapLocation();
        $data = array('latitude' => 34, 'longitude' => 123, 'address' => null);
        $mapValue = new MapLocationValue($data);

        $returnedValue = $map->convertFieldValueFromForm($data);

        $this->assertEquals($mapValue, $returnedValue);
    }

    public function testConvertFieldValueFromFormWhenDataIsNotArray()
    {
        $map = new MapLocation();

        $returnedValue = $map->convertFieldValueFromForm(null);

        $this->assertNull($returnedValue);
    }

    public function testBuildFieldCreateForm()
    {
        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('add'))
            ->getMock();

        $formBuilder->expects($this->once())
            ->method('add');

        $fieldDefinition = new FieldDefinition(
            array(
                'id' => 'id',
                'identifier' => 'identifier',
                'isRequired' => true,
                'descriptions' => array('fre-FR' => 'fre-FR'),
                'names' => array('fre-FR' => 'fre-FR'),
            )
        );

        $languageCode = 'eng-GB';

        $map = new MapLocation();
        $map->buildFieldCreateForm($formBuilder, $fieldDefinition, $languageCode);
    }
}
