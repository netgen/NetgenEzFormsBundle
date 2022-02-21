<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\FieldTypeHandler;

use Ibexa\Core\FieldType\TextBlock\Value as TextBlockValue;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\TextBlock;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilder;

final class TextBlockTest extends TestCase
{
    public function testAssertInstanceOfFieldTypeHandler(): void
    {
        $text = new TextBlock();

        self::assertInstanceOf(FieldTypeHandler::class, $text);
    }

    public function testConvertFieldValueToForm(): void
    {
        $text = new TextBlock();
        $textValue = new TextBlockValue('Some text');

        $returnedValue = $text->convertFieldValueToForm($textValue);

        self::assertSame('Some text', $returnedValue);
    }

    public function testConvertFieldValueFromForm(): void
    {
        $text = new TextBlock();
        $textValue = new TextBlockValue('Some text');

        $returnedValue = $text->convertFieldValueFromForm('Some text');

        self::assertSame($textValue->text, $returnedValue->text);
    }

    public function testConvertFieldValueFromFormWhenDataIsEmpty(): void
    {
        $text = new TextBlock();
        $textValue = new TextBlockValue('');

        $returnedValue = $text->convertFieldValueFromForm('');

        self::assertSame($textValue->text, $returnedValue->text);
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
                'descriptions' => ['fre-FR' => 'fre-FR'],
                'names' => ['fre-FR' => 'fre-FR'],
                'fieldSettings' => [
                    'textRows' => 4,
                ],
            ]
        );

        $languageCode = 'eng-GB';

        $text = new TextBlock();
        $text->buildFieldCreateForm($formBuilder, $fieldDefinition, $languageCode);
    }
}
