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

use TeamNeusta\Magedev\Commands\AbstractCommand;

/**
 * Class: DumpCommand
 *
 * @see AbstractCommand
 */
class DumpCommand extends AbstractCommand
{
    /**
     * configure
     */
    protected function configure()
    {
        $this->setName("db:dump");
        $this->setDescription("dump db");

        $this->onExecute(function ($runtime) {
            $dumpFile = "dump.sql";
            $dbName = "magento";

            $runtime
                ->getDocker()
                ->execute("mysqldump ".$dbName." > ".$dumpFile);
        });
    }
}
