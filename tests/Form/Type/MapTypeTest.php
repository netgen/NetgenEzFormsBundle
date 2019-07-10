<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\Type;

use Netgen\Bundle\EzFormsBundle\Form\Type\MapType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class MapTypeTest extends TestCase
{
    public function testItExtendsAbstractType(): void
    {
        self::assertInstanceOf(AbstractType::class, new MapType());
    }

    public function testItAddsFormFields(): void
    {
        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['add'])
            ->getMock();

        $formBuilder->expects(self::exactly(3))
            ->method('add');

        $url = new MapType();
        $url->buildForm($formBuilder, []);
    }
}
