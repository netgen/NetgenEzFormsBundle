<?php

namespace Netgen\Bundle\EzFormsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class MapType
 * @package Netgen\Bundle\EzFormsBundle\Form\Type
 */
class MapType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("address", "text", array(
            "label" => "ezforms.form.map.address.label",
        ));

        $builder->add("address_hidden", "hidden", array(
            "label" => "ezforms.form.map.address.label",
        ));

        $builder->add("find_address", "button", array(
            "label" => "ezforms.form.map.find_address.label",
        ));

        $builder->add("restore_coordinates", "button", array(
            "label" => "ezforms.form.map.restore_coordinates.label",
            "attr" => array(
                "disabled" => true,
            ),
        ));

        $builder->add("latitude", "number", array(
            "label" => "ezforms.form.map.latitude.label",
        ));

        $builder->add("latitude_hidden", "hidden", array(
            "label" => "ezforms.form.map.latitude.label",
        ));

        $builder->add("longitude", "number", array(
            "label" => "ezforms.form.map.longitude.label",
        ));

        $builder->add("longitude_hidden", "hidden", array(
            "label" => "ezforms.form.map.longitude.label",
        ));

        $builder->add("current_location", "button", array(
            "label" => "ezforms.form.map.current_location.label",
            "attr" => array(
                "class" => "ez-gmap-my-location-button",
            ),
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return "ezforms_map";
    }
}
