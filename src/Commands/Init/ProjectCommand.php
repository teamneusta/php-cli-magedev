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

namespace TeamNeusta\Magedev\Commands\Init;

use TeamNeusta\Magedev\Commands\AbstractCommand;

/**
 * Class: ProjectCommand
 *
 * @see AbstractCommand
 */
class ProjectCommand extends AbstractCommand
{
    /**
     * configure
     */
    protected function configure()
    {
        $this->setName("init:project");
        $this->setDescription("setup project");
        $this->onExecute(function ($runtime) {
            $config = $runtime->getConfig();

            $this->executeSubcommand(new PermissionsCommand());
            $this->executeSubcommand(new ComposerCommand());
            $this->executeSubcommand(new NpmCommand());
            $this->executeSubcommand(new PermissionsCommand());
            $this->executeSubcommand(new \TeamNeusta\Magedev\Commands\Magento\InstallMagerunCommand());
            $this->executeSubcommand(new \TeamNeusta\Magedev\Commands\Magento\AlignConfigCommand());
            $this->executeSubcommand(new \TeamNeusta\Magedev\Commands\Db\ImportCommand());
            $this->executeSubcommand(new \TeamNeusta\Magedev\Commands\Media\ImportCommand());
            $this->executeSubcommand(new \TeamNeusta\Magedev\Commands\Magento\SetBaseUrlCommand());
            $this->executeSubcommand(new \TeamNeusta\Magedev\Commands\Magento\UpgradeCommand());
            $this->executeSubcommand(new \TeamNeusta\Magedev\Commands\Magento\DefaultAdminUserCommand());
            $this->executeSubcommand(new \TeamNeusta\Magedev\Commands\Magento\DefaultCustomerCommand());
            $this->executeSubcommand(new PermissionsCommand());
            $this->executeSubcommand(new \TeamNeusta\Magedev\Commands\Config\ResetCommand());
            $this->executeSubcommand(new \TeamNeusta\Magedev\Commands\Magento\RefreshCommand());
            $this->executeSubcommand(new \TeamNeusta\Magedev\Commands\Magento\CacheCleanCommand());
            $this->executeSubcommand(new \TeamNeusta\Magedev\Commands\Magento\ReindexCommand());
            $this->executeSubcommand(new AddHostEntryCommand());

            $runtime->getOutput()->writeln("project installed");

            if ($config->optionExists("domain")) {
                $domain = $config->get("domain");
                $runtime->getOutput()->writeln("visit: http://".$domain);
            }
        });
    }
}
