<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\FieldTypeHandler;

use eZ\Publish\Core\FieldType\Selection\Value as SelectionValue;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\Selection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilder;

class SelectionTest extends TestCase
{
    /**
     * @var Selection
     */
    protected $handler;

    public function setUp()
    {
        $this->handler = new Selection();
    }

    public function testAssertInstanceOfFieldTypeHandler()
    {
        $this->assertInstanceOf(FieldTypeHandler::class, $this->handler);
    }

    public function testConvertFieldValueToFormWithIdentifiersArrayEmpty()
    {
        $identifiers = array();
        $selection = new SelectionValue($identifiers);
        $fieldDefinition = new FieldDefinition(
            array(
                'fieldSettings' => array(
                    'isMultiple' => false,
                ),
            )
        );

        $converted = $this->handler->convertFieldValueToForm($selection, $fieldDefinition);

        $this->assertEquals('', $converted);
    }

    public function testConvertFieldValueToFormWithFieldDefinitionMultiple()
    {
        $identifiers = array('identifier1', 'identifier2');
        $selection = new SelectionValue($identifiers);
        $fieldDefinition = new FieldDefinition(
            array(
                'fieldSettings' => array(
                    'isMultiple' => true,
                ),
            )
        );

        $converted = $this->handler->convertFieldValueToForm($selection, $fieldDefinition);

        $this->assertEquals($identifiers, $converted);
    }

    public function testConvertFieldValueToFormWithFieldDefinitionSingle()
    {
        $identifiers = array('identifier1', 'identifier2');
        $selection = new SelectionValue($identifiers);
        $fieldDefinition = new FieldDefinition(
            array(
                'fieldSettings' => array(
                    'isMultiple' => false,
                ),
            )
        );

        $converted = $this->handler->convertFieldValueToForm($selection, $fieldDefinition);

        $this->assertEquals($identifiers[0], $converted);
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
                ),
            )
        );

        $languageCode = 'eng-GB';

        $selection = new Selection();
        $selection->buildFieldCreateForm($formBuilder, $fieldDefinition, $languageCode);
    }
}
