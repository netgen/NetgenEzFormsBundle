<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\FieldTypeHandler;

use eZ\Publish\Core\FieldType\TextLine\Value as TextLineValue;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\TextLine;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilder;

class TextLineTest extends TestCase
{
    public function testAssertInstanceOfFieldTypeHandler()
    {
        $text = new TextLine();

        $this->assertInstanceOf(FieldTypeHandler::class, $text);
    }

    public function testConvertFieldValueToForm()
    {
        $text = new TextLine();
        $textValue = new TextLineValue('Some text');

        $returnedValue = $text->convertFieldValueToForm($textValue);

        $this->assertEquals('Some text', $returnedValue);
    }

    public function testConvertFieldValueFromForm()
    {
        $text = new TextLine();
        $textValue = new TextLineValue('Some text');

        $returnedValue = $text->convertFieldValueFromForm('Some text');

        $this->assertEquals($textValue, $returnedValue);
    }

    public function testConvertFieldValueFromFormWhenDataIsEmpty()
    {
        $text = new TextLine();
        $textValue = new TextLineValue('');

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
                'validatorConfiguration' => array(
                    'StringLengthValidator' => array(
                        'minStringLength' => 4,
                        'maxStringLength' => 100,
                    ),
                ),
            )
        );

        $languageCode = 'eng-GB';

        $text = new TextLine();
        $text->buildFieldCreateForm($formBuilder, $fieldDefinition, $languageCode);
    }

    public function testBuildFieldCreateFormWithStringLengthsNull()
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
                'validatorConfiguration' => array(
                    'StringLengthValidator' => array(
                        'minStringLength' => null,
                        'maxStringLength' => null,
                    ),
                ),
            )
        );

        $languageCode = 'eng-GB';

        $text = new TextLine();
        $text->buildFieldCreateForm($formBuilder, $fieldDefinition, $languageCode);
    }
}
