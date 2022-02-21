<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\FieldTypeHandler;

use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Core\FieldType\BinaryFile\Value as FileValue;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\BinaryFile;
use Netgen\Bundle\EzFormsBundle\Tests\Form\Mock\FileMock;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilder;

final class BinaryFileTest extends TestCase
{
    public function testAssertInstanceOfFieldTypeHandler(): void
    {
        $binaryFile = new BinaryFile();

        self::assertInstanceOf(FieldTypeHandler::class, $binaryFile);
    }

    public function testConvertFieldValueToForm(): void
    {
        $binaryFile = new BinaryFile();
        $binaryFileValue = $this->getMockForAbstractClass(Value::class);

        $returnedValue = $binaryFile->convertFieldValueToForm($binaryFileValue);

        self::assertNull($returnedValue);
    }

    public function testConvertFieldValueFromForm(): void
    {
        $binaryFile = new BinaryFile();
        $data = new FileMock();

        $returnedData = $binaryFile->convertFieldValueFromForm($data);

        self::assertInstanceOf(FileValue::class, $returnedData);
    }

    public function testConvertFieldValueFromFormWhenDataIsNull(): void
    {
        $binaryFile = new BinaryFile();

        $returnedData = $binaryFile->convertFieldValueFromForm(null);

        self::assertNull($returnedData);
    }

    public function testBuildFieldCreateForm(): void
    {
        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['add'])
            ->getMock();

        $formBuilder->expects(self::exactly(2))
            ->method('add');

        $fieldDefinition = new FieldDefinition(
            [
                'id' => 'id',
                'identifier' => 'identifier',
                'isRequired' => true,
                'descriptions' => ['fre-FR' => 'fre-FR'],
                'names' => ['fre-FR' => 'fre-FR'],
            ]
        );

        $languageCode = 'eng-GB';

        $binaryFile = new BinaryFile();
        $binaryFile->buildFieldCreateForm($formBuilder, $fieldDefinition, $languageCode);
    }
}
