<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\FieldTypeHandler;

use eZ\Publish\Core\FieldType\DateAndTime as DTValue;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\DateAndTime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilder;

class DateAndTimeTest extends TestCase
{
    public function testAssertInstanceOfFieldTypeHandler()
    {
        $dateAndTime = new DateAndTime();

        $this->assertInstanceOf(FieldTypeHandler::class, $dateAndTime);
    }

    public function testConvertFieldValueToForm()
    {
        $dateAndTime = new DateAndTime();
        $dateTime = new \DateTime();
        $dateValue = new DTValue\Value($dateTime);

        $returnedDate = $dateAndTime->convertFieldValueToForm($dateValue);

        $this->assertEquals($dateTime, $returnedDate);
    }

    public function testConvertFieldValueFromForm()
    {
        $dateTime = new \DateTime();
        $dateAndTime = new DateAndTime();
        $dateValue = new DTValue\Value($dateTime);

        $returnedDate = $dateAndTime->convertFieldValueFromForm($dateTime);

        $this->assertEquals($dateValue, $returnedDate);
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
            )
        );

        $languageCode = 'eng-GB';

        $dateAndTime = new DateAndTime();

        $dateAndTime->buildFieldCreateForm($formBuilder, $fieldDefinition, $languageCode);
    }
}
