<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\FieldTypeHandler;

use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\Country;
use eZ\Publish\Core\FieldType\Country\Value as CountryValue;
use Symfony\Component\Form\FormBuilder;

class CountryTest extends \PHPUnit_Framework_TestCase
{
    public function testAssertInstanceOfFieldTypeHandler()
    {
        $countries = array(
            'HR' => array(
                'Name' => 'Croatia',
                'Code' => 'HR',
                'Language' => 'cro-HR'
            )
        );
        $country = new Country($countries);

        $this->assertInstanceOf(FieldTypeHandler::class, $country);
    }

    public function testConvertFieldValueToForm()
    {
        $countries = array(
            'HR' => array(
                'Name' => 'Croatia',
                'Code' => 'HR',
                'Language' => 'cro-HR'
            )
        );
        $country = new Country($countries);
        $countryValue = new CountryValue($countries);

        $returnedValue = $country->convertFieldValueToForm($countryValue);

        $this->assertEquals($countries, $returnedValue);
    }

    public function testConvertFieldValueFromForm()
    {
        $countries = array(
            'HR' => array(
                'Name' => 'Croatia',
                'Code' => 'HR',
                'Language' => 'cro-HR'
            )
        );
        $country = new Country($countries);
        $countryValue = new CountryValue($countries);
        $returnedData = $country->convertFieldValueFromForm('HR');

        $this->assertEquals($countryValue, $returnedData);
    }

    public function testConvertFieldValueFromFormWithCountryArray()
    {
        $countries = array(
            'HR' => array(
                'Name' => 'Croatia',
                'Code' => 'HR',
                'Language' => 'cro-HR'
            )
        );
        $country = new Country($countries);
        $countryValue = new CountryValue($countries);
        $returnedData = $country->convertFieldValueFromForm(array('HR'));

        $this->assertEquals($countryValue, $returnedData);
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

        $countries = array(
            'HR' => array(
                'Name' => 'Croatia',
                'Code' => 'HR',
                'Language' => 'cro-HR'
            )
        );
        $country = new Country($countries);
        $country->buildFieldCreateForm($formBuilder, $fieldDefinition, $languageCode);
    }
}
