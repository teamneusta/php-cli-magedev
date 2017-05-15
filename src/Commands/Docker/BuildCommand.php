<?php
/**
 * This file is part of the teamneusta/php-cli-magedev package.
 *
 * Copyright (c) 2017 neusta GmbH | Ein team neusta Unternehmen
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 *
 * @license https://opensource.org/licenses/mit-license MIT License
 */

namespace TeamNeusta\Magedev\Commands\Docker;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class: BuildCommand
 *
 * @see AbstractCommand
 */
class BuildCommand extends Base
{
    /**
     * configure
     */
    protected function configure()
    {
        $this->setName("docker:build");
        $this->setDescription("build docker container");
    }

    /**
     * execute
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        // TODO: add argument force, with cache, or no cache
        $this->dockerService->getManager()->rebuildContainers();
        parent::execute($input, $output);
    }
}
