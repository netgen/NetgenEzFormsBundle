<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\FieldTypeHandler;

use eZ\Publish\Core\FieldType\TextBlock\Value as TextBlockValue;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\TextBlock;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilder;

class TextBlockTest extends TestCase
{
    public function testAssertInstanceOfFieldTypeHandler()
    {
        $text = new TextBlock();

        $this->assertInstanceOf(FieldTypeHandler::class, $text);
    }

    public function testConvertFieldValueToForm()
    {
        $text = new TextBlock();
        $textValue = new TextBlockValue('Some text');

        $returnedValue = $text->convertFieldValueToForm($textValue);

        $this->assertEquals('Some text', $returnedValue);
    }

    public function testConvertFieldValueFromForm()
    {
        $text = new TextBlock();
        $textValue = new TextBlockValue('Some text');

        $returnedValue = $text->convertFieldValueFromForm('Some text');

        $this->assertEquals($textValue, $returnedValue);
    }

    public function testConvertFieldValueFromFormWhenDataIsEmpty()
    {
        $text = new TextBlock();
        $textValue = new TextBlockValue('');

        $returnedValue = $text->convertFieldValueFromForm('');

        $this->assertEquals($textValue, $returnedValue);
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
                    'textRows' => 4,
                ),
            )
        );

        $languageCode = 'eng-GB';

        $text = new TextBlock();
        $text->buildFieldCreateForm($formBuilder, $fieldDefinition, $languageCode);
    }
}
