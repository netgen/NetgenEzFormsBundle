<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\FieldTypeHandler;

use eZ\Publish\Core\FieldType\Date as DateValue;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\Date;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilder;

class DateTest extends TestCase
{
    public function testAssertInstanceOfFieldTypeHandler()
    {
        $date = new Date();

        $this->assertInstanceOf(FieldTypeHandler::class, $date);
    }

    public function testConvertFieldValueToForm()
    {
        $dateTime = new \DateTime();
        $date = new Date();
        $dateValue = new DateValue\Value($dateTime);

        $returnedDate = $date->convertFieldValueToForm($dateValue);
        $dateTime->setTime(0, 0, 0);
        $this->assertEquals($dateTime, $returnedDate);
    }

    public function testConvertFieldValueFromForm()
    {
        $dateTime = new \DateTime();
        $date = new Date();
        $dateValue = new DateValue\Value($dateTime);

        $returnedDate = $date->convertFieldValueFromForm($dateTime);

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

        $date = new Date();

        $date->buildFieldCreateForm($formBuilder, $fieldDefinition, $languageCode);
    }

    public function testBuildFieldUpdateForm()
    {
        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('add'))
            ->getMock();

        $formBuilder->expects($this->once())
            ->method('add');

        $content = new Content();

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

        $date = new Date();

        $date->buildFieldUpdateForm($formBuilder, $fieldDefinition, $content, $languageCode);
    }
}
