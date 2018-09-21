<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\FieldTypeHandler;

use eZ\Publish\Core\FieldType\Country\Value as CountryValue;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\Country;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilder;

class CountryTest extends TestCase
{
    public function testAssertInstanceOfFieldTypeHandler()
    {
        $countries = array(
            'HR' => array(
                'Name' => 'Croatia',
                'Code' => 'HR',
                'Language' => 'cro-HR',
            ),
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
                'Language' => 'cro-HR',
            ),
        );
        $country = new Country($countries);
        $countryValue = new CountryValue($countries);

        $returnedValue = $country->convertFieldValueToForm($countryValue);

        $this->assertEquals(['HR'], $returnedValue);
    }

    public function testConvertFieldValueToFormMultipleValues()
    {
        $fieldDefinition = new FieldDefinition(
            array(
                'fieldSettings' => array(
                    'isMultiple' => true,
                ),
            )
        );

        $countries = array(
            'HR' => array(
                'Name' => 'Croatia',
                'Code' => 'HR',
                'Language' => 'cro-HR',
            ),
            'BE' => array(
                'Name' => 'Belgium',
                'Code' => 'BE',
                'Language' => 'be-FR',
            ),
            'BB' => array(
                'Name' => 'Barbados',
                'Code' => 'BB',
                'Language' => 'be-FR',
            ),
        );

        $selectedCountries = array(
            'HR' => array(
                'Name' => 'Croatia',
                'Code' => 'HR',
                'Language' => 'cro-HR',
            ),
            'BB' => array(
                'Name' => 'Barbados',
                'Code' => 'BB',
                'Language' => 'be-FR',
            ),
        );

        $country = new Country($countries);
        $countryValue = new CountryValue($selectedCountries);

        $returnedValue = $country->convertFieldValueToForm($countryValue, $fieldDefinition);

        $this->assertEquals(['HR', 'BB'], $returnedValue);
    }

    public function testConvertFieldValueToFormSingleValue()
    {
        $fieldDefinition = new FieldDefinition(
            array(
                'fieldSettings' => array(
                    'isMultiple' => false,
                ),
            )
        );

        $countries = array(
            'HR' => array(
                'Name' => 'Croatia',
                'Code' => 'HR',
                'Language' => 'cro-HR',
            ),
            'BE' => array(
                'Name' => 'Belgium',
                'Code' => 'BE',
                'Language' => 'be-FR',
            ),
            'BB' => array(
                'Name' => 'Barbados',
                'Code' => 'BB',
                'Language' => 'be-FR',
            ),
        );

        $selectedCountries = array(
            'HR' => array(
                'Name' => 'Croatia',
                'Code' => 'HR',
                'Language' => 'cro-HR',
            ),
        );

        $country = new Country($countries);
        $countryValue = new CountryValue($selectedCountries);

        $returnedValue = $country->convertFieldValueToForm($countryValue, $fieldDefinition);

        $this->assertEquals('HR', $returnedValue);
    }

    public function testConvertFieldValueToFormWithNoneSelected()
    {
        $fieldDefinition = new FieldDefinition(
            array(
                'fieldSettings' => array(
                    'isMultiple' => false,
                ),
            )
        );

        $countries = array(
            'HR' => array(
                'Name' => 'Croatia',
                'Code' => 'HR',
                'Language' => 'cro-HR',
            ),
            'BE' => array(
                'Name' => 'Belgium',
                'Code' => 'BE',
                'Language' => 'be-FR',
            ),
            'BB' => array(
                'Name' => 'Barbados',
                'Code' => 'BB',
                'Language' => 'be-FR',
            ),
        );

        $selectedCountries = array();

        $country = new Country($countries);
        $countryValue = new CountryValue($selectedCountries);

        $returnedValue = $country->convertFieldValueToForm($countryValue, $fieldDefinition);

        $this->assertEquals('', $returnedValue);
    }

    public function testConvertFieldValueFromForm()
    {
        $countries = array(
            'HR' => array(
                'Name' => 'Croatia',
                'Code' => 'HR',
                'Language' => 'cro-HR',
            ),
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
                'Language' => 'cro-HR',
            ),
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
                'Language' => 'cro-HR',
            ),
        );
        $country = new Country($countries);
        $country->buildFieldCreateForm($formBuilder, $fieldDefinition, $languageCode);
    }
}
