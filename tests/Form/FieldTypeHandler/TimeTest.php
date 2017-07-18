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

        $this->assertInstanceOf(FieldTypeHandler::class, $time);
    }

    public function testConvertFieldValueToForm()
    {
        $time = new Time();
        $timeValue = new TimeValue(500);
        $dateTime = new \DateTime('@500');

        $returnedValue = $time->convertFieldValueToForm($timeValue);

        $this->assertEquals($dateTime, $returnedValue);
    }

    public function testConvertFieldValueToFormWithoutTime()
    {
        $time = new Time();
        $timeValue = new TimeValue(null);
        $dateTime = new \DateTime();

        $returnedValue = $time->convertFieldValueToForm($timeValue);

        $this->assertEquals($dateTime->getTimestamp(), $returnedValue->getTimestamp());
    }

    public function testConvertFieldValueFromFormWhenDataInstanceOfDateTime()
    {
        $time = new Time();
        $dateTime = new \DateTime();
        $timeValue = TimeValue::fromDateTime($dateTime);

        $returnedValue = $time->convertFieldValueFromForm($dateTime);

        $this->assertEquals($timeValue, $returnedValue);
    }

    public function testConvertFieldValueFromFormWhenDataIsInt()
    {
        $time = new Time();
        $timeValue = new TimeValue(500);

        $returnedValue = $time->convertFieldValueFromForm(500);

        $this->assertEquals($timeValue, $returnedValue);
    }

    public function testConvertFieldValueFromFormWhenDataIsString()
    {
        $time = new Time();
        $timeValue = new TimeValue(null);

        $returnedValue = $time->convertFieldValueFromForm('weee');

        $this->assertEquals($timeValue, $returnedValue);
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
                    'useSeconds' => true,
                ),
            )
        );

        $languageCode = 'eng-GB';

        $time = new Time();
        $time->buildFieldCreateForm($formBuilder, $fieldDefinition, $languageCode);
    }
}
