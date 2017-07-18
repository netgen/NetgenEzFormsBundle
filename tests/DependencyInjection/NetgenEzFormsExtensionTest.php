<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Netgen\Bundle\EzFormsBundle\DependencyInjection\NetgenEzFormsExtension;

class NetgenEzFormsExtensionTest extends AbstractExtensionTestCase
{
    public function testItSetsValidContainerParameters()
    {
        $this->container->setParameter('ezpublish.siteaccess.list', array());
        $this->load();
    }

    protected function getContainerExtensions()
    {
        return array(
            new NetgenEzFormsExtension(),
        );
    }

    protected function getMinimalConfiguration()
    {
        return array();
    }
}
