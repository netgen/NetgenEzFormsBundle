<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\FieldTypeHandler;

use DateTime;
use Ibexa\Core\FieldType\Date as DateValue;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\Date;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilder;

final class DateTest extends TestCase
{
    public function testAssertInstanceOfFieldTypeHandler(): void
    {
        $date = new Date();

        self::assertInstanceOf(FieldTypeHandler::class, $date);
    }

    public function testConvertFieldValueToForm(): void
    {
        $dateTime = new DateTime();
        $date = new Date();
        $dateValue = new DateValue\Value($dateTime);

        $returnedDate = $date->convertFieldValueToForm($dateValue);
        $dateTime->setTime(0, 0);
        self::assertSame($dateTime->format('Y-m-d'), $returnedDate->format('Y-m-d'));
    }

    public function testConvertFieldValueFromForm(): void
    {
        $dateTime = new DateTime();
        $date = new Date();
        $dateValue = new DateValue\Value($dateTime);

        $returnedDate = $date->convertFieldValueFromForm($dateTime);

        self::assertSame((string) $dateValue, (string) $returnedDate);
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
            ]
        );

        $languageCode = 'eng-GB';

        $date = new Date();

        $date->buildFieldCreateForm($formBuilder, $fieldDefinition, $languageCode);
    }

    public function testBuildFieldUpdateForm(): void
    {
        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['add'])
            ->getMock();

        $formBuilder->expects(self::once())
            ->method('add');

        $content = new Content();

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

        $date = new Date();

        $date->buildFieldUpdateForm($formBuilder, $fieldDefinition, $content, $languageCode);
    }
}
