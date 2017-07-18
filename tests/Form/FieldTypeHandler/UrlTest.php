<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\FieldTypeHandler;

use eZ\Publish\Core\FieldType\Url\Value as UrlValue;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\Url;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilder;

class UrlTest extends TestCase
{
    public function testAssertInstanceOfFieldTypeHandler()
    {
        $url = new Url();

        $this->assertInstanceOf(FieldTypeHandler::class, $url);
    }

    public function testConvertFieldValueToForm()
    {
        $url = new Url();
        $timeValue = new UrlValue('link', 'text');

        $returnedValue = $url->convertFieldValueToForm($timeValue);

        $this->assertEquals(array('url' => 'link', 'text' => 'text'), $returnedValue);
    }

    public function testConvertFieldValueFromForm()
    {
        $url = new Url();
        $timeValue = new UrlValue('link', 'text');

        $returnedValue = $url->convertFieldValueFromForm(array('url' => 'link', 'text' => 'text'));

        $this->assertEquals($timeValue, $returnedValue);
    }

    public function testConvertFieldValueFromFormWhenDataIsNotArray()
    {
        $url = new Url();
        $timeValue = new UrlValue(null, null);

        $returnedValue = $url->convertFieldValueFromForm('some string');

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
            )
        );

        $languageCode = 'eng-GB';

        $url = new Url();
        $url->buildFieldCreateForm($formBuilder, $fieldDefinition, $languageCode);
    }
}
