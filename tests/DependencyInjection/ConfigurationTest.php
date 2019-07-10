<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Tests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Netgen\Bundle\EzFormsBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    public function testConfigurationValuesAreOkAndValid(): void
    {
        $this->assertConfigurationIsValid(
            [
                'netgen_netgen_ez_forms' => [],
            ]
        );
    }

    protected function getConfiguration(): Configuration
    {
        return new Configuration();
    }
}
