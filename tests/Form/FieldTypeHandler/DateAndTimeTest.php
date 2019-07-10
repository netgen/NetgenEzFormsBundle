<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\FieldTypeHandler;

use DateTime;
use eZ\Publish\Core\FieldType\DateAndTime as DTValue;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\DateAndTime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilder;

class DateAndTimeTest extends TestCase
{
    public function testAssertInstanceOfFieldTypeHandler(): void
    {
        $dateAndTime = new DateAndTime();

        self::assertInstanceOf(FieldTypeHandler::class, $dateAndTime);
    }

    public function testConvertFieldValueToForm(): void
    {
        $dateAndTime = new DateAndTime();
        $dateTime = new DateTime();
        $dateValue = new DTValue\Value($dateTime);

        $returnedDate = $dateAndTime->convertFieldValueToForm($dateValue);

        self::assertSame($dateTime, $returnedDate);
    }

    public function testConvertFieldValueFromForm(): void
    {
        $dateTime = new DateTime();
        $dateAndTime = new DateAndTime();
        $dateValue = new DTValue\Value($dateTime);

        $returnedDate = $dateAndTime->convertFieldValueFromForm($dateTime);

        self::assertSame((string) $dateValue, (string) $returnedDate);
    }

    public function testBuildFieldCreateForm(): void
    {
        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['add'])
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

        $dateAndTime = new DateAndTime();

        $dateAndTime->buildFieldCreateForm($formBuilder, $fieldDefinition, $languageCode);
    }
}
