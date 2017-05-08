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

namespace TeamNeusta\Magedev\Commands;

use Herrera\Phar\Update\Manager;
use Herrera\Phar\Update\Manifest;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class: UpdateCommand
 *
 * @see Command
 */
class UpdateCommand extends Command
{
    const MANIFEST_FILE = 'https://teamneusta.github.io/php-cli-magedev/manifest.json';

    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('self-update')
            ->setAliases(['selfupdate'])
            ->setDescription('Updates magedev.phar to the latest version')
        ;
    }

    /**
     * execute
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = new Manager(Manifest::loadFile(self::MANIFEST_FILE));
        $manager->update($this->getApplication()->getVersion(), true);
    }
}
