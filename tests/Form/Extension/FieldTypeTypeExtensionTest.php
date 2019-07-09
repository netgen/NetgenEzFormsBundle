<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\Extension;

use Netgen\Bundle\EzFormsBundle\Form\Extension\FieldTypeTypeExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FieldTypeTypeExtensionTest extends TestCase
{
    public function testGetExtendedType()
    {
        $extension = new FieldTypeTypeExtension();

        self::assertSame(FormType::class, $extension->getExtendedType());
    }

    public function testSetDefaultOptions()
    {
        $resolver = $this->getMockBuilder(OptionsResolver::class)
            ->disableOriginalConstructor()
            ->setMethods(['setDefined'])
            ->getMock();

        $resolver->expects(self::once())
            ->method('setDefined');

        $extension = new FieldTypeTypeExtension();
        $extension->configureOptions($resolver);
    }

    public function testBuildView()
    {
        $formView = $this->getMockBuilder(FormView::class)
            ->disableOriginalConstructor()
            ->getMock();

        $form = $this->getMockForAbstractClass(FormInterface::class);

        $options = [
            'ezforms' => [
                'fielddefinition' => 'fielddefinition',
                'language_code' => 'language_code',
                'content' => 'content',
                'description' => 'description',
            ],
        ];

        $extension = new FieldTypeTypeExtension();
        $extension->buildView($formView, $form, $options);
    }
}
