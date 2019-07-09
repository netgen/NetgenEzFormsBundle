<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\FieldTypeHandler;

use eZ\Publish\Core\FieldType\Time\Value as TimeValue;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\Time;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilder;

class TimeTest extends TestCase
{
    public function testAssertInstanceOfFieldTypeHandler()
    {
        $time = new Time();

        self::assertInstanceOf(FieldTypeHandler::class, $time);
    }

    public function testConvertFieldValueToForm()
    {
        $time = new Time();
        $timeValue = new TimeValue(500);
        $dateTime = new \DateTime('@500');

        $returnedValue = $time->convertFieldValueToForm($timeValue);

        self::assertSame($dateTime->format('H:i:s'), $returnedValue->format('H:i:s'));
    }

    public function testConvertFieldValueToFormWithoutTime()
    {
        $time = new Time();
        $timeValue = new TimeValue(null);

        $returnedValue = $time->convertFieldValueToForm($timeValue);

        self::assertInstanceOf(\DateTimeInterface::class, $returnedValue);
    }

    public function testConvertFieldValueFromFormWhenDataInstanceOfDateTime()
    {
        $time = new Time();
        $dateTime = new \DateTime();
        $timeValue = TimeValue::fromDateTime($dateTime);

        $returnedValue = $time->convertFieldValueFromForm($dateTime);

        self::assertSame($timeValue->time, $returnedValue->time);
    }

    public function testConvertFieldValueFromFormWhenDataIsInt()
    {
        $time = new Time();
        $timeValue = new TimeValue(500);

        $returnedValue = $time->convertFieldValueFromForm(500);

        self::assertSame($timeValue->time, $returnedValue->time);
    }

    public function testConvertFieldValueFromFormWhenDataIsString()
    {
        $time = new Time();
        $timeValue = new TimeValue(null);

        $returnedValue = $time->convertFieldValueFromForm('weee');

        self::assertSame($timeValue->time, $returnedValue->time);
    }

    public function testBuildFieldCreateForm()
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
                'fieldSettings' => [
                    'useSeconds' => true,
                ],
            ]
        );

        $languageCode = 'eng-GB';

        $time = new Time();
        $time->buildFieldCreateForm($formBuilder, $fieldDefinition, $languageCode);
    }
}
