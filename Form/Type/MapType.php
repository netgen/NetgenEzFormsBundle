<?php

namespace Netgen\Bundle\EzFormsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class MapType.
 */
class MapType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('address', 'text', array(
            'label' => 'ezforms.form.map.address.label',
        ));

        $builder->add('latitude', 'number', array(
            'label' => 'ezforms.form.map.latitude.label',
        ));

        $builder->add('longitude', 'number', array(
            'label' => 'ezforms.form.map.longitude.label',
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'ezforms_map';
    }
}
