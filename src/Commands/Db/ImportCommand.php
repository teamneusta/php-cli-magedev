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

namespace TeamNeusta\Magedev\Commands\Db;

use Symfony\Component\Console\Input\InputArgument;
use TeamNeusta\Magedev\Commands\AbstractCommand;

/**
 * Class: ImportCommand
 *
 * @see AbstractCommand
 */
class ImportCommand extends AbstractCommand
{
    /**
     * configure
     */
    protected function configure()
    {
        $this->setName("db:import");
        $this->setDescription("import db");
        $this->addArgument('dump_file', InputArgument::OPTIONAL, 'path to sql dump');

        $this->onExecute(function ($runtime) {
            $config = $runtime->getConfig();
            $fileHelper = $runtime->getHelper('FileHelper');

            if ($config->optionExists('dump_file')) {
                $dumpFile = $config->get('dump_file');

                // escape whitespaces for command
                $dumpFile = str_replace(" ", "\\ ", $dumpFile);

                if (!file_exists(getcwd() . $dumpFile)) {
                    // copy it to project folder
                    $runtime->getShell()->execute("cp ".$dumpFile." .");
                }

                $runtime
                    ->getDocker()
                    ->execute("mysql < ".basename($dumpFile));

                unlink(basename($dumpFile));
            }
        });
    }
}
