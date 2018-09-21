<?php

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\Country\Value as CountryValue;
use eZ\Publish\SPI\FieldType\Value;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class Country extends FieldTypeHandler
{
    /**
     * Country codes.
     *
     * @var array
     */
    protected $countryData;

    /**
     * Removed redundant data from array.
     *
     * @var array
     */
    protected $filteredCountryData;

    /**
     * Constructor
     * Set information data from Service Container for countries.
     *
     * @param array $countryData
     */
    public function __construct($countryData)
    {
        $this->countryData = $countryData;

        foreach ($countryData as $countryCode => $country) {
            $this->filteredCountryData[$countryCode] = $country['Name'];
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return array|string
     */
    public function convertFieldValueToForm(Value $value, FieldDefinition $fieldDefinition = null)
    {
        $isMultiple = true;
        if ($fieldDefinition !== null) {
            $fieldSettings = $fieldDefinition->getFieldSettings();
            $isMultiple = $fieldSettings['isMultiple'];
        }

        if (!$isMultiple) {
            if (empty($value->countries)) {
                return '';
            }

            $keys = array_keys($value->countries);

            return reset($keys);
        }

        return array_keys($value->countries);
    }

    /**
     * {@inheritdoc}
     *
     * @return CountryValue;
     */
    public function convertFieldValueFromForm($data)
    {
        $country = array();

        // case if multiple is true
        if (is_array($data)) {
            foreach ($data as $countryCode) {
                if (array_key_exists($countryCode, $this->countryData)) {
                    $country[$countryCode] = $this->countryData[$countryCode];
                }
            }
        } else {
            if (array_key_exists($data, $this->countryData)) {
                $country[$data] = $this->countryData[$data];
            }
        }

        return new CountryValue((array) $country);
    }

    /**
     * {@inheritdoc}
     */
    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        $languageCode,
        Content $content = null
    ) {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode, $content);

        $options['expanded'] = false;
        $options['choices_as_values'] = true;
        $options['multiple'] = $fieldDefinition->getFieldSettings()['isMultiple'];

        $options['choices'] = array_flip($this->filteredCountryData);

        $formBuilder->add($fieldDefinition->identifier, ChoiceType::class, $options);
    }
}
