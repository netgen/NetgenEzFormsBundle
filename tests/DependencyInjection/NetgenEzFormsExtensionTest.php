<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Netgen\Bundle\EzFormsBundle\DependencyInjection\NetgenEzFormsExtension;

class NetgenEzFormsExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions()
    {
        return [
            new NetgenEzFormsExtension(),
        ];
    }

    public function testItSetsValidContainerParameters()
    {
        $this->container->setParameter('ezpublish.siteaccess.list', []);
        $this->load();
    }

    protected function getMinimalConfiguration()
    {
        return [];
    }
}