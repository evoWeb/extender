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

namespace Evoweb\Extender\Event;

use Evoweb\Extender\Loader\ClassLoader;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\StoppableEventInterface;

class RegisterAutoloaderEvent implements StoppableEventInterface
{
    public function __construct(ContainerInterface $container)
    {
        try {
            $autoloader = [$container->get(ClassLoader::class), 'loadClass'];
            if ($this->autoloaderAlreadyRegistered($autoloader)) {
                $this->unregisterAutoloader($autoloader);
            }
            spl_autoload_register($autoloader, true, true);
        } catch (ContainerExceptionInterface $e) {}
    }

    protected function autoloaderAlreadyRegistered(array $autoloader): bool
    {
        $result = false;

        $autoloaderClass = get_class($autoloader[0]);
        $currentAutoLoaders = spl_autoload_functions();
        foreach ($currentAutoLoaders as $currentAutoLoader) {
            if (
                is_array($currentAutoLoader)
                && ((is_object($currentAutoLoader[0]) && get_class($currentAutoLoader[0]) === $autoloaderClass) ||
                    (is_string($currentAutoLoader[0]) && $currentAutoLoader[0] === $autoloaderClass))
            ) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    protected function unregisterAutoloader(array $autoloader): void
    {
        spl_autoload_unregister($autoloader);
    }

    public function isPropagationStopped(): bool
    {
        return true;
    }
}
