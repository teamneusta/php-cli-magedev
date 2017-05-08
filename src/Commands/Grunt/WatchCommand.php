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
 * Class: WatchCommand
 *
 * @see AbstractCommand
 */
class WatchCommand extends AbstractCommand
{
    /**
     * configure
     */
    protected function configure()
    {
        $this->setName("grunt:watch");
        $this->setDescription("runs watch inside container");

        $this->onExecute(function ($runtime) {
            $magentoVersion = $runtime->getConfig()->getMagentoVersion();
            if ($magentoVersion == "2") {
                $runtime->getDocker()->execute("/usr/local/bin/grunt watch");
            }
        });
    }
}
