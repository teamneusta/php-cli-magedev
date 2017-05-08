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

namespace TeamNeusta\Magedev\Commands\Grunt;

use TeamNeusta\Magedev\Commands\AbstractCommand;

/**
 * Class: KillCommand
 *
 * @see AbstractCommand
 */
class KillCommand extends AbstractCommand
{
    /**
     * configure
     */
    protected function configure()
    {
        // register shutdown, because Ctrl+C is not forwarded
        // to the container, the process will remain active
        // as a workaround use grunt:kill

        $this->setName("grunt:kill");
        $this->setDescription("kills grunt inside container");

        $this->onExecute(function ($runtime) {
            $magentoVersion = $runtime->getConfig()->getMagentoVersion();
            if ($magentoVersion == "2") {
                // this doesn work, because it is
                // interpreted on the host:
                // ->bash("kill $(pidof grunt)");
                $runtime->getDocker()->execute("pkill -SIGKILL grunt");
            }
        });
    }
}
