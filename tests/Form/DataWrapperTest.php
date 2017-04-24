<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form;

use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use PHPUnit\Framework\TestCase;

class DataWrapperTest extends TestCase
{
    public function testSetValuesCorrectly()
    {
        $payload = new \stdClass();
        $payload->payload = 'payload';

        $definition = new \stdClass();
        $definition->definition = 'definition';

        $target = new \stdClass();
        $target->target = 'target';

        $dataWrapper = new DataWrapper($payload, $definition, $target);

        $this->assertSame($payload, $dataWrapper->payload);
        $this->assertSame($definition, $dataWrapper->definition);
        $this->assertSame($target, $dataWrapper->target);
    }
}
