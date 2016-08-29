<?php

namespace Netgen\Bundle\EzFormsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\DependencyInjection\Reference;

class FieldTypeHandlerRegistryPass implements CompilerPassInterface
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('netgen.ezforms.form.fieldtype_handler_registry')) {
            return;
        }

        $registry = $container->getDefinition('netgen.ezforms.form.fieldtype_handler_registry');

        foreach ($container->findTaggedServiceIds('netgen.ezforms.form.fieldtype_handler') as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (!isset($attribute['alias'])) {
                    throw new LogicException(
                        "'netgen.ezforms.form.fieldtype_handler' service tag " .
                        "needs an 'alias' attribute to identify the field type. None given."
                    );
                }

                $registry->addMethodCall(
                    'register',
                    array(
                        $attribute['alias'],
                        new Reference($id),
                    )
                );
            }
        }
    }
}
