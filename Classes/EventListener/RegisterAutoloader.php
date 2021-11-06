<?php

declare(strict_types=1);

namespace Evoweb\Extender\EventListener;

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

use Evoweb\Extender\Utility\ClassLoader;

class RegisterAutoloader
{
    /**
     * @var ClassLoader
     */
    protected $classLoader;

    protected static $registered = false;

    public function __construct(ClassLoader $classLoader)
    {
        $this->classLoader = $classLoader;
    }

    public function onClassLoaderEvent($event)
    {
        if (!self::$registered) {
            self::$registered = true;
            spl_autoload_register([$this->classLoader, 'loadClass'], true, true);
        }
    }
}