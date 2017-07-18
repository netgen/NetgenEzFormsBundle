<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\EzFormsBundle\DependencyInjection\Compiler\FieldTypeHandlerRegistryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class FieldTypeHandlerRegistryPassTest extends AbstractCompilerPassTestCase
{
    public function testCompilerPassCollectsValidServices()
    {
        $registry = new Definition();
        $this->setDefinition('netgen.ezforms.form.fieldtype_handler_registry', $registry);

        $handler = new Definition();
        $handler->addTag('netgen.ezforms.form.fieldtype_handler', array('alias' => 'eztext'));
        $this->setDefinition('netgen.ezforms.form.fieldtype_handler.eztext', $handler);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen.ezforms.form.fieldtype_handler_registry',
            'register',
            array(
                'eztext',
                new Reference('netgen.ezforms.form.fieldtype_handler.eztext'),
            )
        );
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\LogicException
     * @expectedExceptionMessage 'netgen.ezforms.form.fieldtype_handler' service tag needs an 'alias' attribute to identify the field type. None given.
     */
    public function testCompilerPassMustThrowExceptionIfHandlerServiceHasntGotAlias()
    {
        $registry = new Definition();
        $this->setDefinition('netgen.ezforms.form.fieldtype_handler_registry', $registry);

        $handler = new Definition();
        $handler->addTag('netgen.ezforms.form.fieldtype_handler');
        $this->setDefinition('netgen.ezforms.form.fieldtype_handler.eztext', $handler);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen.ezforms.form.fieldtype_handler_registry',
            'register',
            array(
                new Reference('netgen.ezforms.form.fieldtype_handler.eztext'),
            )
        );
    }

    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new FieldTypeHandlerRegistryPass());
    }
}
