<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\FieldTypeHandler;

use Ibexa\Core\FieldType\Selection\Value as SelectionValue;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\Selection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilder;

final class SelectionTest extends TestCase
{
    /**
     * @var Selection
     */
    protected $handler;

    protected function setUp(): void
    {
        $this->handler = new Selection();
    }

    public function testAssertInstanceOfFieldTypeHandler(): void
    {
        self::assertInstanceOf(FieldTypeHandler::class, $this->handler);
    }

    public function testConvertFieldValueToFormWithIdentifiersArrayEmpty(): void
    {
        $identifiers = [];
        $selection = new SelectionValue($identifiers);
        $fieldDefinition = new FieldDefinition(
            [
                'fieldSettings' => [
                    'isMultiple' => false,
                ],
            ]
        );

        $converted = $this->handler->convertFieldValueToForm($selection, $fieldDefinition);

        self::assertSame('', $converted);
    }

    public function testConvertFieldValueToFormWithFieldDefinitionMultiple(): void
    {
        $identifiers = ['identifier1', 'identifier2'];
        $selection = new SelectionValue($identifiers);
        $fieldDefinition = new FieldDefinition(
            [
                'fieldSettings' => [
                    'isMultiple' => true,
                ],
            ]
        );

        $converted = $this->handler->convertFieldValueToForm($selection, $fieldDefinition);

        self::assertSame($identifiers, $converted);
    }

    public function testConvertFieldValueToFormWithFieldDefinitionSingle(): void
    {
        $identifiers = ['identifier1', 'identifier2'];
        $selection = new SelectionValue($identifiers);
        $fieldDefinition = new FieldDefinition(
            [
                'fieldSettings' => [
                    'isMultiple' => false,
                ],
            ]
        );

        $converted = $this->handler->convertFieldValueToForm($selection, $fieldDefinition);

        self::assertSame($identifiers[0], $converted);
    }

    public function testConvertFieldValueToForm(): void
    {
        $selection = new Selection();
        $selectionValue = new SelectionValue([1]);

        $returnedValue = $selection->convertFieldValueToForm($selectionValue);

        self::assertSame([1], $returnedValue);
    }

    public function testConvertFieldValueFromForm(): void
    {
        $selection = new Selection();
        $selectionValue = new SelectionValue([1]);

        $returnedValue = $selection->convertFieldValueFromForm(1);

        self::assertSame($selectionValue->selection, $returnedValue->selection);
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
                    'options' => [
                        1 => 'Selection 1',
                        2 => 'Selection 2',
                    ],
                    'isMultiple' => true,
                ],
            ]
        );

        $languageCode = 'eng-GB';

        $selection = new Selection();
        $selection->buildFieldCreateForm($formBuilder, $fieldDefinition, $languageCode);
    }
}
