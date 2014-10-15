<?php

namespace Netgen\Bundle\EzFormsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Netgen\Bundle\EzFormsBundle\DependencyInjection\Compiler\FieldTypeHandlerRegistryPass;

class NetgenEzFormsBundle extends Bundle
{
    public function build( ContainerBuilder $container )
    {
        parent::build( $container );

        $container->addCompilerPass( new FieldTypeHandlerRegistryPass() );
    }
}
