<?php

namespace Netgen\Bundle\EzFormsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType as CoreUrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UrlType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'url',
            CoreUrlType::class,
            [
                'constraints' => new Assert\Url(),
            ]
        );

        $builder->add('text', TextType::class);
    }

    public function getBlockPrefix(): string
    {
        return 'ezforms_url';
    }
}
