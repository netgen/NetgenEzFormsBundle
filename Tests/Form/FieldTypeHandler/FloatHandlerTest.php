<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\FieldTypeHandler;

use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\FloatHandler;
use eZ\Publish\Core\FieldType\Float as FloatValue;
use Symfony\Component\Form\FormBuilder;

class FloatTest extends \PHPUnit_Framework_TestCase
{
    public function testAssertInstanceOfFieldTypeHandler()
    {
        $float = new FloatHandler();

        $this->assertInstanceOf(FieldTypeHandler::class, $float);
    }

    public function testConvertFieldValueToForm()
    {
        $float = new FloatHandler();
        $floatValue = new FloatValue\Value(4.74);

        $returnedValue = $float->convertFieldValueToForm($floatValue);

        $this->assertEquals(4.74, $returnedValue);
    }

    public function testConvertFieldValueFromForm()
    {
        $float = new FloatHandler();
        $floatValue = new FloatValue\Value(4.74);

        $returnedValue = $float->convertFieldValueFromForm(4.74);

        $this->assertEquals($floatValue, $returnedValue);
    }

    public function testConvertFieldValueFromFormWhenDataIsNotNumeric()
    {
        $float = new FloatHandler();
        $floatValue = new FloatValue\Value(null);

        $returnedValue = $float->convertFieldValueFromForm('test');

        $this->assertEquals($floatValue, $returnedValue);
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
                'defaultValue' => new FloatValue\Value(4.74),
                'descriptions' => array('fre-FR' => 'fre-FR'),
                'names' => array('fre-FR' => 'fre-FR'),
                'validatorConfiguration' => array(
                    'FloatValueValidator' => array(
                        'minFloatValue' => 4,
                        'maxFloatValue' => 10,
                    )
                )
            )
        );

        $languageCode = 'eng-GB';

        $float = new FloatHandler();
        $float->buildFieldCreateForm($formBuilder, $fieldDefinition, $languageCode);
    }
}
