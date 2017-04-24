<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\EzFormsBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    protected function getConfiguration()
    {
        return new Configuration();
    }

    public function testConfigurationValuesAreOkAndValid()
    {
        $this->assertConfigurationIsValid(
            [
                'netgen_netgen_ez_forms' => [],
            ]
        );
    }
}