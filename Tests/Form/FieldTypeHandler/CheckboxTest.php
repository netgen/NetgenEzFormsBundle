<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\FieldTypeHandler;

use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\Checkbox;
use eZ\Publish\Core\FieldType\Checkbox as CheckboxValue;
use Symfony\Component\Form\FormBuilder;

class CheckboxTest extends \PHPUnit_Framework_TestCase
{
    public function testAssertInstanceOfFieldTypeHandler()
    {
        $checkbox = new Checkbox();

        $this->assertInstanceOf(FieldTypeHandler::class, $checkbox);
    }

    public function testConvertFieldValueToForm()
    {
        $checkbox = new Checkbox();
        $checkboxValue = new CheckboxValue\Value(true);

        $returnedBool = $checkbox->convertFieldValueToForm($checkboxValue);

        $this->assertTrue($returnedBool);
    }

    public function testConvertFieldValueFromForm()
    {
        $checkbox = new Checkbox();
        $checkboxValue = new CheckboxValue\Value(true);

        $returnedBool = $checkbox->convertFieldValueFromForm(true);

        $this->assertEquals($checkboxValue, $returnedBool);
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
                'defaultValue' => new CheckboxValue\Value(true),
                'isRequired' => true,
                'descriptions' => array('fre-FR' => 'fre-FR'),
                'names' => array('fre-FR' => 'fre-FR'),
            )
        );

        $languageCode = 'eng-GB';

        $checkbox = new Checkbox();
        $checkbox->buildFieldCreateForm($formBuilder, $fieldDefinition, $languageCode);
    }
}
