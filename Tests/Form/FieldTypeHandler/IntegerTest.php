<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\FieldTypeHandler;

use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\Integer;
use eZ\Publish\Core\FieldType\Integer\Value as IntegerValue;
use Symfony\Component\Form\FormBuilder;

class IntegerTest extends \PHPUnit_Framework_TestCase
{
    public function testAssertInstanceOfFieldTypeHandler()
    {
        $integer = new Integer();

        $this->assertInstanceOf(FieldTypeHandler::class, $integer);
    }

    public function testConvertFieldValueToForm()
    {
        $integer = new Integer();
        $integerValue = new IntegerValue(5);

        $returnedValue = $integer->convertFieldValueToForm($integerValue);

        $this->assertEquals(5, $returnedValue);
    }

    public function testConvertFieldValueFromForm()
    {
        $integer = new Integer();
        $integerValue = new IntegerValue(5);

        $returnedValue = $integer->convertFieldValueFromForm(5);

        $this->assertEquals($integerValue, $returnedValue);
    }

    public function testConvertFieldValueFromFormWhenDataIsNotNumeric()
    {
        $integer = new Integer();
        $integerValue = new IntegerValue(null);

        $returnedValue = $integer->convertFieldValueFromForm('test');

        $this->assertEquals($integerValue, $returnedValue);
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
                'defaultValue' => new IntegerValue(5),
                'descriptions' => array('fre-FR' => 'fre-FR'),
                'names' => array('fre-FR' => 'fre-FR'),
                'validatorConfiguration' => array(
                    'IntegerValueValidator' => array(
                        'minIntegerValue' => 4,
                        'maxIntegerValue' => 10,
                    )
                )
            )
        );

        $languageCode = 'eng-GB';

        $integer = new Integer();
        $integer->buildFieldCreateForm($formBuilder, $fieldDefinition, $languageCode);
    }
}
