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

namespace TeamNeusta\Magedev\Commands\Magento;

use TeamNeusta\Magedev\Commands\AbstractCommand;
use TeamNeusta\Magedev\Commands\Init\PermissionsCommand;

/**
 * Class: UpgradeCommand
 *
 * @see AbstractCommand
 */
class UpgradeCommand extends AbstractCommand
{
    /**
     * configure
     */
    protected function configure()
    {
        $this->setName("magento:upgrade");
        $this->setDescription("executes bin/magento setup:upgrade inside container");

        $this->onExecute(function ($runtime) {
            $magentoVersion = $runtime->getConfig()->getMagentoVersion();
            $magerunHelper = $runtime->getHelper('MagerunHelper');
            if ($magentoVersion == "1") {
                $magerunHelper->magerunCommand("sys:setup:run");
            }
            if ($magentoVersion == "2") {
                $runtime->getDocker()->execute("bin/magento setup:upgrade");
            }
            $this->executeSubcommand(new CacheCleanCommand());
            $this->executeSubcommand(new PermissionsCommand());
            $this->executeSubcommand(new RefreshCommand());
        });
    }
}
