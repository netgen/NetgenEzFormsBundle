<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form;

use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;

class DataWrapperTest extends \PHPUnit_Framework_TestCase
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
