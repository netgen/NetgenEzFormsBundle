<?php

namespace Netgen\Bundle\EzFormsBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FieldTypeTypeExtension.
 */
class FieldTypeTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        // Returning 'form' extends all form types
        return FormType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined(
            array(
                'ezforms',
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $ezFormsVars = array();

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
