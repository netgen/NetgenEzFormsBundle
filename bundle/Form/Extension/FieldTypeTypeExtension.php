<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FieldTypeTypeExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        // Returning 'form' extends all form types
        return [FormType::class];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined(
            [
                'ezforms',
            ]
        );
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $ezFormsVars = [];

        if (isset($options['ezforms']['fielddefinition'])) {
            $ezFormsVars['fielddefinition'] = $options['ezforms']['fielddefinition'];
        }

        if (isset($options['ezforms']['language_code'])) {
            $ezFormsVars['language_code'] = $options['ezforms']['language_code'];
        }

        if (isset($options['ezforms']['content'])) {
            $ezFormsVars['content'] = $options['ezforms']['content'];
        }

        if (isset($options['ezforms']['description'])) {
            $ezFormsVars['description'] = $options['ezforms']['description'];
        }

        $view->vars['ezforms'] = $ezFormsVars;
    }
}
