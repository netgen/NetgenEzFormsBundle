<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\Type;

use Netgen\Bundle\EzFormsBundle\Form\Type\UrlType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType as CoreUrlType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Validator\Constraints as Assert;

class UrlTypeTest extends TestCase
{
    public function testItExtendsAbstractType()
    {
        $this->assertInstanceOf(AbstractType::class, new UrlType());
    }

    public function testItAddsFormFields()
    {
        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('add'))
            ->getMock();

        $formBuilder->expects($this->at(0))
            ->method('add')
            ->with('url', CoreUrlType::class, array('constraints' => new Assert\Url()));

        $formBuilder->expects($this->at(1))
            ->method('add')
            ->with('text', TextType::class);

        $url = new UrlType();
        $url->buildForm($formBuilder, array());
    }

    public function testItReturnsValidFormName()
    {
        $url = new UrlType();

        $this->assertEquals('ezforms_url', $url->getName());
    }
}
