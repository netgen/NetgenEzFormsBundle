<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\Payload;

use Netgen\Bundle\EzFormsBundle\Form\Payload\InformationCollectionStruct;
use PHPUnit\Framework\TestCase;

class InformationCollectionStructTest extends TestCase
{
    public function testGetCollectedFieldValue(): void
    {
        $struct = new InformationCollectionStruct();
        $struct->setCollectedFieldValue('some_field', 'some_value');

        self::assertSame('some_value', $struct->getCollectedFieldValue('some_field'));
        self::assertNull($struct->getCollectedFieldValue('some_field_not_existing'));
    }

    public function testGetCollectedFields(): void
    {
        $fields = [
            'some_field_1' => 'some_value_1',
            'some_field_2' => 'some_value_2',
        ];

        $struct = new InformationCollectionStruct();
        $struct->setCollectedFieldValue('some_field_1', 'some_value_1');
        $struct->setCollectedFieldValue('some_field_2', 'some_value_2');

        self::assertSame($fields, $struct->getCollectedFields());
    }
}
