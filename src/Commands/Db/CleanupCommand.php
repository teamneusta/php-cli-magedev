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
 * Class: CleanupCommand
 *
 * @see AbstractCommand
 */
class CleanupCommand extends AbstractCommand
{
    /**
     * configure
     */
    protected function configure()
    {
        $this->setName("db:cleanup");
        $this->setDescription("clean db, remove customers, orders and so on");

        $this->onExecute(function ($runtime) {
            $script = "cleanup.sql";
            $fileHelper = $runtime->getHelper('FileHelper');

            if ($context->getMagentoVersion() == "1") {
                $scriptPath = $fileHelper->findPath("var/data/magento1/".$script);
            }
            if ($context->getMagentoVersion() == "2") {
                $scriptPath = $fileHelper->findPath("var/data/magento2/".$script);
            }

            $runtime->getShell()->execute("cp ".$scriptPath." .");
            $runtime
                ->getDocker()
                ->execute("mysql -f < ".basename($script));

            unlink($script);
        });
    }
}
