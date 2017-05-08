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
use TeamNeusta\Magedev\Commands\Init\ComposerCommand;
use TeamNeusta\Magedev\Commands\Init\NpmCommand;
use TeamNeusta\Magedev\Commands\Init\PermissionsCommand;

/**
 * Class: InstallCommand
 *
 * @see AbstractCommand
 */
class InstallCommand extends AbstractCommand
{
    /**
     * configure
     */
    protected function configure()
    {
        $this->setName("magento:install");
        $this->setDescription("executes bin/magento setup:install inside container");

        $this->onExecute(function ($runtime) {
            $magentoVersion = $runtime->getConfig()->getMagentoVersion();
            $config = $runtime->getConfig();

            // install only used for magento2
            if ($magentoVersion == "2") {
                $domain = $config->get("domain");

                (new ComposerCommand())->executeCommand();
                (new NpmCommand())->executeCommand();
                (new PermissionsCommand())->executeCommand();

                $runtime->getDocker()->execute(
                    "bin/magento setup:install --db-host=mysql --db-name=magento --db-user=magento --db-password=magento --admin-user=admin --admin-password=admin123 --admin-email=admin@localhost.de --admin-firstname=admin --admin-lastname=admin --backend-frontname=admin --base-url=http://".$domain."/"
                );

                (new PermissionsCommand())->executeCommand();
                (new \TeamNeusta\Magedev\Commands\Config\ResetCommand())->executeCommand();
                (new \TeamNeusta\Magedev\Commands\Init\AddHostEntryCommand())->executeCommand();
            }
        });
    }
}
