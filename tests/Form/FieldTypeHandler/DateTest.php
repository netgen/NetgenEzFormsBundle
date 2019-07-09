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

        self::assertInstanceOf(FieldTypeHandler::class, $date);
    }

    public function testConvertFieldValueToForm()
    {
        $dateTime = new \DateTime();
        $date = new Date();
        $dateValue = new DateValue\Value($dateTime);

        $returnedDate = $date->convertFieldValueToForm($dateValue);
        $dateTime->setTime(0, 0);
        self::assertSame($dateTime->format('Y-m-d'), $returnedDate->format('Y-m-d'));
    }

    public function testConvertFieldValueFromForm()
    {
        $dateTime = new \DateTime();
        $date = new Date();
        $dateValue = new DateValue\Value($dateTime);

        $returnedDate = $date->convertFieldValueFromForm($dateTime);

        self::assertSame((string) $dateValue, (string) $returnedDate);
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
            ]
        );

        $languageCode = 'eng-GB';

        $date = new Date();

        $date->buildFieldCreateForm($formBuilder, $fieldDefinition, $languageCode);
    }

    public function testBuildFieldUpdateForm()
    {
        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['add'])
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
