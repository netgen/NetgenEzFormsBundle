<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Netgen\Bundle\EzFormsBundle\DependencyInjection\NetgenEzFormsExtension;

class NetgenEzFormsExtensionTest extends AbstractExtensionTestCase
{
    public function testItSetsValidContainerParameters(): void
    {
        $this->container->setParameter('ezpublish.siteaccess.list', array());
        $this->load();
    }

    protected function getContainerExtensions(): array
    {
        return array(
            new NetgenEzFormsExtension(),
        );
    }

    protected function getMinimalConfiguration(): array
    {
        return array();
    }
}
