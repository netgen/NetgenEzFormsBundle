<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\FieldTypeHandler;

use Ibexa\Core\FieldType\Country\Value as CountryValue;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\Country;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilder;

final class CountryTest extends TestCase
{
    public function testAssertInstanceOfFieldTypeHandler(): void
    {
        $countries = [
            'HR' => [
                'Name' => 'Croatia',
                'Code' => 'HR',
                'Language' => 'cro-HR',
            ],
        ];
        $country = new Country($countries);

        self::assertInstanceOf(FieldTypeHandler::class, $country);
    }

    public function testConvertFieldValueToForm(): void
    {
        $countries = [
            'HR' => [
                'Name' => 'Croatia',
                'Code' => 'HR',
                'Language' => 'cro-HR',
            ],
        ];
        $country = new Country($countries);
        $countryValue = new CountryValue($countries);

        $returnedValue = $country->convertFieldValueToForm($countryValue);

        self::assertSame(['HR'], $returnedValue);
    }

    public function testConvertFieldValueToFormMultipleValues(): void
    {
        $fieldDefinition = new FieldDefinition(
            [
                'fieldSettings' => [
                    'isMultiple' => true,
                ],
            ]
        );

        $countries = [
            'HR' => [
                'Name' => 'Croatia',
                'Code' => 'HR',
                'Language' => 'cro-HR',
            ],
            'BE' => [
                'Name' => 'Belgium',
                'Code' => 'BE',
                'Language' => 'be-FR',
            ],
            'BB' => [
                'Name' => 'Barbados',
                'Code' => 'BB',
                'Language' => 'be-FR',
            ],
        ];

        $selectedCountries = [
            'HR' => [
                'Name' => 'Croatia',
                'Code' => 'HR',
                'Language' => 'cro-HR',
            ],
            'BB' => [
                'Name' => 'Barbados',
                'Code' => 'BB',
                'Language' => 'be-FR',
            ],
        ];

        $country = new Country($countries);
        $countryValue = new CountryValue($selectedCountries);

        $returnedValue = $country->convertFieldValueToForm($countryValue, $fieldDefinition);

        self::assertSame(['HR', 'BB'], $returnedValue);
    }

    public function testConvertFieldValueToFormSingleValue(): void
    {
        $fieldDefinition = new FieldDefinition(
            [
                'fieldSettings' => [
                    'isMultiple' => false,
                ],
            ]
        );

        $countries = [
            'HR' => [
                'Name' => 'Croatia',
                'Code' => 'HR',
                'Language' => 'cro-HR',
            ],
            'BE' => [
                'Name' => 'Belgium',
                'Code' => 'BE',
                'Language' => 'be-FR',
            ],
            'BB' => [
                'Name' => 'Barbados',
                'Code' => 'BB',
                'Language' => 'be-FR',
            ],
        ];

        $selectedCountries = [
            'HR' => [
                'Name' => 'Croatia',
                'Code' => 'HR',
                'Language' => 'cro-HR',
            ],
        ];

        $country = new Country($countries);
        $countryValue = new CountryValue($selectedCountries);

        $returnedValue = $country->convertFieldValueToForm($countryValue, $fieldDefinition);

        self::assertSame('HR', $returnedValue);
    }

    public function testConvertFieldValueToFormWithNoneSelected(): void
    {
        $fieldDefinition = new FieldDefinition(
            [
                'fieldSettings' => [
                    'isMultiple' => false,
                ],
            ]
        );

        $countries = [
            'HR' => [
                'Name' => 'Croatia',
                'Code' => 'HR',
                'Language' => 'cro-HR',
            ],
            'BE' => [
                'Name' => 'Belgium',
                'Code' => 'BE',
                'Language' => 'be-FR',
            ],
            'BB' => [
                'Name' => 'Barbados',
                'Code' => 'BB',
                'Language' => 'be-FR',
            ],
        ];

        $selectedCountries = [];

        $country = new Country($countries);
        $countryValue = new CountryValue($selectedCountries);

        $returnedValue = $country->convertFieldValueToForm($countryValue, $fieldDefinition);

        self::assertSame('', $returnedValue);
    }

    public function testConvertFieldValueFromForm(): void
    {
        $countries = [
            'HR' => [
                'Name' => 'Croatia',
                'Code' => 'HR',
                'Language' => 'cro-HR',
            ],
        ];
        $country = new Country($countries);
        $countryValue = new CountryValue($countries);
        $returnedData = $country->convertFieldValueFromForm('HR');

        self::assertSame($countryValue->countries, $returnedData->countries);
    }

    public function testConvertFieldValueFromFormWithCountryArray(): void
    {
        $countries = [
            'HR' => [
                'Name' => 'Croatia',
                'Code' => 'HR',
                'Language' => 'cro-HR',
            ],
        ];
        $country = new Country($countries);
        $countryValue = new CountryValue($countries);
        $returnedData = $country->convertFieldValueFromForm(['HR']);

        self::assertSame($countryValue->countries, $returnedData->countries);
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

        $countries = [
            'HR' => [
                'Name' => 'Croatia',
                'Code' => 'HR',
                'Language' => 'cro-HR',
            ],
        ];
        $country = new Country($countries);
        $country->buildFieldCreateForm($formBuilder, $fieldDefinition, $languageCode);
    }
}
