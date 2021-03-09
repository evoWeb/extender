<?php

declare(strict_types=1);

namespace Evoweb\Extender\Command;

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

use Evoweb\Extender\Utility\ClassCacheManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * CLI command for the 'extender' extension - rebuild
 */
class ExtenderRebuildCommand extends Command
{
    protected ?ClassCacheManager $classCacheManager;

    public function __construct(ClassCacheManager $classCacheManager = null)
    {
        $this->classCacheManager = $classCacheManager;
        parent::__construct(null);
    }

    /**
     * Configure the command by defining the name, options and arguments
     */
    public function configure()
    {
        $this->setDescription('Rebuilds the extender class cache');
    }

    /**
     * Execute rebuild
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->classCacheManager->reBuild();
    }
}
