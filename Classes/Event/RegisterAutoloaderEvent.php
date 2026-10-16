<?php

declare(strict_types=1);

/*
 * This file is developed by evoWeb.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Evoweb\Extender\Event;

use Evoweb\Extender\Loader\ClassLoader;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\StoppableEventInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[Autoconfigure(shared: false)]
class RegisterAutoloaderEvent implements StoppableEventInterface
{
    public function __construct(
        #[Autowire(service: 'service_container')]
        ContainerInterface $container
    ) {
        try {
            /** @var ClassLoader $classLoader */
            $classLoader = $container->get(ClassLoader::class);
            $autoloader = [$classLoader, 'loadClass'];
            if ($this->autoloaderAlreadyRegistered($autoloader)) {
                $this->unregisterAutoloader($autoloader);
            }
            /** @var callable(string): void $autoloader */
            spl_autoload_register($autoloader, true, true);
        } catch (ContainerExceptionInterface) {
        }
    }

    /**
     * @param array{0: ClassLoader, 1: string} $autoloader
     */
    protected function autoloaderAlreadyRegistered(array $autoloader): bool
    {
        $result = false;

        $autoloaderClass = get_class($autoloader[0]);
        $currentAutoLoaders = spl_autoload_functions();
        foreach ($currentAutoLoaders as $currentAutoLoader) {
            if (
                is_array($currentAutoLoader)
                && (
                    (is_object($currentAutoLoader[0]) && get_class($currentAutoLoader[0]) === $autoloaderClass)
                    || (is_string($currentAutoLoader[0]) && $currentAutoLoader[0] === $autoloaderClass)
                )
            ) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * @param array{0: ClassLoader, 1: string} $autoloader
     */
    protected function unregisterAutoloader(array $autoloader): void
    {
        assert(is_callable($autoloader));
        spl_autoload_unregister($autoloader);
    }

    public function isPropagationStopped(): bool
    {
        return true;
    }
}
