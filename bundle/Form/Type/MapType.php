<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class MapType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('address', TextType::class, [
            'label' => 'ezforms.form.map.address.label',
        ]);

        $builder->add('latitude', NumberType::class, [
            'label' => 'ezforms.form.map.latitude.label',
        ]);

        $builder->add('longitude', NumberType::class, [
            'label' => 'ezforms.form.map.longitude.label',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'ezforms_map';
    }
}
