<?php

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use eZ\Publish\SPI\FieldType\Value;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\FieldType\Country\Value as CountryValue;

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
     */
    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        $languageCode,
        Content $content = null
    ) {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode, $content);

        $options['expanded'] = false;
        $options['multiple'] = $fieldDefinition->getFieldSettings()['isMultiple'];

        $options['choice_list'] = new ChoiceList(
            array_keys($this->filteredCountryData),
            array_values($this->filteredCountryData)
        );

        $formBuilder->add($fieldDefinition->identifier, 'choice', $options);
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function convertFieldValueToForm(Value $value, FieldDefinition $fieldDefinition = null)
    {
        return $value->countries;
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

        return new CountryValue((array)$country);
    }
}
