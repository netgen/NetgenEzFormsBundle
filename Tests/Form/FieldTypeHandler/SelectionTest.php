<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\FieldTypeHandler;

use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\Selection;
use eZ\Publish\Core\FieldType\Selection\Value as SelectionValue;
use Symfony\Component\Form\FormBuilder;

class SelectionTest extends \PHPUnit_Framework_TestCase
{
    public function testAssertInstanceOfFieldTypeHandler()
    {
        $selection = new Selection();

        $this->assertInstanceOf(FieldTypeHandler::class, $selection);
    }

    public function testConvertFieldValueToForm()
    {
        $selection = new Selection();
        $selectionValue = new SelectionValue(array(1));

        $returnedValue = $selection->convertFieldValueToForm($selectionValue);

        $this->assertEquals(array(1), $returnedValue);
    }

    public function testConvertFieldValueFromForm()
    {
        $selection = new Selection();
        $selectionValue = new SelectionValue(array(1));

        $returnedValue = $selection->convertFieldValueFromForm(1);

        $this->assertEquals($selectionValue, $returnedValue);
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
                'fieldSettings' => array(
                    'options' => array(
                        1 => 'Selection 1',
                        2 => 'Selection 2',
                    ),
                    'isMultiple' => true,
                )
            )
        );

        $languageCode = 'eng-GB';

        $selection = new Selection();
        $selection->buildFieldCreateForm($formBuilder, $fieldDefinition, $languageCode);
    }
}
