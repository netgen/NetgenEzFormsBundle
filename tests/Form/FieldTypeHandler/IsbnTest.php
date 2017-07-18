<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\FieldTypeHandler;

use eZ\Publish\Core\FieldType\ISBN\Value as IsbnValue;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\Isbn;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilder;

class IsbnTest extends TestCase
{
    public function testAssertInstanceOfFieldTypeHandler()
    {
        $isbn = new Isbn();

        $this->assertInstanceOf(FieldTypeHandler::class, $isbn);
    }

    public function testConvertFieldValueToForm()
    {
        $isbn = new Isbn();
        $isbnValue = new IsbnValue('5367GBMK');

        $returnedValue = $isbn->convertFieldValueToForm($isbnValue);

        $this->assertEquals('5367GBMK', $returnedValue);
    }

    public function testConvertFieldValueFromForm()
    {
        $isbn = new Isbn();
        $isbnValue = new IsbnValue('5367GBMK');

        $returnedValue = $isbn->convertFieldValueFromForm('5367GBMK');

        $this->assertEquals($isbnValue, $returnedValue);
    }

    public function testConvertFieldValueFromFormWhenDataIsEmpty()
    {
        $isbn = new Isbn();
        $isbnValue = new IsbnValue('');

        $returnedValue = $isbn->convertFieldValueFromForm('');

        $this->assertEquals($isbnValue, $returnedValue);
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
                'defaultValue' => new IsbnValue('5367GBMK'),
                'descriptions' => array('fre-FR' => 'fre-FR'),
                'names' => array('fre-FR' => 'fre-FR'),
            )
        );

        $languageCode = 'eng-GB';

        $isbn = new Isbn();
        $isbn->buildFieldCreateForm($formBuilder, $fieldDefinition, $languageCode);
    }

    public function testBuildFieldCreateFormIsbn13()
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
                'defaultValue' => new IsbnValue('5367GBMK'),
                'descriptions' => array('fre-FR' => 'fre-FR'),
                'names' => array('fre-FR' => 'fre-FR'),
                'fieldSettings' => array(
                    'isISBN13' => true,
                ),
            )
        );

        $languageCode = 'eng-GB';

        $isbn = new Isbn();
        $isbn->buildFieldCreateForm($formBuilder, $fieldDefinition, $languageCode);
    }
}
