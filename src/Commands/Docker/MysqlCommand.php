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

use TeamNeusta\Magedev\Commands\AbstractCommand;

/**
 * Class: MysqlCommand
 *
 * @see AbstractCommand
 */
class MysqlCommand extends AbstractCommand
{
    /**
     * configure
     */
    protected function configure()
    {
        $this->setName("docker:cli:mysql");
        $this->setDescription("drop a mysql shell");

        $this->onExecute(function ($runtime) {
            $runtime->getDocker()->execute("mysql");
        });
    }
}
