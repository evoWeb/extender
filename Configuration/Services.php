<?php

declare(strict_types=1);

namespace Evoweb\Extender;

use Evoweb\Extender\Utility\ClassLoader;
use Evoweb\Extender\Event\RegisterAutoloaderEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;

return static function (ContainerConfigurator $container, ContainerBuilder $containerBuilder) {
    $containerBuilder->addCompilerPass(new class() implements CompilerPassInterface {
        public function process(ContainerBuilder $container): void
        {
            $eventDispatcher = $container->findDefinition(EventDispatcherInterface::class);
            if ($eventDispatcher) {
                $event = new Definition(RegisterAutoloaderEvent::class);
                $event->setArguments([new Reference('service_container')]);
                $event->setShared(false);
                $eventDispatcher->addMethodCall('dispatch', [$event]);
            }
        }
    });
};
