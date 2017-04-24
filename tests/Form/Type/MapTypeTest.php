<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\Type;

use Netgen\Bundle\EzFormsBundle\Form\Type\MapType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class MapTypeTest extends TestCase
{
    public function testItExtendsAbstractType()
    {
        $this->assertInstanceOf(AbstractType::class, new MapType());
    }

    public function testItAddsFormFields()
    {
        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('add'))
            ->getMock();

        $formBuilder->expects($this->exactly(3))
            ->method('add');

        $url = new MapType();
        $url->buildForm($formBuilder, array());
    }

    public function testItReturnsValidFormName()
    {
        $url = new MapType();

        $this->assertEquals('ezforms_map', $url->getName());
    }
}
