<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\EzFormsBundle\DependencyInjection\Compiler\FieldTypeHandlerRegistryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\DependencyInjection\Reference;

final class FieldTypeHandlerRegistryPassTest extends AbstractCompilerPassTestCase
{
    public function testCompilerPassCollectsValidServices(): void
    {
        $registry = new Definition();
        $this->setDefinition('netgen.ezforms.form.fieldtype_handler_registry', $registry);

        $handler = new Definition();
        $handler->addTag('netgen.ezforms.form.fieldtype_handler', ['alias' => 'eztext']);
        $this->setDefinition('netgen.ezforms.form.fieldtype_handler.eztext', $handler);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen.ezforms.form.fieldtype_handler_registry',
            'register',
            [
                'eztext',
                new Reference('netgen.ezforms.form.fieldtype_handler.eztext'),
            ]
        );
    }

    public function testCompilerPassMustThrowExceptionIfHandlerServiceDoesNotHaveAlias(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("'netgen.ezforms.form.fieldtype_handler' service tag needs an 'alias' attribute to identify the field type. None given.");

        $registry = new Definition();
        $this->setDefinition('netgen.ezforms.form.fieldtype_handler_registry', $registry);

        $handler = new Definition();
        $handler->addTag('netgen.ezforms.form.fieldtype_handler');
        $this->setDefinition('netgen.ezforms.form.fieldtype_handler.eztext', $handler);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen.ezforms.form.fieldtype_handler_registry',
            'register',
            [
                new Reference('netgen.ezforms.form.fieldtype_handler.eztext'),
            ]
        );
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new FieldTypeHandlerRegistryPass());
    }
}
