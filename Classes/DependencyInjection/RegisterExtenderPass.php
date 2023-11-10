<?php

declare(strict_types=1);

/*
 * This file is part of the "extender" Extension for TYPO3 CMS.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Evoweb\Extender\DependencyInjection;

use Evoweb\Extender\Configuration\Register;
use Evoweb\Extender\Event\RegisterAutoloaderEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RegisterExtenderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $this->addRegisterAutoloadEventToEventDispatcher($container);
        $this->addExtendedClassesToRegisterDefinition($container);
    }

    protected function addRegisterAutoloadEventToEventDispatcher(ContainerBuilder $container): void
    {
        $registerAutoloaderEvent = $container->findDefinition(RegisterAutoloaderEvent::class);
        $eventDispatcher = $container->findDefinition(EventDispatcherInterface::class);
        $eventDispatcher->addMethodCall('dispatch', [$registerAutoloaderEvent]);
    }

    protected function addExtendedClassesToRegisterDefinition(ContainerBuilder $container): void
    {
        $extendedClasses = [];
        foreach ($container->findTaggedServiceIds('extender.extends', true) as $extendingClass => $tags) {
            foreach ($tags as $tag) {
                $extendedClass = $tag['class'] ?? '';
                if ($extendedClass === '') {
                    continue;
                }

                if (!isset($extendedClasses[$extendedClass])) {
                    $extendedClasses[$extendedClass] = [];
                }
                $extendedClasses[$extendedClass][] = $extendingClass;
            }
        }

        $registerDefinition = $container->getDefinition(Register::class);
        $registerDefinition->setArguments([$extendedClasses]);
    }
}
