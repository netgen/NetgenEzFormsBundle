<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\FieldTypeHandler;

use Ibexa\Core\FieldType\ISBN\Value as IsbnValue;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\Isbn;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilder;

final class IsbnTest extends TestCase
{
    public function testAssertInstanceOfFieldTypeHandler(): void
    {
        $isbn = new Isbn();

        self::assertInstanceOf(FieldTypeHandler::class, $isbn);
    }

    public function testConvertFieldValueToForm(): void
    {
        $isbn = new Isbn();
        $isbnValue = new IsbnValue('5367GBMK');

        $returnedValue = $isbn->convertFieldValueToForm($isbnValue);

        self::assertSame('5367GBMK', $returnedValue);
    }

    public function testConvertFieldValueFromForm(): void
    {
        $isbn = new Isbn();
        $isbnValue = new IsbnValue('5367GBMK');

        $returnedValue = $isbn->convertFieldValueFromForm('5367GBMK');

        self::assertSame($isbnValue->isbn, $returnedValue->isbn);
    }

    public function testConvertFieldValueFromFormWhenDataIsEmpty(): void
    {
        $isbn = new Isbn();
        $isbnValue = new IsbnValue('');

        $returnedValue = $isbn->convertFieldValueFromForm('');

        self::assertSame($isbnValue->isbn, $returnedValue->isbn);
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
                'defaultValue' => new IsbnValue('5367GBMK'),
                'descriptions' => ['fre-FR' => 'fre-FR'],
                'names' => ['fre-FR' => 'fre-FR'],
            ]
        );

        $languageCode = 'eng-GB';

        $isbn = new Isbn();
        $isbn->buildFieldCreateForm($formBuilder, $fieldDefinition, $languageCode);
    }

    public function testBuildFieldCreateFormIsbn13(): void
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
                'defaultValue' => new IsbnValue('5367GBMK'),
                'descriptions' => ['fre-FR' => 'fre-FR'],
                'names' => ['fre-FR' => 'fre-FR'],
                'fieldSettings' => [
                    'isISBN13' => true,
                ],
            ]
        );

        $languageCode = 'eng-GB';

        $isbn = new Isbn();
        $isbn->buildFieldCreateForm($formBuilder, $fieldDefinition, $languageCode);
    }
}
