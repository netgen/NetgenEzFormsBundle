<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle;

use Netgen\Bundle\EzFormsBundle\DependencyInjection\Compiler\FieldTypeHandlerRegistryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class NetgenEzFormsBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new FieldTypeHandlerRegistryPass());
    }
}
