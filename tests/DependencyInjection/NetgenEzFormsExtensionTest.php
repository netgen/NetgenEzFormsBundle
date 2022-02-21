<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Netgen\Bundle\EzFormsBundle\DependencyInjection\NetgenEzFormsExtension;

final class NetgenEzFormsExtensionTest extends AbstractExtensionTestCase
{
    public function testItSetsValidContainerParameters(): void
    {
        $this->container->setParameter('ibexa.site_access.list', []);
        $this->load();
    }

    protected function getContainerExtensions(): array
    {
        return [
            new NetgenEzFormsExtension(),
        ];
    }

    protected function getMinimalConfiguration(): array
    {
        return [];
    }
}
