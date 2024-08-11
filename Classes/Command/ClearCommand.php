<?php

declare(strict_types=1);

/*
 * This file is developed by evoWeb.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Evoweb\Extender\Command;

use Evoweb\Extender\Cache\CacheFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * CLI command for the 'extender' extension - clear cache
 */
class ClearCommand extends Command
{
    public function __construct(protected CacheFactory $cacheFactory)
    {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = self::SUCCESS;
        try {
            $this->cacheFactory->createCache('extender')->flush();
            $output->writeln('<info>Cache cleared</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            $result = self::FAILURE;
        }
        return $result;
    }
}
