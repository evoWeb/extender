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

namespace Evoweb\Extender\Command;

use Evoweb\Extender\Cache\CacheManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * CLI command for the 'extender' extension - clear cache
 */
class ClearCommand extends Command
{
    protected CacheManager $cacheManager;

    public function __construct(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
        parent::__construct();
    }

    /**
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $result = self::SUCCESS;
        try {
            $this->cacheManager->createCache('extender')->flush();
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
            $result = self::FAILURE;
        }
        return $result;
    }
}
